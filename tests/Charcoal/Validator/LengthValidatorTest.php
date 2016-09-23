<?php

namespace Charcoal\Tests\Validator;

use \PHPUnit_Framework_TestCase;

use \Charcoal\Validator\LengthValidator;

/**
 *
 */
class LengthValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new LengthValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testMin()
    {
        $v = new LengthValidator([
            'min' => 4
        ]);
        $this->assertFalse($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertTrue($v('12345')->isValid());
    }

    public function testMax()
    {
        $v = new LengthValidator([
            'max' => 4
        ]);
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertFalse($v('12345')->isValid());
    }

    public function testMinMax()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertFalse($v('12')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertFalse($v('12345')->isValid());

        // Because unicode is true by default, the following strings also work.
        $this->assertFalse($v('°∆')->isValid());
        $this->assertTrue($v('°∆å')->isValid());
        $this->assertTrue($v('°∆åß')->isValid());
        $this->assertFalse($v('°∆åß∂')->isValid());
    }

    public function testUnicode()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4,
            'unicode' => false
        ]);
        $this->assertFalse($v('12')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());

        // Erroneous count because unicode = false (∆ = 3 chars, ° = 2 chars).
        $this->assertTrue($v('∆')->isValid());
        $this->assertFalse($v('°∆')->isValid());
    }

    public function testSkipEmptyOrNull()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipInvalidType()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isSkipped());
    }
}
