<?php

namespace Charcoal\Tests\Validator;

use \PHPUnit_Framework_TestCase;

use \Charcoal\Validator\ValidationRunner;
use \Charcoal\Validator\ValidationResult;
use \Charcoal\Validator\NullValidator;
use \Charcoal\Validator\LengthValidator;

/**
 *
 */
class ValidationRunnerTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyConstructor()
    {
        $r = new ValidationRunner([]);
        $res = $r->validate(null);
        $this->assertSame($res, $r->results());
        $this->assertEquals([], $r->results());

        $this->assertTrue($r->isValid());
        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_ERROR));
        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_WARNING));
        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_INFO));

        $this->assertEquals([], $r->errors());
        $this->assertEquals([], $r->warnings());
        $this->assertEquals([], $r->infos());

        $returnSkipped = true;
        $this->assertEquals([], $r->errors($returnSkipped));
        $this->assertEquals([], $r->warnings($returnSkipped));
        $this->assertEquals([], $r->infos($returnSkipped));

        $returnValid = true;
        $this->assertEquals([], $r->errors($returnSkipped, $returnValid));
        $this->assertEquals([], $r->warnings($returnSkipped, $returnValid));
        $this->assertEquals([], $r->infos($returnSkipped, $returnValid));
    }

    public function testFailedValidators()
    {
        $r = new ValidationRunner([
            ValidationRunner::LEVEL_ERROR => [
                new NullValidator()
            ],
            ValidationRunner::LEVEL_WARNING => [
                new NullValidator()
            ],
            ValidationRunner::LEVEL_INFO => [
                new NullValidator()
            ]
        ]);
        $res = $r->validate(null);
        $this->assertSame($res, $r->results());

        $this->assertFalse($r->isValid());

        $this->assertFalse($r->isValid(ValidationRunner::LEVEL_INFO));
        $this->assertNotEmpty($r->errors());
        $this->assertCount(1, $r->errors());
        $this->assertContainsOnlyInstancesOf(ValidationResult::class, $r->errors());

        $this->assertFalse($r->isValid(ValidationRunner::LEVEL_WARNING));
        $this->assertNotEmpty($r->warnings());
        $this->assertCount(1, $r->errors());
        $this->assertContainsOnlyInstancesOf(ValidationResult::class, $r->warnings());

        $this->assertFalse($r->isValid(ValidationRunner::LEVEL_ERROR));
        $this->assertNotEmpty($r->infos());
        $this->assertCount(1, $r->errors());
        $this->assertContainsOnlyInstancesOf(ValidationResult::class, $r->infos());
    }

    /**
     *
     */
    public function testSkippedValidators()
    {
        // All tests will be skipped because there's no min / max defined...
        $r = new ValidationRunner([
            ValidationRunner::LEVEL_ERROR => [
                new LengthValidator()
            ],
            ValidationRunner::LEVEL_WARNING => [
                new LengthValidator()
            ],
            ValidationRunner::LEVEL_INFO => [
                new LengthValidator()
            ]
        ]);

        $res = $r->validate('foo');
        $this->assertSame($res, $r->results());

        $this->assertTrue($r->isValid());

        $this->assertEquals([], $r->errors());
        $this->assertEquals([], $r->warnings());
        $this->assertEquals([], $r->infos());

        $returnSkipped = true;
        $returnValid = false;
        $this->assertCount(1, $r->errors($returnSkipped, $returnValid));
        $this->assertCount(1, $r->warnings($returnSkipped, $returnValid));
        $this->assertCount(1, $r->infos($returnSkipped, $returnValid));
    }

    public function testValidValidators()
    {
        $r = new ValidationRunner([
            ValidationRunner::LEVEL_ERROR => [
                new NullValidator()
            ],
            ValidationRunner::LEVEL_WARNING => [
                new NullValidator()
            ],
            ValidationRunner::LEVEL_INFO => [
                new NullValidator()
            ]
        ]);
        $res = $r->validate('foobar');
        $this->assertSame($res, $r->results());

        $this->assertTrue($r->isValid());

        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_ERROR));
        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_WARNING));
        $this->assertTrue($r->isValid(ValidationRunner::LEVEL_INFO));

        $this->assertEquals([], $r->errors());
        $this->assertEquals([], $r->warnings());
        $this->assertEquals([], $r->infos());

        $returnSkipped = false;
        $returnValid = true;
        $this->assertCount(1, $r->errors($returnSkipped, $returnValid));
        $this->assertCount(1, $r->warnings($returnSkipped, $returnValid));
        $this->assertCount(1, $r->infos($returnSkipped, $returnValid));
    }
}
