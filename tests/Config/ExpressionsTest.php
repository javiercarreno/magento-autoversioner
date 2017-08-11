<?php


class ExpressionsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetExpression()
    {
        $expressions =
            ['jira' => ['/FOO/']];

        $expr = new \Autoversioner\Config\Expressions($expressions);
        $this->assertEquals($expr->getExpressions('jira'), ['/FOO/']);
    }

    /**
     *
     */
    public function testNotValidExpression()
    {
        $expressions =
            ['jira' => ['foo']];
        $this->expectOutputString("\nWarning: Expression 'foo' of type 'jira' is not a valid regular expression\n");
        $expr = new \Autoversioner\Config\Expressions($expressions);
    }
}
