<?php

namespace Charcoal\Validator;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 * Ensures a value is not null.
 * Can also ensures a value *is* null with `require_null` set to true.
 */
class NullValidator extends AbstractValidator
{
    /**
     * @var boolean
     */
    private $requireNull = false;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['require_null'])) {
            $this->requireNull = !!$data['require_null'];
        }
    }


    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if ($this->requireNull) {
            return $this->validateIsNull($val);
        } else {
            return $this->validateIsNotNull($val);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    protected function validateIsNull($val)
    {
        if ($val !== null) {
            return $this->failure($val, 'null.failure.is-not-null');
        } else {
            return $this->success($val, 'null.success.is-null');
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    protected function validateIsNotNull($val)
    {
        if ($val === null) {
            return $this->failure($val, 'null.failure.is-null');
        } else {
            return $this->success($val, 'null.success.is-not-null');
        }
    }

    /**
     * @return array
     */
    protected function messages()
    {
        return [
            'null.failure.is-null'      => 'The value can not be null.',
            'null.failure.is-not-null'  => 'The value must be null.',
            'null.success.is-not-null'      => 'The value is not null.',
            'null.success.is-null'          => 'The value is null'
        ];
    }
}
