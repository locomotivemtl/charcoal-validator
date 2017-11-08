<?php

namespace Charcoal\Validator;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 *
 */
class EmailValidator extends AbstractValidator
{
    /**
     * @var boolean
     */
    private $mx = false;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['mx'])) {
            $this->mx = !!$data['mx'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'email.skipped.empty-val');
        }

        if (is_string($val) === false) {
            return $this->failure($val, 'email.failure.invalid-type');
        }

        if ($this->validateEmail($val) === false) {
            return $this->failure($val, 'email.failure.invalid-email');
        }

        if ($this->validateMx($val) === false) {
            return $this->failure($val, 'email.failure.invalid-mx');
        }

        return $this->success($val, 'email.success');
    }


    /**
     * @param string $val The string to validate as email.
     * @return boolean
     */
    private function validateEmail($val)
    {
        return !!filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $val The string (email) to validate MX record.
     * @return boolean
     */
    private function validateMx($val)
    {
        if ($this->mx) {
            $host = substr($val, (strrpos($val, '@') + 1));
            return checkdnsrr($host, 'MX');
        } else {
            return true;
        }
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'email.failure.invalid-type'    => 'Invalid type. Emails must be string.',
            'email.failure.invalid-email'   => 'Not a valid email.',
            'email.failure.invalid-mx'      => 'Email does not have a valid MX record.',
            'email.skipped.empty-val'       => 'Email validation skipped (value is empty)',
            'email.success'                 => 'Email is valid.'
        ];
    }
}
