<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Exception;
use StdClass;

use Charcoal\Validator\NumberValidator;

/**
 *
 */
class NumberValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new NumberValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testMin()
    {
        $v = new NumberValidator([
            'min' => 4,
            'max' => null
        ]);
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(3)->isValid());
        $this->assertTrue($v(4)->isValid());
        $this->assertTrue($v(5)->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new NumberValidator([
            'min' => 4
        ]);
        $this->assertEquals('number.failure.min', $v(0)->code());
        $this->assertEquals('number.failure.min', $v(3)->code());
        $this->assertEquals('number.success', $v(4)->code());
        $this->assertEquals('number.success', $v(5)->code());
    }

    public function testMax()
    {
        $v = new NumberValidator([
            'min' => null,
            'max' => 4
        ]);
        $this->assertTrue($v(0)->isValid());
        $this->assertTrue($v(3.9)->isValid());
        $this->assertTrue($v(4.0)->isValid());
        $this->assertFalse($v(4.1)->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new NumberValidator([
            'max' => 4
        ]);
        $this->assertEquals('number.success', $v(0)->code());
        $this->assertEquals('number.success', $v(3.9)->code());
        $this->assertEquals('number.success', $v(4.0)->code());
        $this->assertEquals('number.failure.max', $v(4.1)->code());
    }

    public function testMinMax()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertFalse($v(2)->isValid());
        $this->assertTrue($v(3)->isValid());
        $this->assertTrue($v(4)->isValid());
        $this->assertFalse($v(5)->isValid());

        $this->assertFalse($v('2.0')->isValid());
        $this->assertTrue($v('3.0')->isValid());
        $this->assertTrue($v('4.0')->isValid());
        $this->assertFalse($v('5.0')->isValid());
    }

    public function testSkipEmptyOrNull()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());

        // 0 is a numerical value, so it should not be skipped.
        $this->assertFalse($v(0)->isSkipped());
    }

    public function testSkipEmptyOrNullReturnCode()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertEquals('number.skipped.empty-val', $v(null)->code());
        $this->assertEquals('number.skipped.empty-val', $v('')->code());
    }

    public function testSkipInvalidType()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $this->assertTrue($v(new StdClass)->isSkipped());
    }

    public function testInvalidMinThrowsException()
    {
        $this->setExpectedException(Exception::class);

        $v = new NumberValidator([
            'min' => ['failure']
        ]);
    }

    public function testInvalidMaxThrowsException()
    {
        $this->setExpectedException(Exception::class);

        $v = new NumberValidator([
            'max' => ['failure']
        ]);
    }
}
