<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 * Regexp validator
 */
class PatternRule extends Rule
{

    /**
     * @var string $pattern
     */
    private $pattern = '';

    /**
     * @param array<string, mixed> $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['pattern'])) {
            $this->pattern = strval($data['pattern']);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        if ($this->pattern === '') {
            return $this->skip('pattern.skipped.no-pattern');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip('pattern.skipped.empty-val');
        }

        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->skip('pattern.skipped.invalid-type');
        }

        $val = (string)$val;

        $valid = !!preg_match($this->pattern, $val);

        if ($valid) {
            return $this->success('pattern.success');
        } else {
            return $this->failure('pattern.failure.no-match');
        }
    }

    /**
     * @return string[]
     */
    protected function messages(): array
    {
        return [
            'pattern.failure.no-match' => 'The value does not match the pattern.',
            'pattern.skipped.no-pattern' => 'Regexp validation skipped, pattern pattern defined.',
            'pattern.skipped.empty-val' => 'Regexp validation skipped, value is empty.',
            'pattern.skipped.invalid-type' => 'Regexp validation skipped, value not a string.',
            'pattern.success' => 'The value matches the pattern.'
        ];
    }
}
