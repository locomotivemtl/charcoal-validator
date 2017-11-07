<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\EmptyValidator;

/**
 *
 */
class EmptyValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new EmptyValidator();
        $this->doTestIsNotEmptyValidator($v);
    }

    public function testEmpty()
    {
        $v = new EmptyValidator([
            'require_empty' => true
        ]);
        $this->doTestIsEmptyValidator($v);
    }

    public function testNotEmpty()
    {
        $v = new EmptyValidator([
            'require_empty' => false
        ]);
        $this->doTestIsNotEmptyValidator($v);
    }

    protected function doTestIsEmptyValidator(EmptyValidator $v)
    {
        $this->assertFalse($v('foobar')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertFalse($v(42)->isValid());
        $this->assertTrue($v(0)->isValid());
        $this->assertFalse($v([1,2,3])->isValid());
        $this->assertTrue($v([])->isValid());
        $obj = new \StdClass();
        $this->assertFalse($v($obj)->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    protected function doTestIsNotEmptyValidator(EmptyValidator $v)
    {
        $this->assertTrue($v('foobar')->isValid());
        $this->assertFalse($v('')->isValid());
        $this->assertTrue($v(42)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertTrue($v([1,2,3])->isValid());
        $this->assertFalse($v([])->isValid());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isValid());
        $this->assertFalse($v(null)->isValid());
    }
}
