<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\FilesizeValidator;

/**
 *
 */
class FilesizeValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new FilesizeValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testMin()
    {
        $v = new FilesizeValidator([
            'min' => 4,
            'max' => null
        ]);
        $this->assertFalse($v(dirname(__FILE__).'/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/4bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/5bytes.txt')->isValid());
    }

    public function testMax()
    {
        $v = new FilesizeValidator([
            'min' => null,
            'max' => 4
        ]);
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/4bytes.txt')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/5bytes.txt')->isValid());
    }

    public function testMinMax()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertFalse($v(dirname(__FILE__).'/data/2bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/4bytes.txt')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/5bytes.txt')->isValid());
        ;
    }

    public function testSkipEmptyOrNull()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(0)->isSkipped());
    }

    public function testSkipInvalidType()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4,
            'php' => true
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $obj = new \StdClass();
        $this->assertTrue($v($obj)->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
        $this->assertTrue($v('foo')->isSkipped());
    }

    public function testPhpFailure()
    {

        $v = new FilesizeValidator([
            'min' => ['will-be-cast-to-0'],
            'max' => '5G',
            'php' => true
        ]);
        $this->assertTrue($v(dirname(__FILE__).'/data/5bytes.txt')->isValid());
    }
}
