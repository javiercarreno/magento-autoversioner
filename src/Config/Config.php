<?php


namespace Autoversioner\Config;

use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Expressions
     */
    private $expressions;

    /**
     * @param YamlParser $parser
     */
    public function __construct(YamlParser $parser)
    {
        $this->config = $parser->getParsedYaml();
        $this->expressions = $this->createExpressions();
    }

    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @return string
     */
    public function getDefaultChange()
    {
        if (isset($this->config['config'])&&isset($this->config['config']['default_change'])) {
            return $this->config['config']['default_change'];
        } else {
            return "";
        }
    }

    /**
     * @return Expressions
     */
    private function createExpressions()
    {
        if (isset($this->config['expressions'])) {
            return new Expressions($this->config['expressions']);
        } else {
            return new Expressions([]);
        }
    }
}
