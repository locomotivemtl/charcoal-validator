<?php

namespace Charcoal\Validator;

use \Charcoal\Validator\AbstractValidator;
use \Charcoal\Validator\ValidationResult;

/**
 * Ensures a value is not empty.
 * Can also ensures a value *is* empty with `require_empty` set to true.
 */
class EmptyValidator extends AbstractValidator
{
    /**
     * @var boolean
     */
    private $requireEmpty = false;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['require_empty'])) {
            $this->setRequireEmpty($data['require_empty']);
        }
    }


    /**
     * @param string $requireEmpty The requireEmpty (multibytes) flag.
     * @return  void
     */
    private function setRequireEmpty($requireEmpty)
    {
        $this->requireEmpty = !!$requireEmpty;
    }

    /**
     * @return string
     */
    private function requireEmpty()
    {
        return $this->requireEmpty;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if ($this->requireEmpty()) {
            return $this->validateIsEmpty($val);
        } else {
            return $this->validateIsNotEmpty($val);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    protected function validateIsEmpty($val)
    {
        if (!empty($val)) {
            return $this->failure($val, 'empty.failure.is-not-empty');
        } else {
            return $this->success($val, 'empty.success.is-empty');
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    protected function validateIsNotEmpty($val)
    {
        if (empty($val)) {
            return $this->failure($val, 'empty.failure.is-empty');
        } else {
            return $this->success($val, 'empty.success.is-not-empty');
        }
    }

    /**
     * @return array
     */
    protected function messages()
    {
        return [
            'empty.failure.is-empty'      => 'The value can not be empty.',
            'empty.failure.is-not-empty'  => 'The value must be empty.',
            'empty.success.is-not-empty'      => 'The value is not empty.',
            'empty.success.is-empty'          => 'The value is empty'
        ];
    }
}
