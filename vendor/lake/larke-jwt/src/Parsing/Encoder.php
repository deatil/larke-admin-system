<?php

namespace Larke\JWT\Parsing;

use RuntimeException;

/**
 * Class that encodes data according with the specs of RFC-4648
 *
 * @link http://tools.ietf.org/html/rfc4648#section-5
 */
class Encoder
{
    /**
     * Encodes to JSON, validating the errors
     *
     * @param mixed $data
     * @return string
     *
     * @throws RuntimeException When something goes wrong while encoding
     */
    public function jsonEncode($data)
    {
        $json = json_encode($data);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new RuntimeException('Error while encoding to JSON: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * Encodes to base64url
     *
     * @param string $data
     * @return string
     */
    public function base64UrlEncode($data)
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }
}
