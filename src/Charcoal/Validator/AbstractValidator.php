<?php

namespace Charcoal\Validator;

use Charcoal\Validator\ValidationResult;
use Charcoal\Validator\ValidatorInterface;

/**
 *
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @param mixed $val The value to test / validate.
     * @return ValidationResult
     */
    abstract public function validate($val);

    /**
     * @return string[]
     */
    abstract protected function messages();

    /**
     * Every validator can be used as a function.
     *
     * @param mixed $val The value to test / validate.
     * @return ValidationResult
     */
    public function __invoke($val)
    {
        return $this->validate($val);
    }

    /**
     * @param string $code The validator code to retrieve the message from.
     * @see self:messages()
     * @return string
     */
    protected function message($code)
    {
        $messages = $this->messages();
        if (isset($messages[$code])) {
            return $messages[$code];
        } else {
            return '';
        }
    }

    /**
     * Generate a Result object, with failure.
     *
     * @param mixed  $value The value that failed.
     * @param string $code  The validator code that failed.
     * @return ValidationResult
     */
    protected function failure($value, $code)
    {
        return new ValidationResult([
            'value'     => $value,
            'type'      => ValidationResult::TYPE_FAILURE,
            'code'      => $code,
            'message'   => $this->message($code)
        ]);
    }

    /**
     * Generate a Result object, with success.
     *
     * @param mixed  $value The value that succeeded.
     * @param string $code  The validator code that succeeded.
     * @return ValidationResult
     */
    protected function success($value, $code)
    {
        return new ValidationResult([
            'value'     => $value,
            'type'      => ValidationResult::TYPE_SUCCESS,
            'code'      => $code,
            'message'   => $this->message($code)
        ]);
    }

    /**
     * Generate a skipped result test.
     *
     * Skip results are better left blank.
     *
     * @param mixed  $value The value that was skipped.
     * @param string $code  The validator code that was skipped.
     * @return ValidationResult
     */
    protected function skip($value, $code)
    {
        return new ValidationResult([
            'value'     => $value,
            'type'      => ValidationResult::TYPE_SKIPPED,
            'code'      => $code,
            'message'   => $this->message($code)
        ]);
    }
}
