<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\RegexpValidator;

/**
 *
 */
class RegexpValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new RegexpValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testPattern()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('bar')->isValid());
    }

    public function testSkipEmptyOrNull()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipInvalidType()
    {
        $v = new RegexpValidator([
            'pattern' => '/foo/'
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isSkipped());
    }
}
