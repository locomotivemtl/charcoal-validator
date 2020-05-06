<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
class EmailRule extends Rule
{
    /**
     * @var bool
     */
    private $mx = false;

    /**
     * @param array<string, mixed> $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['mx'])) {
            $this->mx = !!$data['mx'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip('email.skipped.empty-val');
        }

        if (is_string($val) === false) {
            return $this->failure('email.failure.invalid-type');
        }

        if ($this->validateEmail($val) === false) {
            return $this->failure('email.failure.invalid-email');
        }

        if ($this->validateMx($val) === false) {
            return $this->failure('email.failure.invalid-mx');
        }

        return $this->success('email.success');
    }


    /**
     * @param string $val The string to validate as email.
     * @return bool
     */
    private function validateEmail($val): bool
    {
        return !!filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $val The string (email) to validate MX record.
     * @return bool
     */
    private function validateMx($val): bool
    {
        if ($this->mx) {
            $host = substr($val, (strrpos($val, '@') + 1));
            return checkdnsrr($host, 'MX');
        } else {
            return true;
        }
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.failure.invalid-type' => 'Invalid type. Emails must be string.',
            'email.failure.invalid-email' => 'Not a valid email.',
            'email.failure.invalid-mx' => 'Email does not have a valid MX record.',
            'email.skipped.empty-val' => 'Email validation skipped (value is empty)',
            'email.success' => 'Email is valid.'
        ];
    }
}
