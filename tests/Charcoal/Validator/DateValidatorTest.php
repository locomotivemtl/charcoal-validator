<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use InvalidArgumentException;
use DateTime;

use Charcoal\Validator\DateValidator;

/**
 *
 */
class DateValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyDateSkipped()
    {
        $v = new DateValidator([
            'min' => 'today'
        ]);
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testEmptyDateSkippedReturnCode()
    {
        $v = new DateValidator([
            'min' => 'today'
        ]);
        $this->assertEquals('date.skipped.empty-val', $v('')->code());
        $this->assertEquals('date.skipped.empty-val', $v(null)->code());
    }

    public function testInvalidDateStringFails()
    {
        $v = new DateValidator([
            'min' => new DateTime('today'),
            'max' => null
        ]);
        $this->assertFalse($v('invalid string')->isValid());
        $this->assertFalse($v(1)->isValid());
        $this->assertFalse($v(['array'])->isValid());
    }

    public function testInvalidDateStringFailsReturnCode()
    {
        $v = new DateValidator([
            'min' => new DateTime('today'),
            'max' => null
        ]);
        $this->assertEquals('date.failure.invalid-type', $v('invalid string')->code());
        $this->assertEquals('date.failure.invalid-type', $v(1)->code());
        $this->assertEquals('date.failure.invalid-type', $v(['array'])->code());
    }

    public function testInvalidDateStringSkipsIfCheckTypeIsFalse()
    {
        $v = new DateValidator([
            'min' => new DateTime('today'),
            'max' => null,
            'check_type' => false
        ]);
        $this->assertTrue($v('invalid string')->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
        $this->assertTrue($v(['array'])->isSkipped());
    }

    public function testInvalidDateStringSkipsIfCheckTypeIsFalseReturnCode()
    {
        $v = new DateValidator([
            'min' => new DateTime('today'),
            'max' => null,
            'check_type' => false
        ]);
        $this->assertEquals('date.skipped.invalid-type', $v('invalid string')->code());
        $this->assertEquals('date.skipped.invalid-type', $v(1)->code());
        $this->assertEquals('date.skipped.invalid-type', $v(['array'])->code());
    }

    public function testMin()
    {
        $v = new DateValidator([
            'min' => 'today',
            'max' => null
        ]);
        $this->assertTrue($v('tomorrow')->isValid());
        $this->assertTrue($v(new DateTime('today'))->isValid());
        $this->assertFalse($v('yesterday')->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new DateValidator([
            'min' => 'today'
        ]);
        $this->assertEquals('date.success', $v('tomorrow')->code());
        $this->assertEquals('date.failure.min', $v('yesterday')->code());
    }

    public function testInvalidMinStringThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $v = new DateValidator([
            'min' => 'invalid string'
        ]);
    }

    public function testInvalidMinTypeThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $v = new DateValidator([
            'min' => ['array']
        ]);
    }

    public function testMax()
    {
        $v = new DateValidator([
            'max' => 'today'
        ]);
        $this->assertFalse($v('tomorrow')->isValid());
        $this->assertTrue($v(new DateTime('today'))->isValid());
        $this->assertTrue($v('yesterday')->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new DateValidator([
            'max' => 'today'
        ]);
        $this->assertEquals('date.failure.max', $v('tomorrow')->code());
        $this->assertEquals('date.success', $v('yesterday')->code());
    }


    public function testInvalidMaxThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $v = new DateValidator([
            'max' => 'invalid string'
        ]);
    }

    public function testInvalidMaxTypeThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $v = new DateValidator([
            'max' => ['array']
        ]);
    }
}
