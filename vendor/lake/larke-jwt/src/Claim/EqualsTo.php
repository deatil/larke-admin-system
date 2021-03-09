<?php

namespace Larke\JWT\Claim;

use Larke\JWT\Contracts\Claim;
use Larke\JWT\ValidationData;

/**
 * Validatable claim that checks if value is strictly equals to the given data
 */
class EqualsTo extends Basic implements Claim, Validatable
{
    /**
     * {@inheritdoc}
     */
    public function validate(ValidationData $data)
    {
        if ($data->has($this->getName())) {
            return $this->getValue() === $data->get($this->getName());
        }

        return true;
    }
}
