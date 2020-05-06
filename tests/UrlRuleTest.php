<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Charcoal\Validator\UrlRule;

/**
 *
 */
class UrlRuleTest extends TestCase
{
    /**
     * @dataProvider dataValidUrls
     */
    public function testValidUrls($email)
    {
        $v = new UrlRule();
        $this->assertTrue($v($email)->isValid());
    }

    /**
     * @dataProvider dataValidUrls
     */
    public function testValidUrlsReturnCode($email)
    {
        $v = new UrlRule();
        $this->assertEquals('url.success', $v($email)->getCode());
    }

    /**
     * @dataProvider dataInvalidUrls
     */
    public function testInvalidUrls($email)
    {
        $v = new UrlRule();
        $this->assertFalse($v($email)->isValid());
    }

    /**
     * @dataProvider dataInvalidUrls
     */
    public function testInvalidUrlsReturnCode($email)
    {
        $v = new UrlRule();
        $this->assertEquals('url.failure.invalid-url', $v($email)->getCode());
    }


    public function testEmptyEmailSkipped()
    {
        $v = new UrlRule();
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testEmptyEmailSkippedReturnCode()
    {
        $v = new UrlRule();
        $this->assertEquals('url.skipped.empty-val', $v('')->getCode());
        $this->assertEquals('url.skipped.empty-val', $v(null)->getCode());
    }

    public function testInvalidEmailTypeFails()
    {
        $v = new UrlRule();
        $this->assertFalse($v([])->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testInvalidEmailTypeFailsReturnCode()
    {
        $v = new UrlRule();
        $this->assertEquals('url.failure.invalid-type', $v([])->getCode());
        $this->assertEquals('url.failure.invalid-type', $v(1)->getCode());
    }

    public function dataValidUrls()
    {
        return [
            ['http://example.com'],
            ['https://example.com/'],
            ['https://subdomain.example.com/subolder/file.extension?query=1&x=foo'],
            ['https://username:password@subdomain.example.com/subfolder/file.extension?query=1&x=foo'],
            ['protocol://example.com/file.ext']
        ];
    }

    public function dataInvalidUrls()
    {
        return [
            ['notadomain'],
            ['http:/missingslash.com']
        ];
    }
}
