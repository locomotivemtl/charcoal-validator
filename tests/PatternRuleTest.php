<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use StdClass;
use Charcoal\Validator\PatternRule;

/**
 *
 */
class PatternRuleTest extends TestCase
{

    public function testPattern()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('bar')->isValid());
    }

    public function testPatternReturnCode()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertEquals('pattern.success', $v('foo')->getCode());
        $this->assertEquals('pattern.failure.no-match', $v('bar')->getCode());
    }

    public function testSkipEmptyOrNull()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipEmptyOrNullReturnCode()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertEquals('pattern.skipped.empty-val', $v(null)->getCode());
        $this->assertEquals('pattern.skipped.empty-val', $v('')->getCode());
    }

    public function testSkipInvalidType()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertTrue($v([1, 2, 3])->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
    }

    public function testSkipInvalidTypeReturnCode()
    {
        $v = new PatternRule(
            [
                'pattern' => '/foo/'
            ]
        );
        $this->assertEquals('pattern.skipped.invalid-type', $v([1, 2, 3])->getCode());
        $this->assertEquals('pattern.skipped.invalid-type', $v(new StdClass())->getCode());
    }

    public function testSkipEmptyPattern()
    {
        $v = new PatternRule();
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testSkipEmptyPatternReturnCode()
    {
        $v = new PatternRule();
        $this->assertEquals('pattern.skipped.no-pattern', $v('foo')->getCode());
    }
}
