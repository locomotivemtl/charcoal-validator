<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use Charcoal\Validator\Validation;
use PHPUnit\Framework\TestCase;
use Charcoal\Validator\Validator;
use Charcoal\Validator\Result;
use Charcoal\Validator\NullRule;
use Charcoal\Validator\LengthRule;

/**
 *
 */
class ValidatorTest extends TestCase
{
    public function testEmptyConstructor()
    {
        $r = new Validator([]);
        $res = $r(null);
        $this->assertInstanceOf(Validation::class, $res);
        $this->assertTrue($res->isValid());
    }

    public function testSuccess()
    {
        $r = new Validator([new NullRule()]);
        $res = $r('foo');
        $this->assertInstanceOf(Validation::class, $res);
        $this->assertTrue($res->isValid());
    }
    public function testFailure()
    {
        $r = new Validator([new NullRule()]);
        $res = $r(null);
        $this->assertInstanceOf(Validation::class, $res);
        $this->assertFalse($res->isValid());
    }
//
//    public function testFailedRules()
//    {
//        $r = new Validator(
//            [new NullRule()],
//            [new NullRule()],
//            [new NullRule()]
//        );
//        $res = $r->validate(null);
//        $this->assertSame($res, $r->results());
//
//        $this->assertFalse($r->isValid());
//
//        $this->assertFalse($r->isValid(Validator::LEVEL_INFO));
//        $this->assertNotEmpty($r->errors());
//        $this->assertCount(1, $r->errors());
//        $this->assertContainsOnlyInstancesOf(Result::class, $r->errors());
//
//        $this->assertFalse($r->isValid(Validator::LEVEL_WARNING));
//        $this->assertNotEmpty($r->warnings());
//        $this->assertCount(1, $r->errors());
//        $this->assertContainsOnlyInstancesOf(Result::class, $r->warnings());
//
//        $this->assertFalse($r->isValid(Validator::LEVEL_ERROR));
//        $this->assertNotEmpty($r->infos());
//        $this->assertCount(1, $r->errors());
//        $this->assertContainsOnlyInstancesOf(Result::class, $r->infos());
//    }
//
//    /**
//     *
//     */
//    public function testSkippedRules()
//    {
//        // All tests will be skipped because there's no min / max defined...
//        $r = new Validator(
//            [new LengthRule()],
//            [new LengthRule()],
//            [new LengthRule()]
//        );
//
//        $res = $r->validate('foo');
//        $this->assertSame($res, $r->results());
//
//        $this->assertTrue($r->isValid());
//
//        $this->assertEquals([], $r->errors());
//        $this->assertEquals([], $r->warnings());
//        $this->assertEquals([], $r->infos());
//
//        $returnSkipped = true;
//        $returnValid = false;
//        $this->assertCount(1, $r->errors($returnSkipped, $returnValid));
//        $this->assertCount(1, $r->warnings($returnSkipped, $returnValid));
//        $this->assertCount(1, $r->infos($returnSkipped, $returnValid));
//    }
//
//    public function testValidRules()
//    {
//        $r = new Validator(
//            [new NullRule()],
//            [new NullRule()],
//            [new NullRule()]
//
//        );
//        $res = $r->validate('foobar');
//        $this->assertSame($res, $r->results());
//
//        $this->assertTrue($r->isValid());
//
//        $this->assertTrue($r->isValid(Validator::LEVEL_ERROR));
//        $this->assertTrue($r->isValid(Validator::LEVEL_WARNING));
//        $this->assertTrue($r->isValid(Validator::LEVEL_INFO));
//
//        $this->assertEquals([], $r->errors());
//        $this->assertEquals([], $r->warnings());
//        $this->assertEquals([], $r->infos());
//
//        $returnSkipped = false;
//        $returnValid = true;
//        $this->assertCount(1, $r->errors($returnSkipped, $returnValid));
//        $this->assertCount(1, $r->warnings($returnSkipped, $returnValid));
//        $this->assertCount(1, $r->infos($returnSkipped, $returnValid));
//    }
}
