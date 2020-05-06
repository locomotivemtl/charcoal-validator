<?php

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use StdClass;
use Charcoal\Validator\LengthRule;

/**
 *
 */
class LengthRuleTest extends TestCase
{

    public function testMin()
    {
        $v = new LengthRule(
            [
                'min' => 4,
                'max' => null
            ]
        );
        $this->assertFalse($v('')->isValid());
        $this->assertFalse($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertTrue($v('12345')->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new LengthRule(
            [
                'min' => 4
            ]
        );
        $this->assertEquals('length.failure.min', $v('123')->getCode());
        $this->assertEquals('length.success', $v('1234')->getCode());
        $this->assertEquals('length.success', $v('12345')->getCode());
    }

    public function testMax()
    {
        $v = new LengthRule(
            [
                'min' => null,
                'max' => 4
            ]
        );
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());
        $this->assertFalse($v('12345')->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new LengthRule(
            [
                'max' => 4
            ]
        );
        $this->assertEquals('length.success', $v('123')->getCode());
        $this->assertEquals('length.success', $v('1234')->getCode());
        $this->assertEquals('length.failure.max', $v('12345')->getCode());
    }

    public function testMinMax()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
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

    public function testUnigetCode()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4,
                'unicode' => false
            ]
        );
        $this->assertFalse($v('12')->isValid());
        $this->assertTrue($v('123')->isValid());
        $this->assertTrue($v('1234')->isValid());

        // Erroneous count because unicode = false (∆ = 3 chars, ° = 2 chars).
        $this->assertTrue($v('∆')->isValid());
        $this->assertFalse($v('°∆')->isValid());
    }

    public function testSkipNull()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testSkipNullReturnCode()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertEquals('length.skipped.empty-val', $v(null)->getCode());
    }

    public function testSkipInvalidType()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertTrue($v([1, 2, 3])->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
    }

    public function testSkipInvalidTypeReturnCode()
    {
        $v = new LengthRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertEquals('length.skipped.invalid-type', $v([1, 2, 3])->getCode());
        $this->assertEquals('length.skipped.invalid-type', $v(new StdClass())->getCode());
    }

    public function testSkipNoMinMax()
    {
        $v = new LengthRule();
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testSkipNoMinMaxReturnCode()
    {
        $v = new LengthRule();
        $this->assertEquals('length.skipped.no-min-max', $v('foo')->getCode());
    }
}
