<?php


use Autoversioner\Config\YamlParser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class CredentialsTest extends TestCase
{
    /**
     * @var YamlParser | MockObject
     */
    private $yamlParser;

    protected function setUp()
    {
        parent::setUp();
        $this->yamlParser = $this->getMockBuilder(YamlParser::class)->disableOriginalConstructor()->getMock();
    }

    public function testGetGithubCredentials()
    {
        $credentials = [
            'github'=> [
                'username' => "foo",
                "token" => "bar"
            ]
        ];
        $this->yamlParser->expects($this->once())
            ->method('getParsedYaml')
            ->willReturn($credentials);

        $credentials = new \Autoversioner\Config\Credentials($this->yamlParser);
        $githubCred = $credentials->getGitHubCredentials();

        $this->assertEquals($githubCred->getUserName(), 'foo');
        $this->assertEquals($githubCred->getToken(), 'bar');
    }

    public function testGetJiraCredentials()
    {
        $credentials = [
            'jira'=> [
                'url'=>'url',
                'user' => 'foo',
                'token' => 'bar'
            ]
        ];
        $this->yamlParser->expects($this->once())
            ->method('getParsedYaml')
            ->willReturn($credentials);

        $credentials = new \Autoversioner\Config\Credentials($this->yamlParser);
        $jiraCred = $credentials->getJiraCredentials();

        $this->assertEquals($jiraCred->getUserName(), 'foo');
        $this->assertEquals($jiraCred->getToken(), 'bar');
        $this->assertEquals($jiraCred->getUrl(), 'url');
    }
}
