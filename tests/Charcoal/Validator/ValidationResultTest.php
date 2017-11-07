<?php

namespace Charcoal\Tests\Validator;

use \PHPUnit_Framework_TestCase;

use \Charcoal\Validator\ValidationResult;

/**
 *
 */
class ValidationResultTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testJsonSerialize()
    {
        $r = new ValidationResult([
            'type' => ValidationResult::TYPE_FAILURE,
            'code' => 'foo',
            'message' => 'foobar',
            'value' => 'test'
        ]);

        $json = json_encode($r);
        $arr = json_decode($json, true);

        $this->assertEquals($arr['type'], ValidationResult::TYPE_FAILURE);
        $this->assertEquals($arr['code'], 'foo');
        $this->assertEquals($arr['message'], 'foobar');
        $this->assertEquals($arr['value'], 'test');
    }

    public function testConstructorInvalidTsThrowsException()
    {
        $this->setExpectedException('\Exception');
        $r = new ValidationResult([
            'ts' => false
        ]);
    }
}
