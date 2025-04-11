<?php

namespace Duplicator\Package\Create\Scan;

use DUP_PRO_Archive;
use DUP_PRO_Log;
use DUP_PRO_Package;
use Duplicator\Libs\Chunking\ChunkingManager;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapWP;
use Exception;

/**
 * ScanChunker
 */
class ScanChunker extends ChunkingManager
{
    /** @var ScanNodeInfo[] */
    protected $position = [];
    /** @var DUP_PRO_Package */
    protected $package = null;
    /** @var string[] */
    protected $pathsToScan = [];
    /** @var ArchiveNodesList */
    protected $dirList = null;
    /** @var ArchiveNodesList */
    protected $fileList = null;
    /** @var ScanOptions */
    protected $scanOpts = null;
    /** @var ScanResult */
    protected $scanResult = null;

    /**
     * Class contructor
     *
     * @template T of array{package:DUP_PRO_Package,pathsToScan:string[],dirList:ArchiveNodesList,fileList:ArchiveNodesList,scanOpts:ScanOptions}
     *
     * @param T   $extraData    extra data for manager used on extended classes
     * @param int $maxIteration max number of iterations, 0 for no limit
     * @param int $timeOut      timeout in microseconds, 0 for no timeout
     * @param int $throttling   throttling microseconds, 0 for no throttling
     *
     * @return void
     */
    public function __construct($extraData, $maxIteration = 0, $timeOut = 0, $throttling = 0)
    {
        if (!isset($extraData['package']) || !($extraData['package'] instanceof DUP_PRO_Package)) {
            throw new Exception('Package object not set or set incorrectly.');
        }
        $this->package = $extraData['package'];

        if (!isset($extraData['pathsToScan'])) {
            throw new Exception('Paths to scan not set');
        }
        $this->pathsToScan = $extraData['pathsToScan'];

        if (!isset($extraData['dirList']) || !($extraData['dirList'] instanceof ArchiveNodesList)) {
            throw new Exception('DirList object not set or set incorrectly.');
        }
        $this->dirList = $extraData['dirList'];

        if (!isset($extraData['fileList']) || !($extraData['fileList'] instanceof ArchiveNodesList)) {
            throw new Exception('FileList object not set or set incorrectly.');
        }
        $this->fileList = $extraData['fileList'];

        if (isset($extraData['scanOpts']) && $extraData['scanOpts'] instanceof ScanOptions) {
            $this->scanOpts = $extraData['scanOpts'];
        } else {
            $this->scanOpts = new ScanOptions();
        }

        $this->scanResult = new ScanResult();

        parent::__construct($extraData, $maxIteration, $timeOut, $throttling);
    }

    /**
     * Rewind scan
     *
     * @return void
     */
    protected function rewind()
    {
        $this->dirList->emptyFile();
        $this->fileList->emptyFile();
        $this->scanResult->reset();
        parent::rewind();
    }

    /**
     * Exec action on current position
     *
     * @param string       $key     Current Nodes path
     * @param ScanNodeInfo $current Current Node Info
     *
     * @return bool return true on success, false on failure
     */
    protected function action($key, $current)
    {
        try {
            switch ($current->getType()) {
                case ScanNodeInfo::TYPE_DIR:
                case ScanNodeInfo::TYPE_LINK_DIR:
                    if (!$current->isReadable()) {
                        $this->scanResult->unreadableDirs[] = $current->getPath();
                        return true;
                    }

                    if ($current->isCyclicLink()) {
                        $this->scanResult->recursiveLinks[] = $current->getPath();
                        return true;
                    }

                    $this->addToDirList($current);
                    break;
                case ScanNodeInfo::TYPE_LINK_FILE:
                case ScanNodeInfo::TYPE_FILE:
                    if (!$current->isReadable()) {
                        $this->scanResult->unreadableFiles[] = $current->getPath();
                        return true;
                    }
                    $this->addToFileList($current);
                    break;
                case ScanNodeInfo::TYPE_UNKNOWN:
                default:
                    break;
            }
        } catch (Exception $e) {
            DUP_PRO_Log::error("Error while scanning: {$key} - {$e->getMessage()}");
            return false;
        }

        return true;
    }

