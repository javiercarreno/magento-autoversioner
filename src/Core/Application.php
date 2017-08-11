<?php

namespace Autoversioner\Core;

use Autoversioner\Config\Config;
use Autoversioner\Config\Credentials;
use Autoversioner\Config\YamlParser;
use Autoversioner\Helpers\ErrorHandler;
use Autoversioner\Helpers\GitHubHelper;
use Autoversioner\Helpers\GitHubRepository;
use Autoversioner\Helpers\JiraHelper;
use \Github\Client;
use PhpCsFixer\Error\Error;

class Application
{
    /**
     * @var Credentials
     */
    private $credentials;
    /**
     * @var Client
     */
    private $client;

    /**
     * @var GitHubHelper
     */
    private $gitHubHelper;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param string $gitHubRepository
     */
    public function __construct($gitHubRepository)
    {
        $this->config = new Config(new YamlParser('config.yml'));
        $credentials = new Credentials(new YamlParser('credentials.yml'));
        $this->gitHubHelper = new GitHubHelper(
            new GitHubRepository($gitHubRepository),
            $credentials->getGitHubCredentials(),
            new Client()
        );
        $this->jiraHelper = new JiraHelper(
            $credentials->getJiraCredentials(),
            $this->config->getExpressions()
            );
    }

    /**
     *
     */
    public function Run()
    {
        try {
            $this->doVersionCalculation();
        } catch (\Exception $ex) {
            ErrorHandler::HandleError(new \Exception("Fatal error resolving version. Possible messy github repository (no tags, no pull requests...)."));
        }
    }

    /**
     * @return array
     */
    private function getPullRequestsByLastTag($lastTag)
    {
        try {
            if ($lastTag) {
                $dateLastTag = '';
                if ($lastTag['object']['type']=='commit') {
                    $tagCommit = $this->gitHubHelper->getCommit($lastTag['object']['sha']);
                    $dateLastTag = $tagCommit['commit']['author']['date'];
                } elseif ($lastTag['object']['type']=='tag') {
                    $tag = $this->gitHubHelper->getTag($lastTag['object']['sha']);
                    $dateLastTag = $tag['tagger']['date'];
                }
                $pullRequests = $this->gitHubHelper->getPullRequests($dateLastTag, $lastTag['object']['sha']);
            } else {
                $pullRequests = $this->gitHubHelper->getPullRequests('', '');
            }
            return $pullRequests;
        } catch (\Exception $ex) {
            return [];
        }
    }

    private function doVersionCalculation()
    {
        echo 'Fetching last tag... ' . "\n";
        $lastTag = $this->gitHubHelper->getLastTag();
        if ($lastTag!="") {
            echo sprintf("Last tag found: \e[1m %s \e[0m", $lastTag['version']) . "\n";
        } else {
            echo "Not tag found\n";
        }
        echo "Fetching pull requests...\n";
        $pullRequests = $this->getPullRequestsByLastTag($lastTag);

        if (count($pullRequests) > 0) {
            $versionCalculator = new VersionCalculator(
                $this->config->getExpressions(),
                $this->config->getDefaultChange(),
                $this->jiraHelper,
                $pullRequests,
                $lastTag!="" ? $lastTag['version'] : ""
            );
            $versionCalculator->calculate();
        } else {
            echo 'No merged pull requests found since last version (tag).'."\n";
        }

        if (!$lastTag&&count($pullRequests)==0) {
            ErrorHandler::HandleError(new \Exception('Seems to be a new repository.'));
        }
    }
}
