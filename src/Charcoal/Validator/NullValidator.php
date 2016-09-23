<?php

namespace Charcoal\Validator;

use \Charcoal\Validator\AbstractValidator;
use \Charcoal\Validator\ValidationResult;

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
            $this->setRequireNull($data['require_null']);
        }
    }


    /**
     * @param string $requireNull The requireNull (multibytes) flag.
     * @return  void
     */
    private function setRequireNull($requireNull)
    {
        $this->requireNull = !!$requireNull;
    }

    /**
     * @return string
     */
    private function requireNull()
    {
        return $this->requireNull;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if ($this->requireNull()) {
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
            return $this->failure($val, 'null.is-not-null');
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
            return $this->failure($val, 'null.is-null');
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
            'null.is-null'      => 'The value can not be null.',
            'null.is-not-null'  => 'The value must be null.',
            'null.success.is-not-null'      => 'The value is not null.',
            'null.success.is-null'          => 'The value is null'
        ];
    }
}
