<?php

namespace Larke\JWT\Signer\Hmac;

use Larke\JWT\Signer\Hmac;

/**
 * Signer for HMAC SHA-256
 */
class Sha256 extends Hmac
{
    /**
     * {@inheritdoc}
     */
    public function getAlgorithmId()
    {
        return 'HS256';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlgorithm()
    {
        return 'sha256';
    }
}
