<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use StdClass;

use Charcoal\Validator\FilesizeValidator;

/**
 *
 */
class FilesizeValidatorTest extends PHPUnit_Framework_TestCase
{
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

    public function testMinReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => 4
        ]);
        $this->assertEquals('filesize.failure.min', $v(dirname(__FILE__).'/data/3bytes.txt')->code());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__).'/data/4bytes.txt')->code());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__).'/data/5bytes.txt')->code());
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

    public function testMaxReturnCode()
    {
        $v = new FilesizeValidator([
            'max' => 4
        ]);
        $this->assertEquals('filesize.success', $v(dirname(__FILE__).'/data/3bytes.txt')->code());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__).'/data/4bytes.txt')->code());
        $this->assertEquals('filesize.failure.max', $v(dirname(__FILE__).'/data/5bytes.txt')->code());
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
    }

    public function testSkipEmptyOrNullReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4
        ]);
        $this->assertEquals('filesize.skipped.empty-val', $v(null)->code());
        $this->assertEquals('filesize.skipped.empty-val', $v('')->code());
    }

    public function testInvalidTypeCheckTypeTrue()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4,
            'check_type' => true
        ]);
        $this->assertFalse($v([1,2,3])->isValid());
        $this->assertFalse($v(new StdClass)->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testInvalidFileCheckFileTrue()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4,
            'check_file' => true
        ]);

        $this->assertFalse($v('foo')->isValid());
    }

    public function testInvalidTypeCheckTypeFalse()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4,
            'check_type' => false
        ]);
        $this->assertTrue($v([1,2,3])->isSkipped());
        $this->assertTrue($v(new StdClass)->isSkipped());
        $this->assertTrue($v(0)->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
    }

    public function testInvalidFileCheckFileFalse()
    {
        $v = new FilesizeValidator([
            'min' => 3,
            'max' => 4,
            'check_file' => true
        ]);

        $this->assertFalse($v('foo')->isValid());
    }

    public function testInvalidTypeCheckTypeTrueReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => '10k',
            'max' => '200M',
            'check_type' => true
        ]);
        $this->assertEquals('filesize.failure.invalid-type', $v([1,2,3])->code());
        $this->assertEquals('filesize.failure.invalid-type', $v(new StdClass)->code());
        $this->assertEquals('filesize.failure.invalid-type', $v(0)->code());
        $this->assertEquals('filesize.failure.invalid-type', $v(1)->code());
    }

    public function testInvalidTypeCheckTypeFalseReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => '100k',
            'max' => '200M',
            'check_type' => false
        ]);
        $this->assertEquals('filesize.skipped.invalid-type', $v([1,2,3])->code());
        $this->assertEquals('filesize.skipped.invalid-type', $v(new StdClass)->code());
        $this->assertEquals('filesize.skipped.invalid-type', $v(0)->code());
        $this->assertEquals('filesize.skipped.invalid-type', $v(1)->code());
    }

    public function testInvalidFileCheckFileTrueReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => '10k',
            'max' => '200M',
            'check_file' => true
        ]);
        $this->assertEquals('filesize.failure.invalid-file', $v('foo')->code());
    }

    public function testInvalidFileCheckFileFalseReturnCode()
    {
        $v = new FilesizeValidator([
            'min' => '10k',
            'max' => '200M',
            'check_file' => false
        ]);
        $this->assertEquals('filesize.skipped.invalid-file', $v('foo')->code());
    }

    public function testSkipNoMinMax()
    {
        $v = new FilesizeValidator();
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isSkipped());
    }

    public function testSkipNoMinMaxReturnCode()
    {
        $v = new FilesizeValidator();
        $this->assertEquals('filesize.skipped.no-min-max', $v(dirname(__FILE__).'/data/3bytes.txt')->code());
    }
}
