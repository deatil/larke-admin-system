<?php

namespace Larke\JWT\Signer;

use SodiumException;

use Larke\JWT\Exception\InvalidKeyProvided;
use Larke\JWT\Contracts\Key;

use function sodium_crypto_sign_detached;
use function sodium_crypto_sign_verify_detached;

/**
 * EDDSA signers
 */
class Eddsa extends BaseSigner
{
    public function getAlgorithmId()
    {
        return 'EdDSA';
    }
    
    public function createHash($payload, Key $key)
    {
        try {
            return sodium_crypto_sign_detached($payload, $key->getContent());
        } catch (SodiumException $sodiumException) {
            throw new InvalidKeyProvided($sodiumException->getMessage(), 0, $sodiumException);
        }
    }

    public function doVerify($expected, $payload, Key $key)
    {
        try {
            return sodium_crypto_sign_verify_detached($expected, $payload, $key->getContent());
        } catch (SodiumException $sodiumException) {
            throw new InvalidKeyProvided($sodiumException->getMessage(), 0, $sodiumException);
        }
    }
}