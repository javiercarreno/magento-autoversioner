<?php

namespace Autoversioner\Config;

class Credentials
{
    /**
     * @var mixed
     */
    private $config;

    /**
     *
     */
    public function __construct(YamlParser $parser)
    {
        $this->config = $parser->getParsedYaml();
    }

    public function getGitHubCredentials()
    {
        return new GitHubCredentials($this->config['github']);
    }

    /**
     * @return JiraCredentials
     */
    public function getJiraCredentials()
    {
        return new JiraCredentials($this->config['jira']);
    }
}
