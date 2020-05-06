<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use StdClass;
use Charcoal\Validator\FiletypeRule;

/**
 *
 */
class FiletypeRuleTest extends TestCase
{
    public function testAcceptedAsCommaSeparatedString()
    {
        $v = new FiletypeRule([
            'accepted' => 'image/png, text/plain'
        ]);
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/100x100.png')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/100x100.jpg')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/mimetype.pdf')->isValid());
    }

    public function testAccepted()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/100x100.png')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/100x100.jpg')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/mimetype.pdf')->isValid());
    }

    public function testAcceptedReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertEquals('filetype.success', $v(dirname(__FILE__) . '/data/3bytes.txt')->getCode());
        $this->assertEquals('filetype.success', $v(dirname(__FILE__) . '/data/100x100.png')->getCode());
        $this->assertEquals('filetype.failure.accepted', $v(dirname(__FILE__) . '/data/100x100.jpg')->getCode());
        $this->assertEquals('filetype.failure.accepted', $v(dirname(__FILE__) . '/data/mimetype.pdf')->getCode());
    }

    public function testSkipEmptyZeroOrNull()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipEmptyZeroOrNullReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertEquals('filetype.skipped.empty-val', $v(null)->getCode());
        $this->assertEquals('filetype.skipped.empty-val', $v('')->getCode());
    }

    public function testFailInvalidTypeCheckTypeTrue()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkType' => true
// default
        ]);
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(new StdClass())->isValid());
        $this->assertFalse($v([1,2,3])->isValid());
    }

    public function testFailInvalidTypeCheckTypeTrueReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkType' => true
// default
        ]);
        $this->assertEquals('filetype.failure.invalid-type', $v(0)->getCode());
        $this->assertEquals('filetype.failure.invalid-type', $v(new StdClass())->getCode());
        $this->assertEquals('filetype.failure.invalid-type', $v([1,2,3])->getCode());
    }

    public function testSkipInvalidTypeCheckTypeFalse()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkType' => false
        ]);
        $this->assertTrue($v(0)->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
        $this->assertTrue($v([1,2,3])->isSkipped());
    }

    public function testSkipInvalidTypeCheckTypeFalseReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkType' => false
        ]);
        $this->assertEquals('filetype.skipped.invalid-type', $v(0)->getCode());
        $this->assertEquals('filetype.skipped.invalid-type', $v(new StdClass())->getCode());
        $this->assertEquals('filetype.skipped.invalid-type', $v([1,2,3])->getCode());
    }

    public function testFailInvalidFileCheckFileTrue()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkFile' => true
// default
        ]);
        $this->assertFalse($v('foo')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data')->isValid());
    }

    public function testFailInvalidFileCheckFileTrueReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkType' => true
// default
        ]);
        $this->assertEquals('filetype.failure.invalid-file', $v('foo')->getCode());
        $this->assertEquals('filetype.failure.invalid-file', $v(dirname(__FILE__) . '/data')->getCode());
    }

    public function testSkipInvalidFileCheckFileFalse()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkFile' => false
        ]);
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v(dirname(__FILE__) . '/data')->isSkipped());
    }

    public function testSkipInvalidFileCheckFileFalseReturnCode()
    {
        $v = new FiletypeRule([
            'accepted' => ['image/png', 'text/plain'],
            'checkFile' => false
        ]);
        $this->assertEquals('filetype.skipped.invalid-file', $v('foo')->getCode());
        $this->assertEquals('filetype.skipped.invalid-file', $v(dirname(__FILE__) . '/data')->getCode());
    }

    public function testInvalidAcceptedThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $v = new FiletypeRule([
            'accepted' => 0
        ]);
    }

    public function testSkipEmptyAccepted()
    {
        $v = new FiletypeRule();
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isSkipped());
    }

    public function testSkipEmptyAcceptedReturnCode()
    {
        $v = new FiletypeRule();
        $this->assertEquals('filetype.skipped.no-accepted', $v(dirname(__FILE__) . '/data/3bytes.txt')->getCode());
    }
}
