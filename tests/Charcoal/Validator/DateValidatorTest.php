<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Exception;
use DateTime;

use Charcoal\Validator\DateValidator;

/**
 *
 */
class DateValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new DateValidator();
        $this->assertTrue($v('today')->isValid());
        $this->assertTrue($v(new DateTime('1990-01-01'))->isValid());

        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    public function testEmptyDateSkipped()
    {
        $v = new DateValidator([
            'min' => 'today'
        ]);
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
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

    public function testInvalidMinStringThrowsException()
    {
        $this->setExpectedException(Exception::class);
        $v = new DateValidator([
            'min' => 'invalid string'
        ]);
    }

    public function testInvalidMinTypeThrowsException()
    {
        $this->setExpectedException(Exception::class);
        $v = new DateValidator([
            'min' => ['array']
        ]);
    }

    public function testMax()
    {
        $v = new DateValidator([
            'min' => null,
            'max' => 'today'
        ]);
        $this->assertFalse($v('tomorrow')->isValid());
        $this->assertTrue($v(new DateTime('today'))->isValid());
        $this->assertTrue($v('yesterday')->isValid());
    }

    public function testInvalidMaxThrowsException()
    {
        $this->setExpectedException(Exception::class);
        $v = new DateValidator([
            'max' => 'invalid string'
        ]);
    }

    public function testInvalidMaxTypeThrowsException()
    {
        $this->setExpectedException(Exception::class);
        $v = new DateValidator([
            'max' => ['array']
        ]);
    }

    public function testValidateEmptyMinMaxSkipped()
    {
        $v = new DateValidator();
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('tommorow')->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
        $this->assertTrue($v(['array'])->isSkipped());
    }
}
