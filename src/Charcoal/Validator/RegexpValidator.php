<?php

namespace Charcoal\Validator;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 * Regexp validator
 */
class RegexpValidator extends AbstractValidator
{

    /**
     * @var string $pattern
     */
    private $pattern = '';

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['pattern'])) {
            $this->pattern = strval($data['pattern']);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if ($this->pattern === '') {
            return $this->skip($val, 'regexp.skipped.no-pattern');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'regexp.skipped.empty-val');
        }

        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->skip($val, 'regexp.skipped.invalid-type');
        }

        $val = (string)$val;

        $valid = !!preg_match($this->pattern, $val);

        if ($valid) {
            return $this->success($val, 'regexp.success');
        } else {
            return $this->failure($val, 'regexp.failure.no-match');
        }
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'regexp.failure.no-match'       => 'The value does not match the pattern.',
            'regexp.skipped.no-pattern'     => 'Regexp validation skipped, regexp pattern defined.',
            'regexp.skipped.empty-val'      => 'Regexp validation skipped, value is empty.',
            'regexp.skipped.invalid-type'   => 'Regexp validation skipped, value not a string.',
            'regexp.success'   => 'The value matches the pattern.'
        ];
    }
}