    /**
     * Check if file must be added to warning list
     *
     * @param ScanNodeInfo $node node info
     *
     * @return void
     */
    protected function addToFileList(ScanNodeInfo $node)
    {
        $filePath     = $node->getPath();
        $relativePath = SnapIO::getRelativePath($filePath, $this->scanOpts->rootPath);
        $fileSize     = $node->getSize();

        if (!$this->scanOpts->skipSizeWarning && $fileSize > DUPLICATOR_PRO_SCAN_WARN_FILE_SIZE) {
            $this->scanResult->bigFiles[] = [
                'path'         => $filePath,
                'relativePath' => $relativePath,
                'size'         => $fileSize,
            ];
        }

        $this->scanResult->fileCount++;
        $this->scanResult->size += $fileSize;

        $this->fileList->addEntry($relativePath, $fileSize, 1);
    }

    /**
     * Check if dir must be added to warning list
     *
     * @param ScanNodeInfo $node node info
     *
     * @return void
     */
    protected function addToDirList($node)
    {
        $dirPath      = $node->getPath();
        $relativePath = SnapIO::getRelativePath($dirPath, $this->scanOpts->rootPath);
        $size         = $node->getSize();
        $nodes        = $node->getNodes();

        // is relative path is empty is the root path
        if ($relativePath !== '' && !$this->scanOpts->skipSizeWarning) {
            if ($size > DUPLICATOR_PRO_SCAN_WARN_DIR_SIZE) {
                $this->scanResult->bigDirs[] = [
                    'path'         => $dirPath,
                    'relativePath' => $relativePath,
                    'size'         => $size,
                    'nodes'        => $nodes,
                ];
            }
        }

        // Check for other WordPress installs
        if (!DUP_PRO_Archive::isCurrentWordpressInstallPath($dirPath) && SnapWP::isWpHomeFolder($dirPath)) {
            $this->scanResult->addonSites[] = $dirPath;
        }

        $this->scanResult->dirCount++;
        $this->dirList->addEntry($relativePath, $size, $nodes);
    }

    /**
     * Get scan result of chunk
     *
     * @return ScanResult
     */
    public function getScanResult()
    {
        return $this->scanResult;
    }

    /**
     * Return iterator
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return ScanIterator
     */
    protected function getIterator($extraData = null)
    {
        $filters = [
            'paths'   => $this->scanOpts->filterDirs,
            'regexes' => [],
        ];

        $filterFilePaths  = array_filter($this->scanOpts->filterFiles, function ($path) {
            return strpos($path, '/') !== false;
        });
        $filters['paths'] = array_unique(array_merge($filters['paths'], $filterFilePaths));

        $filterFileNames = array_diff($this->scanOpts->filterFiles, $filterFilePaths);
        if (!empty($filterFileNames)) {
            $filters['regexes'][] = '/(' . implode('|', $filterFileNames) . ')/';
        }

        if ($this->scanOpts->filterBadEncoding) {
            $filters['regexes'][] = '/([\/\*\?><\:\\\\\|]|[^\x20-\x7f])/';
        }

        if (!empty($this->scanOpts->filterFileExtensions)) {
            $filters['regexes'][] = '/\.(' . implode('|', $this->scanOpts->filterFileExtensions) . ')$/i';
        }

        return new ScanIterator(
            $this->pathsToScan,
            $filters,
            $this->scanOpts->sort
        );
    }

    /**
     * Return persistance adapter
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return ScanPersistanceAdapter
     */
    protected function getPersistance($extraData = null)
    {
        return new ScanPersistanceAdapter(
            $this->package->getNameHash(),
            $this->scanResult
        );
    }

    /**
     * Desctructor
     *
     * @return void
     */
    public function __destruct()
    {
        $this->dirList->close();
        $this->fileList->close();
    }
}
