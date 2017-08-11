<?php


use Autoversioner\Config\JiraCredentials;

class JiraCredentialsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var JiraCredentials
     */
    private $githubCredentials;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $config = ['user'=>'foo','token'=>'bar','url'=>'url'];
        $this->githubCredentials = new JiraCredentials($config);
    }


    public function testGetUserName()
    {
        $this->assertEquals($this->githubCredentials->getUserName(), 'foo');
    }

    public function testGetToken()
    {
        $this->assertEquals($this->githubCredentials->getToken(), 'bar');
    }
    public function testGetUrl()
    {
        $this->assertEquals($this->githubCredentials->getUrl(), 'url');
    }
}
