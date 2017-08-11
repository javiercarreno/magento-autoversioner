<?php


namespace Autoversioner\Core;

use Autoversioner\Config\Expressions;
use Autoversioner\Helpers\ErrorHandler;
use Autoversioner\Helpers\JiraHelper;
use Composer\Semver\Semver;
use SebastianBergmann\Version;

class VersionCalculator
{
    const DEFAULT_MINOR = 'minor';
    const DEFAULT_PATCH = 'patch';
    /**
     * @var array
     */
    private $pullRequests;

    /**
     * @var string
     */
    private $lastVersion;
    /**
     * @var JiraHelper
     */
    private $jiraHelper;
    /**
     * @var Expressions
     */
    private $expressions;
    /**
     * @var string
     */
    private $defaultChange;

    /**
     * @param Expressions $expressions
     * @param string $defaultChange
     * @param JiraHelper $jiraHelper
     * @param array $pullRequests
     * @param string $lastVersion
     */
    public function __construct(Expressions $expressions, $defaultChange, JiraHelper $jiraHelper, array $pullRequests, $lastVersion)
    {
        $this->pullRequests = $pullRequests;
        $this->expressions = $expressions;
        $this->defaultChange = $defaultChange;
        if ($lastVersion=="") {
            $lastVersion="0.0.0";
        }
        $this->lastVersion = $lastVersion;
        $this->jiraHelper = $jiraHelper;
    }

    /**
     * return VersionResult
     */
    public function calculate()
    {
        $this->aasort($this->pullRequests, 'merged_at');
        $minorChanges=0;
        $patches=0;
        foreach ($this->pullRequests as $pullRequest) {
            echo "\n";
            echo sprintf('Pull Request \'%s\'. Date of Pull Request: %s', $pullRequest['title'], $pullRequest['merged_at']);
            $issue = $this->jiraHelper->getJiraIssue($pullRequest['title']);
            if ($issue!==false) {
                $jiraIssueType = $this->jiraHelper->getIssueType($issue);
                switch ($jiraIssueType) {
                    case JiraHelper::ISSUE_BUG: $patches++; echo "\n\e[32mJira Bug: Patch\e[39m";break;
                    case JiraHelper::ISSUE_STORY: $minorChanges++;echo "\n\e[32mJira Story: Minor changes\e[39m";break;
                    case JiraHelper::ISSUE_OTHER: $patches++;echo "\n\e[33mJira issue is not bug or story.\e[39m";break;
                    case JiraHelper::ISSUE_NOT_EXIST: echo "\n\e[91mJira exception or issue does not exist.\e[39m";break;
                }
            } else {
                $type = $this->processTitleByConfig($pullRequest['title']);
                if ($type!="") {
                    if ($type==self::DEFAULT_MINOR) {
                        echo "\n\e[32mMinor change (configured)\e[39m";
                        $minorChanges++;
                    } else {
                        echo "\n\e[32mPatch change (configured)\e[39m";
                        $patches++;
                    }
                } else {
                    echo "\n\e[91mCannot resolve pull request title.\e[39m";
                    if ($this->defaultChange!="") {
                        switch ($this->defaultChange) {
                            case self::DEFAULT_MINOR: echo " \e[33mAssuming minor change\e[39m";$minorChanges++;break;
                            case self::DEFAULT_PATCH: echo " \e[33mAssuming patch change\e[39m";$patches++;break;
                        }
                    }
                }
            }
        }
        echo "\n";
        $version = new \vierbergenlars\SemVer\version($this->lastVersion);
        if ($minorChanges==0&&$patches==0&&count($this->pullRequests)>0) {
            ErrorHandler::HandleError(new \Exception("Cannot resolve any pull request. (No minor, no patches)."));
        } else {
            echo "\n\nRecount: $minorChanges minor changes and $patches patches.";
            echo "\nSuggested version number: ";
            if ($minorChanges>0) {
                $version = $version->inc("minor");
            } else {
                $version = $version->inc('patch');
            }
            echo "\e[1m".$version."\e[0m\n";
        }
    }

    private function processTitleByConfig($title)
    {
        foreach ($this->expressions->getExpressions(self::DEFAULT_MINOR) as $expression) {
            $match = [];
            if (preg_match($expression, $title, $match)>0) {
                return self::DEFAULT_MINOR;
            }
        }

        foreach ($this->expressions->getExpressions(self::DEFAULT_PATCH) as $expression) {
            $match = [];
            if (preg_match($expression, $title, $match)>0) {
                return self::DEFAULT_PATCH;
            }
        }

        return "";
    }

    private function aasort(&$array, $key)
    {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }
}
