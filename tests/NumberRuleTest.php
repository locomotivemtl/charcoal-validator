<?php

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Exception;
use StdClass;
use Charcoal\Validator\NumberRule;

/**
 *
 */
class NumberRuleTest extends TestCase
{
    public function testDefaultValidator()
    {
        $v = new NumberRule();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testMin()
    {
        $v = new NumberRule(
            [
                'min' => 4,
                'max' => null
            ]
        );
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(3)->isValid());
        $this->assertTrue($v(4)->isValid());
        $this->assertTrue($v(5)->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new NumberRule(
            [
                'min' => 4
            ]
        );
        $this->assertEquals('number.failure.min', $v(0)->getCode());
        $this->assertEquals('number.failure.min', $v(3)->getCode());
        $this->assertEquals('number.success', $v(4)->getCode());
        $this->assertEquals('number.success', $v(5)->getCode());
    }

    public function testMax()
    {
        $v = new NumberRule(
            [
                'min' => null,
                'max' => 4
            ]
        );
        $this->assertTrue($v(0)->isValid());
        $this->assertTrue($v(3.9)->isValid());
        $this->assertTrue($v(4.0)->isValid());
        $this->assertFalse($v(4.1)->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new NumberRule(
            [
                'max' => 4
            ]
        );
        $this->assertEquals('number.success', $v(0)->getCode());
        $this->assertEquals('number.success', $v(3.9)->getCode());
        $this->assertEquals('number.success', $v(4.0)->getCode());
        $this->assertEquals('number.failure.max', $v(4.1)->getCode());
    }

    public function testMinMax()
    {
        $v = new NumberRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
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
        $v = new NumberRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());

        // 0 is a numerical value, so it should not be skipped.
        $this->assertFalse($v(0)->isSkipped());
    }

    public function testSkipEmptyOrNullReturnCode()
    {
        $v = new NumberRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertEquals('number.skipped.empty-val', $v(null)->getCode());
        $this->assertEquals('number.skipped.empty-val', $v('')->getCode());
    }

    public function testCheckTypeFalseSkipInvalidType()
    {
        $v = new NumberRule(
            [
                'min' => 3,
                'max' => 4,
                'checkType' => false
            ]
        );
        $this->assertTrue($v([1, 2, 3])->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
    }

    public function testInvalidMinThrowsException()
    {
        $this->expectException(Exception::class);

        $v = new NumberRule(
            [
                'min' => ['failure']
            ]
        );
    }

    public function testInvalidMaxThrowsException()
    {
        $this->expectException(Exception::class);

        $v = new NumberRule(
            [
                'max' => ['failure']
            ]
        );
    }
}
