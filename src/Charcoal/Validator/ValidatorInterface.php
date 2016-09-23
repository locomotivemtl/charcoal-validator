<?php

namespace Charcoal\Validator;

/**
 *
 */
interface ValidatorInterface
{
    /**
     * @param mixed $val The value to validate.
     * @return \Charcoal\Validator\ValidationResult
     */
    public function validate($val);

    /**
     * Every validator can be used as a function.
     *
     * @param mixed $val The value to test / validate.
     * @return \Charcoal\Validator\ValidationResult
     */
    public function __invoke($val);
}
