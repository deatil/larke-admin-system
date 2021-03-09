<?php

namespace Larke\JWT\Contracts;

use JsonSerializable;

/**
 * Basic interface for token claims
 */
interface Claim extends JsonSerializable
{
    /**
     * Returns the claim name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the claim value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the string representation of the claim
     *
     * @return string
     */
    public function __toString();
}
