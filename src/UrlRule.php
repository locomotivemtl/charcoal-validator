<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
class UrlRule extends Rule
{
    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip('url.skipped.empty-val');
        }

        if (is_string($val) === false) {
            return $this->failure('url.failure.invalid-type');
        }

        if (filter_var($val, FILTER_VALIDATE_URL) === false) {
            return $this->failure('url.failure.invalid-url');
        }

        return $this->success('url.success');
    }


    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.failure.invalid-type' => 'Invalid type. URLs must be string.',
            'url.failure.invalid-url' => 'Not a valid URL.',
            'url.skipped.empty-val' => 'URL validation skipped (value is empty)',
            'url.success' => 'URL is valid.'
        ];
    }
}
