<?php


namespace Autoversioner\Helpers;

use Autoversioner\Config\Credentials;
use Autoversioner\Config\Expressions;
use Autoversioner\Config\JiraCredentials;

class JiraHelper
{
    const ISSUE_BUG='Bug';
    const ISSUE_STORY = 'Story';
    const ISSUE_OTHER = 'Unknown';
    const ISSUE_NOT_EXIST = 'NA';
    /**
     * @var JiraCredentials
     */
    private $jiraCredentials;
    /**
     * @var array
     */
    private $jiraExpressions;

    /**
     *
     */
    public function __construct(JiraCredentials $jiraCredentials, Expressions $expressions)
    {
        $this->jiraCredentials = $jiraCredentials;
        $this->jiraExpressions = $expressions;
    }

    /**
     * @param string
     */
    public function getIssueType($ticketName)
    {
        $url = $this->jiraCredentials->getUrl().'/rest/api/2/issue/'.$ticketName;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Authorization: Basic '.$this->jiraCredentials->getToken()
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        try {
            $data = curl_exec($curl);
            $issue = json_decode($data);
            curl_close($curl);
            if ($issue!=null) {
                switch ($issue->fields->issuetype->name) {
                    case 'Story':
                        return self::ISSUE_STORY;
                    case 'Bug':
                        return self::ISSUE_BUG;
                    default:
                        return self::ISSUE_OTHER;
                    }
            } else {
                return self::ISSUE_NOT_EXIST;
            }
        } catch (\Exception $ex) {
            return self::ISSUE_NOT_EXIST;
        }
    }

    /**
     * @param $title
     * @return bool|string
     */
    public function getJiraIssue($title)
    {
        foreach ($this->jiraExpressions as $expr) {
            $match = [];
            if (preg_match('/PD[- ][0-9]{1,9}/', $title, $match)>0) {
                return $match[0];
            }
        }
        return false;
    }
}
