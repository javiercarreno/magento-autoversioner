<?php

namespace Autoversioner\Core;

use Autoversioner\Config\Config;

class Application
{
    /**
     * @var string
     */
    private $gitHubRepository;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param string $gitHubRepository
     */
    public function __construct($gitHubRepository)
    {
        $this->config = new Config();
        $this->gitHubRepository = $gitHubRepository;
    }

    public function Run()
    {

    }
}