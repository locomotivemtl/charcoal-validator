<?php

declare(strict_types=1);

namespace Charcoal\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Charcoal\Validator\Result;

/**
 *
 */
class ResultTest extends TestCase
{
    /**
     *
     */
    public function testJsonSerialize()
    {
        $r = new Result(
            Result::TYPE_FAILURE,
            'foo',
            'foobar'
        );

        $json = json_encode($r);
        $arr = json_decode($json, true);

        $this->assertEquals($arr['type'], Result::TYPE_FAILURE);
        $this->assertEquals($arr['code'], 'foo');
        $this->assertEquals($arr['message'], 'foobar');
    }
}
