<?php


namespace Autoversioner\Config;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return array
     */
    public function getParsedYaml()
    {
        return Yaml::parse(file_get_contents($this->filename));
    }
}
