<?php

namespace Larke\JWT\Exception;

use Throwable;
use InvalidArgumentException;

final class FileCouldNotBeRead 
    extends InvalidArgumentException 
    implements Exception
{
    public static function onPath(string $path, ?Throwable $cause = null): self
    {
        return new self(
            'The path "' . $path . '" does not contain a valid key file',
            0,
            $cause
        );
    }
}
