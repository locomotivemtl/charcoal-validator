<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Charcoal\Validator\EmailRule;

/**
 *
 */
class EmailRuleTest extends TestCase
{
    /**
     * @dataProvider dataValidEmails
     */
    public function testValidEmails($email)
    {
        $v = new EmailRule();
        $this->assertTrue($v($email)->isValid());
    }

    /**
     * @dataProvider dataValidEmails
     */
    public function testValidEmailsReturnCode($email)
    {
        $v = new EmailRule();
        $this->assertEquals('email.success', $v($email)->getCode());
    }

    /**
     * @dataProvider dataInvalidEmails
     */
    public function testInvalidEmails($email)
    {
        $v = new EmailRule();
        $this->assertFalse($v($email)->isValid());
    }

    /**
     * @dataProvider dataInvalidEmails
     */
    public function testInvalidEmailsReturnCode($email)
    {
        $v = new EmailRule();
        $this->assertEquals('email.failure.invalid-email', $v($email)->getCode());
    }


    public function testEmptyEmailSkipped()
    {
        $v = new EmailRule();
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testEmptyEmailSkippedReturnCode()
    {
        $v = new EmailRule();
        $this->assertEquals('email.skipped.empty-val', $v('')->getCode());
        $this->assertEquals('email.skipped.empty-val', $v(null)->getCode());
    }

    public function testInvalidEmailTypeFails()
    {
        $v = new EmailRule();
        $this->assertFalse($v([])->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testInvalidEmailTypeFailsReturnCode()
    {
        $v = new EmailRule();
        $this->assertEquals('email.failure.invalid-type', $v([])->getCode());
        $this->assertEquals('email.failure.invalid-type', $v(1)->getCode());
    }

    public function testMx()
    {
        $v = new EmailRule(
            [
                'mx' => true
            ]
        );
        $this->assertTrue($v('foo@gmail.com')->isValid());
        $this->assertFalse($v('foo@invalidmx.invalid')->isValid());
    }

    public function testMxReturnCode()
    {
        $v = new EmailRule(
            [
                'mx' => true
            ]
        );
        $this->assertEquals('email.success', $v('foo@gmail.com')->getCode());
        $this->assertEquals('email.failure.invalid-mx', $v('foo@invalidmx.invalid')->getCode());
    }

    public function dataValidEmails()
    {
        return [
            ['email@domain.com'],
// Simple email
            ['email@domain.museum'],
// Long tld, such as museum, are accepted
            //['email@localdomain'],            // Local domains are not supported with PHP's filter_var
            ['firstname.lastname@domain.com'],
// Dots in the middle of the username are supported
            ['firstname+lastname@domain.com'],
// + are supported in username
            ['firstname.lastname@subdomain.domain.co.com'],
// Subdomains and sub-tlds are supported
            //['email@123.123.123.123'],        // PHP's filter_var wants IP domain enclosed in [square brackets]
            ['email@[123.123.123.123]'],
// Ip domain can be set, if enclosed in [square brackets]
            ['"email"@domain.com'],
// The username part of the email can be in quotes
            ['1234567890@domain.com'],
// Username can be entirely made of numbers
            ['emain@domain-x.com'],
// Support dashes in domain
            ['____@domain.com']
// Username can be entirely made of underscores
        ];
    }

    public function dataInvalidEmails()
    {
        return [
            ['notanemail'],
// Just a string, not an email
            ['email@domain#.com'],
// Invalid character in domain
            ['#@%^%#$@#$@#.com'],
// Garbage
            ['@domain.com'],
// No username
            ['email@'],
// No domain
            ['email.domain.com'],
// Missing username@ part
            ['email@domain@domain.com'],
// Two @s
            ['.email@domain.com'],
// Leading dot in username
            ['email.@domain.com'],
// Trailing dot in username
            ['email@.domain.com'],
// Leading dot in domain
            ['email@domain.com.'],
// Trailing dot in domain
            ['firstname last@domain.com'],
// Space in username
            ['email..email@domain.com'],
// Two dots in username
            ['(garbage, then) email@domain.com'],
// Garbage before email
            ['email@domain.com (then garbage)'],
// Garbage after email
            ['電子メール@domain.com'],
// Invalid unicode characters in username
            ['email@111.222.333.44444'],
// Invalid IPs
            ['email@domain..com'],
// Tow dots in domain,
            ['<script src="http://malicious"></script>@domain.com'],
// XSS

            // Should be valid?
            ['root@localhost'],
// Local domains are not supported with PHP's filter_var
            ['email@123.123.123.123']
// PHP's filter_var wants IP domain enclosed in [square brackets]
        ];
    }
}
