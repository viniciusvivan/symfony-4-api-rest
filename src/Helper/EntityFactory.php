<?php

namespace App\Helper;

/**
 * Interface EntityFactory
 * @package App\Helper
 */
interface EntityFactory
{
    /**
     * @param string $json
     * @return mixed
     */
    public function createEntity(string $json);
}