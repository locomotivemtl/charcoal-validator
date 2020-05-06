<?php

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use StdClass;
use Charcoal\Validator\FilesizeRule;

/**
 *
 */
class FilesizeRuleTest extends TestCase
{
    public function testMin()
    {
        $v = new FilesizeRule(
            [
                'min' => 4,
                'max' => null
            ]
        );
        $this->assertFalse($v(dirname(__FILE__) . '/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/4bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/5bytes.txt')->isValid());
    }

    public function testMinReturnCode()
    {
        $v = new FilesizeRule(
            [
                'min' => 4
            ]
        );
        $this->assertEquals('filesize.failure.min', $v(dirname(__FILE__) . '/data/3bytes.txt')->getCode());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__) . '/data/4bytes.txt')->getCode());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__) . '/data/5bytes.txt')->getCode());
    }

    public function testMax()
    {
        $v = new FilesizeRule(
            [
                'min' => null,
                'max' => 4
            ]
        );
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/4bytes.txt')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/5bytes.txt')->isValid());
    }

    public function testMaxReturnCode()
    {
        $v = new FilesizeRule(
            [
                'max' => 4
            ]
        );
        $this->assertEquals('filesize.success', $v(dirname(__FILE__) . '/data/3bytes.txt')->getCode());
        $this->assertEquals('filesize.success', $v(dirname(__FILE__) . '/data/4bytes.txt')->getCode());
        $this->assertEquals('filesize.failure.max', $v(dirname(__FILE__) . '/data/5bytes.txt')->getCode());
    }


    public function testMinMax()
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertFalse($v(dirname(__FILE__) . '/data/2bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isValid());
        $this->assertTrue($v(dirname(__FILE__) . '/data/4bytes.txt')->isValid());
        $this->assertFalse($v(dirname(__FILE__) . '/data/5bytes.txt')->isValid());
        ;
    }

    public function testSkipEmptyOrNull(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
    }

    public function testSkipEmptyOrNullReturnCode(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4
            ]
        );
        $this->assertEquals('filesize.skipped.empty-val', $v(null)->getCode());
        $this->assertEquals('filesize.skipped.empty-val', $v('')->getCode());
    }

    public function testInvalidTypeCheckTypeTrue(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4,
                'checkType' => true
            ]
        );
        $this->assertFalse($v([1, 2, 3])->isValid());
        $this->assertFalse($v(new StdClass())->isValid());
        $this->assertFalse($v(0)->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testInvalidFileCheckFileTrue(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4,
                'checkFile' => true
            ]
        );

        $this->assertFalse($v('foo')->isValid());
    }

    public function testInvalidTypeCheckTypeFalse(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4,
                'checkType' => false
            ]
        );
        $this->assertTrue($v([1, 2, 3])->isSkipped());
        $this->assertTrue($v(new StdClass())->isSkipped());
        $this->assertTrue($v(0)->isSkipped());
        $this->assertTrue($v(1)->isSkipped());
    }

    public function testInvalidFileCheckFileFalse(): void
    {
        $v = new FilesizeRule(
            [
                'min' => 3,
                'max' => 4,
                'checkFile' => true
            ]
        );

        $this->assertFalse($v('foo')->isValid());
    }

    public function testInvalidTypeCheckTypeTrueReturnCode(): void
    {
        $v = new FilesizeRule(
            [
                'min' => '10k',
                'max' => '200M',
                'checkType' => true
            ]
        );
        $this->assertEquals('filesize.failure.invalid-type', $v([1, 2, 3])->getCode());
        $this->assertEquals('filesize.failure.invalid-type', $v(new StdClass())->getCode());
        $this->assertEquals('filesize.failure.invalid-type', $v(0)->getCode());
        $this->assertEquals('filesize.failure.invalid-type', $v(1)->getCode());
    }

    public function testInvalidTypeCheckTypeFalseReturnCode(): void
    {
        $v = new FilesizeRule(
            [
                'min' => '100k',
                'max' => '200M',
                'checkType' => false
            ]
        );
        $this->assertEquals('filesize.skipped.invalid-type', $v([1, 2, 3])->getCode());
        $this->assertEquals('filesize.skipped.invalid-type', $v(new StdClass())->getCode());
        $this->assertEquals('filesize.skipped.invalid-type', $v(0)->getCode());
        $this->assertEquals('filesize.skipped.invalid-type', $v(1)->getCode());
    }

    public function testInvalidFileCheckFileTrueReturnCode(): void
    {
        $v = new FilesizeRule(
            [
                'min' => '10k',
                'max' => '200M',
                'checkFile' => true
            ]
        );
        $this->assertEquals('filesize.failure.invalid-file', $v('foo')->getCode());
    }

    public function testInvalidFileCheckFileFalseReturnCode()
    {
        $v = new FilesizeRule(
            [
                'min' => '10k',
                'max' => '200M',
                'checkFile' => false
            ]
        );
        $this->assertEquals('filesize.skipped.invalid-file', $v('foo')->getCode());
    }

    public function testSkipNoMinMax()
    {
        $v = new FilesizeRule();
        $this->assertTrue($v(dirname(__FILE__) . '/data/3bytes.txt')->isSkipped());
    }

    public function testSkipNoMinMaxReturnCode()
    {
        $v = new FilesizeRule();
        $this->assertEquals('filesize.skipped.no-min-max', $v(dirname(__FILE__) . '/data/3bytes.txt')->getCode());
    }
}
