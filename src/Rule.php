<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
abstract class Rule implements RuleInterface
{
    /**
     * Every validator can be used as a function.
     *
     * @param mixed $val The value to test / validate.
     * @return Result
     */
    final public function __invoke($val): Result
    {
        return $this->process($val);
    }

    /**
     * @param mixed $val The value to test / validate.
     * @return Result
     */
    abstract public function process($val): Result;


    /**
     * Generate a Result object, with failure.
     *
     * @param string $code The validator code that failed.
     * @return Result
     */
    final protected function failure($code): Result
    {
        return new Result(
            Result::TYPE_FAILURE,
            $code,
            $this->getMessageForCode($code)
        );
    }

    /**
     * Generate a Result object, with success.
     *
     * @param string $code The validator code that succeeded.
     * @return Result
     */
    final protected function success($code): Result
    {
        return new Result(
            Result::TYPE_SUCCESS,
            $code,
            $this->getMessageForCode($code)
        );
    }

    /**
     * Generate a skipped result test.
     *
     * Skip results are better left blank.
     *
     * @param string $code The validator code that was skipped.
     * @return Result
     */
    final protected function skip($code): Result
    {
        return new Result(
            Result::TYPE_SKIPPED,
            $code,
            $this->getMessageForCode($code)
        );
    }

    /**
     * @return string[]
     */
    abstract protected function messages(): array;

    /**
     * @param string $code The validator code to retrieve the message from.
     * @return string
     * @see self:messages()
     */
    private function getMessageForCode($code): string
    {
        $messages = $this->messages();
        if (isset($messages[$code])) {
            return $messages[$code];
        } else {
            return '';
        }
    }
}
