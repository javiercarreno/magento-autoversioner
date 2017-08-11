<?php


namespace Autoversioner\Config;

class GitHubCredentials
{
    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $token;
    /**
     * @param array $github
     *
     */
    public function __construct(array $config)
    {
        $this->userName = $config['username'];
        $this->token = $config['token'];
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
