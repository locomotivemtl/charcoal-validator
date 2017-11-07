<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Exception;

use Charcoal\Validator\NumberValidator;

/**
 *
 */
class NumberValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new NumberValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testMin()
    {
        $v = new NumberValidator([
            'min' => 4,
            'max' => null
        ]);
        $this->assertFalse($v(3)->isValid());
        $this->assertTrue($v(4)->isValid());
        $this->assertTrue($v(5)->isValid());
    }

    public function testMax()
    {
        $v = new NumberValidator([
            'min' => null,
            'max' => 4
        ]);
        $this->assertTrue($v(3.9)->isValid());
        $this->assertTrue($v(4.0)->isValid());
        $this->assertFalse($v(4.1)->isValid());
    }

    public function testMinMax()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
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
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());

        // 0 is a numerical value, so it should not be skipped.
        $this->assertFalse($v(0)->isSkipped());
    }

    public function testSkipInvalidType()
    {
        $v = new NumberValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isSkipped());
    }

    public function testInvalidMinThrowsException()
    {
        $this->setExpectedException(Exception::class);

        $v = new NumberValidator([
            'min' => ['failure']
        ]);
    }

    public function testInvalidMaxThrowsException()
    {
        $this->setExpectedException(Exception::class);

        $v = new NumberValidator([
            'max' => ['failure']
        ]);
    }
}
