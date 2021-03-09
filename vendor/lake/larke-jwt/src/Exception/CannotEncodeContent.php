<?php

namespace Larke\JWT\Exception;

use JsonException;
use RuntimeException;

final class CannotEncodeContent 
    extends RuntimeException 
    implements Exception
{
    public static function jsonIssues(JsonException $previous): self
    {
        return new self('Error while encoding to JSON', 0, $previous);
    }
}
