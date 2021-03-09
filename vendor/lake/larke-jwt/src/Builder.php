<?php

namespace Larke\JWT;

use Larke\JWT\Claim\Factory as ClaimFactory;
use Larke\JWT\Parsing\Encoder;
use Larke\JWT\Contracts\Signer;
use Larke\JWT\Contracts\Key;

use function implode;

/**
 * This class makes easier the token creation process
 */
class Builder
{
    /**
     * The token header
     *
     * @var array
     */
    private $headers = [
        'typ'=> 'JWT', 
        'alg' => 'none'
    ];

    /**
     * The token claim set
     *
     * @var array
     */
    private $claims = [];

    /**
     * The data encoder
     *
     * @var Encoder
     */
    private $encoder;

    /**
     * The factory of claims
     *
     * @var ClaimFactory
     */
    private $claimFactory;

    /**
     * @var Signer|null
     */
    private $signer;

    /**
     * @var Key|null
     */
    private $key;

    /**
     * Initializes a new builder
     *
     * @param Encoder $encoder
     * @param ClaimFactory $claimFactory
     */
    public function __construct(
        Encoder $encoder = null,
        ClaimFactory $claimFactory = null
    ) {
        $this->encoder = $encoder ?: new Encoder();
        $this->claimFactory = $claimFactory ?: new ClaimFactory();
    }

    /**
     * Configures the audience
     *
     * @param string $audience
     * @param bool $replicateAsHeader
     *
     * @return Builder
     */
    public function permittedFor($audience, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('aud', (string) $audience, $replicateAsHeader);
    }

    /**
     * Configures the expiration time, expirTime
     *
     * @param int $expiration
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function expiresAt($expiration, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('exp', (int) $expiration, $replicateAsHeader);
    }

    /**
     * Configures the token id JwtId
     *
     * @param string $id
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function identifiedBy($id, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('jti', (string) $id, $replicateAsHeader);
    }

    /**
     * Configures the time that the token was issued
     *
     * @param int $issuedAt
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function issuedAt($issuedAt, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('iat', (int) $issuedAt, $replicateAsHeader);
    }

    /**
     * Configures the issuer
     *
     * @param string $issuer
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function issuedBy($issuer, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('iss', (string) $issuer, $replicateAsHeader);
    }

    /**
     * Configures the time before which the token cannot be accepted
     *
     * @param int $notBefore
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function canOnlyBeUsedAfter($notBefore, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('nbf', (int) $notBefore, $replicateAsHeader);
    }

    /**
     * Configures the subject
     *
     * @param string $subject
     * @param boolean $replicateAsHeader
     *
     * @return Builder
     */
    public function relatedTo($subject, $replicateAsHeader = false)
    {
        return $this->withRegisteredClaim('sub', (string) $subject, $replicateAsHeader);
    }

    /**
     * Configures a registered claim
     *
     * @param string $name
     * @param mixed $value
     * @param boolean $replicate
     *
     * @return Builder
     */
    protected function withRegisteredClaim($name, $value, $replicate)
    {
        $this->withClaim($name, $value);

        if ($replicate) {
            $this->headers[$name] = $this->claims[$name];
        }

        return $this;
    }

    /**
     * Configures a header item
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Builder
     */
    public function withHeader($name, $value)
    {
        $this->headers[(string) $name] = $this->claimFactory->create($name, $value);

        return $this;
    }

    /**
     * Configures a claim item
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Builder
     */
    public function withClaim($name, $value)
    {
        $this->claims[(string) $name] = $this->claimFactory->create($name, $value);

        return $this;
    }

    /**
     * Returns the resultant token
     *
     * @return Token
     */
    public function getToken(Signer $signer = null, Key $key = null)
    {
        $signer = $signer ?: $this->signer;
        $key = $key ?: $this->key;

        if ($signer instanceof Signer) {
            $signer->modifyHeader($this->headers);
        }

        $payload = [
            $this->encoder->base64UrlEncode($this->encoder->jsonEncode($this->headers)),
            $this->encoder->base64UrlEncode($this->encoder->jsonEncode($this->claims))
        ];

        $signature = $this->createSignature($payload, $signer, $key);

        if ($signature !== null) {
            $payload[] = $this->encoder->base64UrlEncode($signature);
        }

        return new Token($this->headers, $this->claims, $signature, $payload);
    }

    /**
     * @param string[] $payload
     *
     * @return Signature|null
     */
    private function createSignature(array $payload, Signer $signer = null, Key $key = null)
    {
        if ($signer === null || $key === null) {
            return null;
        }

        return $signer->sign(implode('.', $payload), $key);
    }
}
