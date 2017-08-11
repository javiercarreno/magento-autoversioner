<?php


use Autoversioner\Config\Config;
use Autoversioner\Config\YamlParser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ConfigTest extends TestCase
{
    /**
     * @var YamlParser|MockObject
     */
    private $yamlParser;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->yamlParser = $this->getMockBuilder(YamlParser::class)->disableOriginalConstructor()->getMock();
    }

    public function testGetExpressions()
    {
        $exp = ['jira'=>['/expr/']];
        $config = [
            'expressions'=>$exp
        ];

        $this->yamlParser->expects($this->once())
            ->method('getParsedYaml')
            ->willReturn($config);
        $config = new Config($this->yamlParser);

        $var = $config->getExpressions()->getExpressions('jira');

        $this->assertTrue($var[0]=="/expr/");
    }

    public function testGetDefaultChange()
    {
        $default = ['default_change'=>'patch'];
        $config = [
            'config'=>$default
        ];
        $this->yamlParser->expects($this->once())
            ->method('getParsedYaml')
            ->willReturn($config);
        $config = new Config($this->yamlParser);
        $this->assertEquals('patch', $config->getDefaultChange());
    }
}
