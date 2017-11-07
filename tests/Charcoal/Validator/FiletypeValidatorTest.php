<?php

namespace Charcoal\Tests\Validator;

use PHPUnit_Framework_TestCase;

use Charcoal\Validator\FiletypeValidator;

/**
 *
 */
class FiletypeValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValidator()
    {
        $v = new FiletypeValidator();
        $this->assertTrue($v('foo')->isValid());
        $this->assertTrue($v('')->isValid());
        $this->assertTrue($v('foo')->isSkipped());
        $this->assertTrue($v('')->isSkipped());
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

    public function testSkipEmptyZeroOrNull()
    {
        $v = new FiletypeValidator([
            'accepted' => ['image/png', 'text/plain']
        ]);
        $this->assertTrue($v(null)->isSkipped());
        $this->assertTrue($v('')->isSkipped());
        $this->assertTrue($v(0)->isSkipped());
    }
}
