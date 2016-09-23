<?php

namespace Charcoal\Tests\Validator;

use \PHPUnit_Framework_TestCase;

use \Charcoal\Validator\NullValidator;

/**
 *
 */
class NullValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new NullValidator();
        $this->doTestIsNotNullValidator($v);
    }

    public function testNull()
    {
        $v = new NullValidator([
            'require_null' => true
        ]);
        $this->doTestIsNullValidator($v);
    }

    public function testNotNull()
    {
        $v = new NullValidator([
            'require_null' => false
        ]);
        $this->doTestIsNotNullValidator($v);
    }

    protected function doTestIsNullValidator(NullValidator $v)
    {
        $this->assertFalse($v('foobar')->isValid());
        $this->assertFalse($v('')->isValid());
        $this->assertFalse($v(42)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v([1,2,3])->isValid());
        $this->assertFalse($v([])->isValid());
        $obj = new \StdClass();
        $this->assertFalse($v($obj)->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    protected function doTestIsNotNullValidator(NullValidator $v)
    {
        $this->assertTrue($v('foobar')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v(42)->isValid());
        $this->assertTrue($v(0)->isValid());
        $this->assertTrue($v([1,2,3])->isValid());
        $this->assertTrue($v([])->isValid());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isValid());
        $this->assertFalse($v(null)->isValid());
    }
}
