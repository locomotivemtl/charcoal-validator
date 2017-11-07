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
        if ($val === null || $val === '') {
            return $this->skip($val, 'email.skipped.empty-val');
        }

        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->failure($val, 'email.failure.invalid-type');
        }

        if (!is_string($val)) {
            $val = (string)$val;
        }

        $emailValid = filter_var($val, FILTER_VALIDATE_EMAIL);
        if (!$emailValid) {
            return $this->failure($val, 'email.failure.invalid-email');
        }

        if ($this->mx()) {
            $host = substr($val, (strrpos($val, '@') + 1));
            $mxValid = checkdnsrr($host, 'MX');
            if (!$mxValid) {
                return $this->failure($val, 'email.failure.invalid-mx');
            }
        }

        return $this->success($val, 'email.success');
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
