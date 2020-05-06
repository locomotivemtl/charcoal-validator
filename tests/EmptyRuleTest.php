<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Charcoal\Validator\EmptyRule;

/**
 *
 */
class EmptyRuleTest extends TestCase
{
    public function testDefaultRule()
    {
        $v = new EmptyRule();
        $this->doTestIsNotEmptyRule($v);
    }

    public function testRequireEmptyIsTrue()
    {
        $v = new EmptyRule(
            [
                'requireEmpty' => true
            ]
        );
        $this->doTestIsEmptyRule($v);
    }

    public function testRequireEmptyIsTrueReturnCode()
    {
        $v = new EmptyRule(
            [
                'requireEmpty' => true
            ]
        );
        $this->assertEquals('empty.success.is-empty', $v('')->getCode());
        $this->assertEquals('empty.failure.is-not-empty', $v('foo')->getCode());
    }

    public function testRequireEmptyIsFalse()
    {
        $v = new EmptyRule(
            [
                'requireEmpty' => false
            ]
        );
        $this->doTestIsNotEmptyRule($v);
    }

    public function testRequireEmptyIsFalseReturnCode()
    {
        $v = new EmptyRule(
            [
                'requireEmpty' => false
            ]
        );
        $this->assertEquals('empty.success.is-not-empty', $v('foo')->getCode());
        $this->assertEquals('empty.failure.is-empty', $v('')->getCode());
    }


    protected function doTestIsEmptyRule(EmptyRule $v)
    {
        $this->assertFalse($v('foobar')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertFalse($v(42)->isValid());
        $this->assertTrue($v(0)->isValid());
        $this->assertFalse($v([1, 2, 3])->isValid());
        $this->assertTrue($v([])->isValid());
        $obj = new \StdClass();
        $this->assertFalse($v($obj)->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    protected function doTestIsNotEmptyRule(EmptyRule $v)
    {
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('')->isValid());
        $this->assertTrue($v(42)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertTrue($v([1, 2, 3])->isValid());
        $this->assertFalse($v([])->isValid());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isValid());
        $this->assertFalse($v(null)->isValid());
    }
}
