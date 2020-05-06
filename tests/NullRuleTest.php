<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Charcoal\Validator\NullRule;

/**
 *
 */
class NullRuleTest extends TestCase
{
    public function testDefaultRule()
    {
        $v = new NullRule();
        $this->doTestIsNotNullRule($v);
    }

    public function testNull()
    {
        $v = new NullRule(
            [
                'requireNull' => true
            ]
        );
        $this->doTestIsNullRule($v);
    }

    public function testNullReturnCode()
    {
        $v = new NullRule(
            [
                'requireNull' => true
            ]
        );
        $this->assertEquals('null.success.is-null', $v(null)->getCode());
        $this->assertEquals('null.failure.is-not-null', $v('foo')->getCode());
    }

    public function testNotNull()
    {
        $v = new NullRule(
            [
                'require_null' => false
            // default
            ]
        );
        $this->doTestIsNotNullRule($v);
    }

    public function testNotNullReturnCode()
    {
        $v = new NullRule(
            [
                'require_null' => false
            // default
            ]
        );
        $this->assertEquals('null.failure.is-null', $v(null)->getCode());
        $this->assertEquals('null.success.is-not-null', $v('foo')->getCode());
    }

    protected function doTestIsNullRule(NullRule $v)
    {
        $this->assertFalse($v('foobar')->isValid());
        $this->assertFalse($v('')->isValid());
        $this->assertFalse($v(42)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v([1, 2, 3])->isValid());
        $this->assertFalse($v([])->isValid());
        $obj = new \StdClass();
        $this->assertFalse($v($obj)->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    protected function doTestIsNotNullRule(NullRule $v)
    {
        $this->assertTrue($v('foobar')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v(42)->isValid());
        $this->assertTrue($v(0)->isValid());
        $this->assertTrue($v([1, 2, 3])->isValid());
        $this->assertTrue($v([])->isValid());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isValid());
        $this->assertFalse($v(null)->isValid());
    }
}
