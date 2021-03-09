<?php

namespace Larke\JWT\Signer\Key;

use Larke\JWT\Contracts\Key;
use Larke\JWT\Exception\FileCouldNotBeRead;

final class LocalFileReference implements Key
{
    private const PATH_PREFIX = 'file://';

    private $path;
    private $passphrase;

    private function __construct(string $path, string $passphrase)
    {
        $this->path = $path;
        $this->passphrase = $passphrase;
    }

    /** 
     * @throws FileCouldNotBeRead 
     */
    public static function file(string $path, string $passphrase = ''): self
    {
        if (strpos($path, self::PATH_PREFIX) === 0) {
            $path = substr($path, 7);
        }

        if (! file_exists($path)) {
            throw FileCouldNotBeRead::onPath($path);
        }

        return new self($path, $passphrase);
    }

    public function getContent(): string
    {
        return self::PATH_PREFIX . $this->path;
    }

    public function getPassphrase(): string
    {
        return $this->passphrase;
    }
}
