<?php

namespace Charcoal\Validator;

use \Charcoal\Validator\AbstractValidator;
use \Charcoal\Validator\ValidationResult;

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
            $this->setPattern($data['pattern']);
        }
    }

    /**
     * @param string $pattern The validation regular expression.
     * @return StringValidator Chainable
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return string
     */
    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidatorResult
     */
    public function validate($val)
    {
        if (!$this->pattern()) {
            return $this->skip($val, 'regexp.skipped.no-pattern');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'regexp.skipped.empty-val');
        }

        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->skip('regexp.skipped.invalid-type');
        }

        $val = (string)$val;

        $valid = !!preg_match($this->pattern(), $val);

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
            'length.skipped.no-min-max'     => 'Regexp validation skipped, no min or max defined.',
            'length.skipped.empty-val'      => 'Regexp validation skipped, value is empty.',
            'length.skipped.invalid-type'   => 'Regexp validation skipped, value not a string.',
            'regexp.success'   => 'The value matches the pattern.'
        ];
    }
}
