<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use StdClass;

use Charcoal\Validator\LengthValidator;

/**
 *
 */
class LengthValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testMin()
    {
        $v = new LengthValidator([
            'min' => 4,
            'max' => null
        ]);
        $this->assertFalse($v('')->isValid());
        $this->assertFalse($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertTrue($v('12345')->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new LengthValidator([
            'min' => 4
        ]);
        $this->assertEquals('length.failure.min', $v('123')->code());
        $this->assertEquals('length.success', $v('1234')->code());
        $this->assertEquals('length.success', $v('12345')->code());
    }

    public function testMax()
    {
        $v = new LengthValidator([
            'min' => null,
            'max' => 4
        ]);
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertFalse($v('12345')->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new LengthValidator([
            'max' => 4
        ]);
        $this->assertEquals('length.success', $v('123')->code());
        $this->assertEquals('length.success', $v('1234')->code());
        $this->assertEquals('length.failure.max', $v('12345')->code());
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

    public function testSkipNull()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testSkipNullReturnCode()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertEquals('length.skipped.empty-val', $v(null)->code());
    }

    public function testSkipInvalidType()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $this->assertTrue($v(new StdClass)->isSkipped());
    }

    public function testSkipInvalidTypeReturnCode()
    {
        $v = new LengthValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertEquals('length.skipped.invalid-type', $v([1,2,3])->code());
        $this->assertEquals('length.skipped.invalid-type', $v(new StdClass)->code());
    }

    public function testSkipNoMinMax()
    {
        $v = new LengthValidator();
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testSkipNoMinMaxReturnCode()
    {
        $v = new LengthValidator();
        $this->assertEquals('length.skipped.no-min-max', $v('foo')->code());
    }
}
