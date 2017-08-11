<?php


namespace Autoversioner\Config;

use Symfony\Component\Yaml\Yaml;

class Expressions
{

    /**
     * @param array $expressions
     */
    public function __construct(array $expressions)
    {
        $expr = [];
        foreach (array_keys($expressions) as $type) {
            $expr[$type] = [];
            foreach ($expressions[$type] as $expression) {
                if (preg_match("/^\/.+\/[a-z]*$/i", $expression)) {
                    array_push($expr[$type], $expression);
                } else {
                    echo "\nWarning: Expression '$expression' of type '$type' is not a valid regular expression\n";
                }
            }
        }
        $this->expressions = $expr;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getExpressions($type)
    {
        if (isset($this->expressions[$type])) {
            return $this->expressions[$type];
        }
        return [];
    }
}
