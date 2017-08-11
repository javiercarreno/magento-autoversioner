<?php


namespace Autoversioner\Config;

class JiraCredentials
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
     * @var string
     */
    private $url;

    /**
     * @param String[] $config
     *
     */
    public function __construct(array $config)
    {
        $this->userName = $config['user'];
        $this->token = $config['token'];
        $this->url = $config['url'];
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

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
