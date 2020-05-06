<?php

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use DateTime;
use Charcoal\Validator\DateRule;

/**
 *
 */
class DateRuleTest extends TestCase
{
    public function testEmptyDateSkipped()
    {
        $v = new DateRule(
            [
                'min' => 'today'
            ]
        );
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testEmptyDateSkippedReturnCode()
    {
        $v = new DateRule(
            [
                'min' => 'today'
            ]
        );
        $this->assertEquals('date.skipped.empty-val', $v('')->getCode());
        $this->assertEquals('date.skipped.empty-val', $v(null)->getCode());
    }

    public function testInvalidDateStringFails()
    {
        $v = new DateRule(
            [
                'min' => new DateTime('today'),
                'max' => null
            ]
        );
        $this->assertFalse($v('invalid string')->isValid());
        $this->assertFalse($v(1)->isValid());
        $this->assertFalse($v(['array'])->isValid());
    }

    public function testInvalidDateStringFailsReturnCode()
    {
        $v = new DateRule(
            [
                'min' => new DateTime('today'),
                'max' => null
            ]
        );
        $this->assertEquals('date.failure.invalid-type', $v('invalid string')->getCode());
        $this->assertEquals('date.failure.invalid-type', $v(1)->getCode());
        $this->assertEquals('date.failure.invalid-type', $v(['array'])->getCode());
    }

    public function testInvalidDateStringSkipsIfCheckTypeIsFalse()
    {
        $v = new DateRule(
            [
                'min' => new DateTime('today'),
                'max' => null,
                'checkType' => false
            ]
        );
        $this->assertTrue($v('invalid string')->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
        $this->assertTrue($v(['array'])->isSkipped());
    }

    public function testInvalidDateStringSkipsIfCheckTypeIsFalseReturnCode()
    {
        $v = new DateRule(
            [
                'min' => new DateTime('today'),
                'max' => null,
                'checkType' => false
            ]
        );
        $this->assertEquals('date.skipped.invalid-type', $v('invalid string')->getCode());
        $this->assertEquals('date.skipped.invalid-type', $v(1)->getCode());
        $this->assertEquals('date.skipped.invalid-type', $v(['array'])->getCode());
    }

    public function testMin()
    {
        $v = new DateRule(
            [
                'min' => 'today',
                'max' => null
            ]
        );
        $this->assertTrue($v('tomorrow')->isValid());
        $this->assertTrue($v(new DateTime('today'))->isValid());
        $this->assertFalse($v('yesterday')->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new DateRule(
            [
                'min' => 'today'
            ]
        );
        $this->assertEquals('date.success', $v('tomorrow')->getCode());
        $this->assertEquals('date.failure.min', $v('yesterday')->getCode());
    }

    public function testInvalidMinStringThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $v = new DateRule(
            [
                'min' => 'invalid string'
            ]
        );
    }

    public function testInvalidMinTypeThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $v = new DateRule(
            [
                'min' => ['array']
            ]
        );
    }

    public function testMax()
    {
        $v = new DateRule(
            [
                'max' => 'today'
            ]
        );
        $this->assertFalse($v('tomorrow')->isValid());
        $this->assertTrue($v(new DateTime('today'))->isValid());
        $this->assertTrue($v('yesterday')->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new DateRule(
            [
                'max' => 'today'
            ]
        );
        $this->assertEquals('date.failure.max', $v('tomorrow')->getCode());
        $this->assertEquals('date.success', $v('yesterday')->getCode());
    }


    public function testInvalidMaxThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $v = new DateRule(
            [
                'max' => 'invalid string'
            ]
        );
    }

    public function testInvalidMaxTypeThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $v = new DateRule(
            [
                'max' => ['array']
            ]
        );
    }
}
