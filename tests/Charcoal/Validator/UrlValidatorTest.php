<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\UrlValidator;

/**
 *
 */
class UrlValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataValidUrls
     */
    public function testValidUrls($email)
    {
        $v = new UrlValidator();
        $this->assertTrue($v($email)->isValid());
    }

    /**
     * @dataProvider dataValidUrls
     */
    public function testValidUrlsReturnCode($email)
    {
        $v = new UrlValidator();
        $this->assertEquals('url.success', $v($email)->code());
    }

    /**
     * @dataProvider dataInvalidUrls
     */
    public function testInvalidUrls($email)
    {
        $v = new UrlValidator();
        $this->assertFalse($v($email)->isValid());
    }

    /**
     * @dataProvider dataInvalidUrls
     */
    public function testInvalidUrlsReturnCode($email)
    {
        $v = new UrlValidator();
        $this->assertEquals('url.failure.invalid-url', $v($email)->code());
    }


    public function testEmptyEmailSkipped()
    {
        $v = new UrlValidator();
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testEmptyEmailSkippedReturnCode()
    {
        $v = new UrlValidator();
        $this->assertEquals('url.skipped.empty-val', $v('')->code());
        $this->assertEquals('url.skipped.empty-val', $v(null)->code());
    }

    public function testInvalidEmailTypeFails()
    {
        $v = new UrlValidator();
        $this->assertFalse($v([])->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testInvalidEmailTypeFailsReturnCode()
    {
        $v = new UrlValidator();
        $this->assertEquals('url.failure.invalid-type', $v([])->code());
        $this->assertEquals('url.failure.invalid-type', $v(1)->code());
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
