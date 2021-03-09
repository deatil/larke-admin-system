<?php

namespace Larke\JWT\Contracts;

/**
 * Key
 */
interface Key
{
    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getPassphrase();
}
