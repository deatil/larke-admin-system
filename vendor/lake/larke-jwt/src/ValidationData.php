<?php

namespace Larke\JWT;

/**
 * Class that wraps validation values
 */
class ValidationData
{
    /**
     * The list of things to be validated
     *
     * @var array
     */
    private $items;

    /**
     * The leeway (in seconds) to use when validating time claims
     * @var int
     */
    private $leeway;

    /**
     * Initializes the object
     *
     * @param int $currentTime
     * @param int $leeway
     */
    public function __construct($currentTime = null, $leeway = 0)
    {
        $currentTime  = $currentTime ?: time();
        $this->leeway = (int) $leeway;

        $this->items = [
            'jti' => null,
            'iss' => null,
            'aud' => null,
            'sub' => null
        ];

        $this->currentTime($currentTime);
    }

    /**
     * Configures the id
     *
     * @param string $id
     */
    public function identifiedBy($id)
    {
        $this->items['jti'] = (string) $id;
    }

    /**
     * Configures the issuer
     *
     * @param string $issuer
     */
    public function issuedBy($issuer)
    {
        $this->items['iss'] = (string) $issuer;
    }

    /**
     * Configures the audience
     *
     * @param string $audience
     */
    public function permittedFor($audience)
    {
        $this->items['aud'] = (string) $audience;
    }

    /**
     * Configures the subject
     *
     * @param string $subject
     */
    public function relatedTo($subject)
    {
        $this->items['sub'] = (string) $subject;
    }

    /**
     * Configures the time that "iat", "nbf" and "exp" should be based on
     *
     * @param int $currentTime
     */
    public function currentTime($currentTime)
    {
        $currentTime  = (int) $currentTime;

        $this->items['iat'] = $currentTime + $this->leeway;
        $this->items['nbf'] = $currentTime + $this->leeway;
        $this->items['exp'] = $currentTime - $this->leeway;
    }

    /**
     * Returns the requested item
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Returns if the item is present
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name)
    {
        return !empty($this->items[$name]);
    }
}
