<?php

namespace VendorDuplicator\Aws\S3;

use VendorDuplicator\Aws\CommandInterface;
use VendorDuplicator\Aws\ResultInterface;
use VendorDuplicator\Psr\Http\Message\RequestInterface;
/**
 * Injects ObjectURL into the result of the PutObject operation.
 *
 * @internal
 */
class PutObjectUrlMiddleware
{
    /** @var callable  */
    private $nextHandler;
    /**
     * Create a middleware wrapper function.
     *
     * @return callable
     */
    public static function wrap()
    {
        return function (callable $handler) {
            return new self($handler);
        };
    }
    /**
     * @param callable $nextHandler Next handler to invoke.
     */
    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }
    public function __invoke(CommandInterface $command, $request = null)
    {
        $next = $this->nextHandler;
        return $next($command, $request)->then(function (ResultInterface $result) use ($command) {
            $name = $command->getName();
            switch ($name) {
                case 'PutObject':
                case 'CopyObject':
                    $result['ObjectURL'] = isset($result['@metadata']['effectiveUri']) ? $result['@metadata']['effectiveUri'] : null;
                    break;
                case 'CompleteMultipartUpload':
                    $result['ObjectURL'] = $result['Location'];
                    break;
            }
            return $result;
        });
    }
}
