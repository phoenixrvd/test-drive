<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Helper;

class ConfigHelper
{
    public function get(string $key, $default = null)
    {
        return getenv($key) ? getenv($key) : $default;
    }
}