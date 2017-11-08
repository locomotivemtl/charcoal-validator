<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use InvalidArgumentException;
use StdClass;

use Charcoal\Validator\FiletypeValidator;

/**
 *
 */
class FiletypeValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testAcceptedAsCommaSeparatedString()
    {
        $v = new FiletypeValidator([
            'accepted' => 'image/png, text/plain'
        ]);
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/100x100.png')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/100x100.jpg')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/mimetype.pdf')->isValid());
    }

    public function testAccepted()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__).'/data/100x100.png')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/100x100.jpg')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data/mimetype.pdf')->isValid());
    }

    public function testAcceptedReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertEquals('filetype.success', $v(dirname(__FILE__).'/data/3bytes.txt')->code());
        $this->assertEquals('filetype.success', $v(dirname(__FILE__).'/data/100x100.png')->code());
        $this->assertEquals('filetype.failure.accepted', $v(dirname(__FILE__).'/data/100x100.jpg')->code());
        $this->assertEquals('filetype.failure.accepted', $v(dirname(__FILE__).'/data/mimetype.pdf')->code());
    }

    public function testSkipEmptyZeroOrNull()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipEmptyZeroOrNullReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertEquals('filetype.skipped.empty-val', $v(null)->code());
        $this->assertEquals('filetype.skipped.empty-val', $v('')->code());
    }

    public function testFailInvalidTypeCheckTypeTrue()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_type' => true
// default
        ]);
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(new StdClass)->isValid());
        $this->assertFalse($v([1,2,3])->isValid());
    }

    public function testFailInvalidTypeCheckTypeTrueReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_type' => true
// default
        ]);
        $this->assertEquals('filetype.failure.invalid-type', $v(0)->code());
        $this->assertEquals('filetype.failure.invalid-type', $v(new StdClass)->code());
        $this->assertEquals('filetype.failure.invalid-type', $v([1,2,3])->code());
    }

    public function testSkipInvalidTypeCheckTypeFalse()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_type' => false
        ]);
        $this->assertTrue($v(0)->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
        $this->assertTrue($v([1,2,3])->isSkipped());
    }

    public function testSkipInvalidTypeCheckTypeFalseReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_type' => false
        ]);
        $this->assertEquals('filetype.skipped.invalid-type', $v(0)->code());
        $this->assertEquals('filetype.skipped.invalid-type', $v(new StdClass)->code());
        $this->assertEquals('filetype.skipped.invalid-type', $v([1,2,3])->code());
    }

    public function testFailInvalidFileCheckFileTrue()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_file' => true
// default
        ]);
        $this->assertFalse($v('foo')->isValid());
        $this->assertFalse($v(dirname(__FILE__).'/data')->isValid());
    }

    public function testFailInvalidFileCheckFileTrueReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_type' => true
// default
        ]);
        $this->assertEquals('filetype.failure.invalid-file', $v('foo')->code());
        $this->assertEquals('filetype.failure.invalid-file', $v(dirname(__FILE__).'/data')->code());
    }

    public function testSkipInvalidFileCheckFileFalse()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_file' => false
        ]);
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v(dirname(__FILE__).'/data')->isSkipped());
    }

    public function testSkipInvalidFileCheckFileFalseReturnCode()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain'],
            'check_file' => false
        ]);
        $this->assertEquals('filetype.skipped.invalid-file', $v('foo')->code());
        $this->assertEquals('filetype.skipped.invalid-file', $v(dirname(__FILE__).'/data')->code());
    }

    public function testInvalidAcceptedThrowsException()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $v = new FiletypeValidator([
            'accepted' => 0
        ]);
    }

    public function testSkipEmptyAccepted()
    {
        $v = new FiletypeValidator();
        $this->assertTrue($v(dirname(__FILE__).'/data/3bytes.txt')->isSkipped());
    }

    public function testSkipEmptyAcceptedReturnCode()
    {
        $v = new FiletypeValidator();
        $this->assertEquals('filetype.skipped.no-accepted', $v(dirname(__FILE__).'/data/3bytes.txt')->code());
    }
}
