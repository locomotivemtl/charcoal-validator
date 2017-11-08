<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use StdClass;

use Charcoal\Validator\RegexpValidator;

/**
 *
 */
class RegexpValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testPattern()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('bar')->isValid());
    }

    public function testPatternReturnCode()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertEquals('regexp.success', $v('foo')->code());
        $this->assertEquals('regexp.failure.no-match', $v('bar')->code());
    }

    public function testSkipEmptyOrNull()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipEmptyOrNullReturnCode()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertEquals('regexp.skipped.empty-val', $v(null)->code());
        $this->assertEquals('regexp.skipped.empty-val', $v('')->code());
    }

    public function testSkipInvalidType()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $this->assertTrue($v(new StdClass)->isSkipped());
    }

    public function testSkipInvalidTypeReturnCode()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertEquals('regexp.skipped.invalid-type', $v([1,2,3])->code());
        $this->assertEquals('regexp.skipped.invalid-type', $v(new StdClass)->code());
    }

    public function testSkipEmptyPattern()
    {
        $v = new RegexpValidator();
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testSkipEmptyPatternReturnCode()
    {
        $v = new RegexpValidator();
        $this->assertEquals('regexp.skipped.no-pattern', $v('foo')->code());
    }
}
