<?php


namespace Autoversioner\Helpers;

use Autoversioner\Config\Credentials;
use Autoversioner\Config\GitHubCredentials;
use Github\Client;

class GitHubHelper
{
    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $repository;

    /**
     * @var Credentials
     */
    private $config;

    /**
     * @param GitHubRepository $gitHubRepository
     *
     */
    public function __construct(GitHubRepository $gitHubRepository, GitHubCredentials $credentials)
    {
        $this->client = new Client();
        $this->client->authenticate($credentials->getToken(), null, Client::AUTH_HTTP_TOKEN);
        $this->userName = $gitHubRepository->getUserName();
        $this->repository = $gitHubRepository->getRepository();
    }

    public function getTags()
    {
        /**
         * @var \Github\Api\Repo $repoApi
         */
        $repoApi = $this->client->api('repo');
        $tags = $repoApi->tags($this->userName, $this->repository);
        return $tags;
    }

    public function getTag($tagSha)
    {
        /**
         * @var \Github\Api\GitData $dataApi
         */
        $dataApi = $this->client->api('git_data');
        $tag = $dataApi->tags()->show(
            $this->userName,
            $this->repository,
            $tagSha
        );
        return $tag;
    }

    public function getCommit($commitSha)
    {
        /**
         * @var \Github\Api\Repo $repoApi
         */
        $repoApi = $this->client->api('repo');
        return $repoApi->commits()->show(
            $this->userName,
            $this->repository,
            $commitSha
        );
    }

    /**
     * @param string $dateAfter
     * @param string|null $tagCommitSha
     * @return array
     */
    public function getPullRequests($dateAfter, $tagCommitSha = null)
    {
        /**
         * @var \Github\Api\PullRequest $pullApi
         */
        $pullApi = $this->client->api('pull_requests');
        $pullRequests = $pullApi->all(
            $this->userName,
            $this->repository,
            ['state'=>'all']
        );

        if ($dateAfter!="") {
            $datePullAfter = new \DateTime($dateAfter);
            $pullRequestsAfterDate = [];
            foreach ($pullRequests as $pullRequest) {
                $datePull = $pullRequest['merged_at'];
                if ($datePull!="") {
                    $datePull = new \DateTime($datePull);
                    if ($datePull>$datePullAfter) {
                        if ($pullRequest['merge_commit_sha']!=$tagCommitSha) {
                            $pullRequestsAfterDate[] = $pullRequest;
                        }
                    }
                }
            }
            return $pullRequestsAfterDate;
        }
        return $pullRequests;
    }

    public function getLastRelease()
    {
        /**
         * @var \Github\Api\Repo $repoApi
         */
        $repoApi = $this->client->api('repo');
        return $repoApi->releases()->latest(
            $this->userName,
            $this->repository
        );
    }

    public function getLastTag()
    {
        $semanticTags = [];
        $tags=[];
        /**
         * @var \Github\Api\GitData $dataApi
         */
        $dataApi = $this->client->api('git_data');
        try {
            $tagRefs = $dataApi->tags()->all(
                $this->userName,
                $this->repository
            );
            foreach ($tagRefs as $tag) {
                $version = str_replace('refs/tags/', '', $tag['ref']);
                $tag['version'] = $version;
                if (version_compare($version, '0.0.1', '>=')&&strpos($version, '.', 0)>=1) {
                    $semanticTags[]=$version;
                    $tags[$version]=$tag;
                }
            }
            $semanticTags = \Composer\Semver\Semver::rsort($semanticTags);
            $semanticTags = array_slice($semanticTags, 0, 5, true);
            $lastVersion =  $this->getLastVersionByDate($semanticTags, $tags);
            return $tags[$lastVersion];
        } catch (\Exception $ex) {
            return "";
        }
    }

    /**
     * @param $semanticTags
     *
     * @return string
     */
    private function getLastVersionByDate($semanticTagsVersions, $tags)
    {
        /**
         * @var \Github\Api\GitData $dataApi
         */
        $dataApi = $this->client->api('git_data');
        $dataTags = [];
        foreach ($semanticTagsVersions as $version) {
            $tagRef = $tags[$version];
            $tag = $dataApi->tags()->show(
                $this->userName,
                $this->repository,
                $tagRef['object']['sha']
            );
            $dataTags[] = ['version'=>$version,"date"=>$tag['tagger']['date']];
        }
        usort($dataTags, function ($a1, $a2) {
            $v1 = strtotime($a1['date']);
            $v2 = strtotime($a2['date']);
            return $v2 - $v1; // $v2 - $v1 to reverse direction
        });
        return $dataTags[0]['version'];
    }
}
