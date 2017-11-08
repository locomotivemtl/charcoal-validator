<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\EmptyValidator;

/**
 *
 */
class EmptyValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new EmptyValidator();
        $this->doTestIsNotEmptyValidator($v);
    }

    public function testRequireEmptyIsTrue()
    {
        $v = new EmptyValidator([
            'require_empty' => true
        ]);
        $this->doTestIsEmptyValidator($v);
    }

    public function testRequireEmptyIsTrueReturnCode()
    {
        $v = new EmptyValidator([
            'require_empty' => true
        ]);
        $this->assertEquals('empty.success.is-empty', $v('')->code());
        $this->assertEquals('empty.failure.is-not-empty', $v('foo')->code());
    }

    public function testRequireEmptyIsFalse()
    {
        $v = new EmptyValidator([
            'require_empty' => false
        ]);
        $this->doTestIsNotEmptyValidator($v);
    }

    public function testRequireEmptyIsFalseReturnCode()
    {
        $v = new EmptyValidator([
            'require_empty' => false
        ]);
        $this->assertEquals('empty.success.is-not-empty', $v('foo')->code());
        $this->assertEquals('empty.failure.is-empty', $v('')->code());
    }


    protected function doTestIsEmptyValidator(EmptyValidator $v)
    {
        $this->assertFalse($v('foobar')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertFalse($v(42)->isValid());
        $this->assertTrue($v(0)->isValid());
        $this->assertFalse($v([1,2,3])->isValid());
        $this->assertTrue($v([])->isValid());
        $obj = new \StdClass();
        $this->assertFalse($v($obj)->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    protected function doTestIsNotEmptyValidator(EmptyValidator $v)
    {
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('')->isValid());
        $this->assertTrue($v(42)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertTrue($v([1,2,3])->isValid());
        $this->assertFalse($v([])->isValid());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isValid());
        $this->assertFalse($v(null)->isValid());
    }
}
