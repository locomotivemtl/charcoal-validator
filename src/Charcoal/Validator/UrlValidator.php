<?php

namespace Charcoal\Validator;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 *
 */
class UrlValidator extends AbstractValidator
{

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'url.skipped.empty-val');
        }

        if (is_string($val) === false) {
            return $this->failure($val, 'url.failure.invalid-type');
        }

        if (filter_var($val, FILTER_VALIDATE_URL) === false) {
            return $this->failure($val, 'url.failure.invalid-url');
        }

        return $this->success($val, 'url.success');
    }


    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'url.failure.invalid-type'  => 'Invalid type. URLs must be string.',
            'url.failure.invalid-url'   => 'Not a valid URL.',
            'url.skipped.empty-val'     => 'URL validation skipped (value is empty)',
            'url.success'               => 'URL is valid.'
        ];
    }
}
