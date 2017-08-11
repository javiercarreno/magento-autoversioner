<?php


namespace Autoversioner\Helpers;

class GitHubRepository
{
    /**
     * @var string
     */
    private $userName;

    /**
     * @var string;
     */
    private $repository;

    /**
     * @param string $gitHubRepositoryDefinition
     */
    public function __construct($gitHubRepositoryDefinition)
    {
        if ($gitHubRepositoryDefinition!="") {
            $definitions = explode(":", $gitHubRepositoryDefinition);
            $definitions = explode("/", $definitions[1]);

            $this->userName = $definitions[0];
            $this->repository = str_replace('.git', '', $definitions[1]);
        }
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
    public function getRepository()
    {
        return $this->repository;
    }
}
