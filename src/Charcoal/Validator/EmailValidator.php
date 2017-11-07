<?php

namespace Charcoal\Validator;

use Charcoal\Validator\AbstractValidator;

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
            $this->setMx($data['mx']);
        }
    }

    /**
     * @param boolean $mx The MX (check MX record) flag.
     * @return  void
     */
    private function setMx($mx)
    {
        $this->mx = !!$mx;
    }

    /**
     * @return boolean
     */
    private function mx()
    {
        return $this->mx;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {

        // Null values and empty strings should be handled by different validators.
        if ($this->isValueEmpty($val) === true) {
            return $this->skip($val, 'email.skipped.empty-val');
        }

        if ($this->isValueValid($val) === false) {
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
     * @param mixed $val The value to check for emptiness.
     * @return boolean
     */
    private function isValueEmpty($val)
    {
        if ($val === null || $val === '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $val The value to check as valid type.
     * @return boolean
     */
    private function isValueValid($val)
    {
        return is_string($val);
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
        if ($this->mx()) {
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
