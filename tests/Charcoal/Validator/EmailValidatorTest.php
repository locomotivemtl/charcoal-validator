<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\EmailValidator;

/**
 *
 */
class EmailValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new EmailValidator();
        $this->assertTrue($v('mat@locomotive.ca')->isValid());
        $this->assertTrue($v('foo@gmail.com')->isValid());
        $this->assertFalse($v('foo')->isValid());
        $this->assertFalse($v('foo@bar')->isValid());

        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v(null)->isValid());
    }

    public function testEmptyEmailSkipped()
    {
        $v = new EmailValidator();
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(null)->isSkipped());
    }

    public function testInvalidEmailTypeFails()
    {
        $v = new EmailValidator();
        $this->assertFalse($v([])->isValid());
        $this->assertFalse($v(1)->isValid());
    }

    public function testMX()
    {
        $v = new EmailValidator([
            'mx' => true
        ]);
        $this->assertTrue($v('foo@gmail.com')->isValid());
        $this->assertFalse($v('foo@invalidmx.invalid')->isValid());
    }
}
