<?php

namespace Autoversioner\Config;

use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var mixed
     */
    private $config;

    /**
     *
     */
    public function __construct()
    {
        $this->config = Yaml::parse(file_get_contents('res/config.yml'));
    }
}