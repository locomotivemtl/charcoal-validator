<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
interface RuleInterface
{
    /**
     * Every validator can be used as a function.
     *
     * @param mixed $val The value to test / validate.
     * @return Result
     */
    public function __invoke($val): Result;

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result;
}
