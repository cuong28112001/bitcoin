<?php

namespace Duplicator\Package\Create\Scan;

use Duplicator\Libs\Snap\SnapIO;

class ScanNodeInfo
{
    const TYPE_UNKNOWN   = 0;
    const TYPE_FILE      = 1;
    const TYPE_DIR       = 2;
    const TYPE_LINK_FILE = 3;
    const TYPE_LINK_DIR  = 4;

    /** @var string */
    protected $path = '';
    /** @var int in bytes */
    protected $size = 0;
    /** @var int */
    protected $nodes = 1;
    /** @var int ENUM  self::TYPE_* */
    protected $type = self::TYPE_UNKNOWN;
    /** @var bool */
    protected $isCyclicLink = false;

    /**
     * Class constructor
     *
     * @param string $path Path
     */
    public function __construct($path)
    {
        $this->path = untrailingslashit(wp_normalize_path($path));
        if (is_link($path)) {
            if (is_dir($path)) {
                $this->type = self::TYPE_LINK_DIR;
            } else {
                $this->type = self::TYPE_LINK_FILE;
            }
        } elseif (is_file($path)) {
            $this->type = self::TYPE_FILE;
            $this->size = (int) @filesize($path);
        } elseif (is_dir($path)) {
            $this->type = self::TYPE_DIR;
        } else {
            $this->type  = self::TYPE_UNKNOWN;
            $this->nodes = 0;
        }
    }

    /**
     * Add child from node
     *
     * @param ScanNodeInfo $node Node to add
     *
     * @return void
     */
    public function addChildFromNode(ScanNodeInfo $node)
    {
        if (!$this->isDir()) {
            return;
        }
        $this->size  += $node->getSize();
        $this->nodes += $node->getNodes();
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get nodes
     *
     * @return int
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Get type
     *
     * @return int enum self::TYPE_*
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is dir
     *
     * @return bool
     */
    public function isDir()
    {
        return $this->type === self::TYPE_DIR || $this->type === self::TYPE_LINK_DIR;
    }

    /**
     * Is link
     *
     * @return bool
     */
    public function isLink()
    {
        return $this->type === self::TYPE_LINK_DIR || $this->type === self::TYPE_LINK_FILE;
    }

    /**
     * Get target symlink path
     * If is not a symlink return false
     *
     * @return false|string
     */
    public function getLinkTarget()
    {

        if (!$this->isLink()) {
            return false;
        }

        return SnapIO::readlinkReal($this->path);
    }

    /**
     * Is recursive link
     *
     * @return bool
     */
    public function isCyclicLink()
    {
        return $this->isCyclicLink;
    }

    /**
     * Set is cyclic link
     *
     * @param bool $isCyclicLink Is cyclic link
     *
     * @return void
     */
    public function setIsCycleLink($isCyclicLink)
    {
        $this->isCyclicLink = ($this->type === self::TYPE_LINK_DIR && $isCyclicLink);
    }

    /**
     * Is unreadable
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_readable($this->path);
    }
}
