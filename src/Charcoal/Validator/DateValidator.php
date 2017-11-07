<?php

namespace Charcoal\Validator;

// PHP dependencies
use Exception;
use InvalidArgumentException;
use DateTimeInterface;
use DateTime;

// Local depenencies
use Charcoal\Validator\AbstractValidator;
use Charcoal\Validator\ValidationResult;

/**
 * Length validator ensures a string is of a certain length.
 *
 */
class DateValidator extends AbstractValidator
{
    /**
     * Minimum length. 0 = no limit.
     *
     * @var DateTimeInterface|null $min
     */
    private $min = null;

    /**
     * Maximum length. 0 = no limit.
     *
     * @var DateTimeInterface|null $max
     */
    private $max = null;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            $this->setMin($data['min']);
        }
        if (isset($data['max'])) {
            $this->setMax($data['max']);
        }
    }

    /**
     * @param string|DateTimeInterface $min The minimum allowed date.
     * @throws InvalidArgumentException If the date/time is invalid.
     * @return void
     */
    private function setMin($min)
    {

        if (is_string($min)) {
            try {
                $min = new DateTime($min);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Can not set min: '.$e->getMessage()
                );
            }
        }
        if (!($min instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid min'
            );
        }
        $this->min = $min;
    }

    /**
     * @return DateTimeInterface|null The minimum length.
     */
    private function min()
    {
        return $this->min;
    }

    /**
     * @param string|DateTime $max The maximum allowed date.
     * @throws InvalidArgumentException If the date/time is invalid.
     * @return void
     */
    private function setMax($max)
    {
        if (is_string($max)) {
            try {
                $max = new DateTime($max);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Can not set max: '.$e->getMessage()
                );
            }
        }
        if (!($max instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid max'
            );
        }
        $this->max = $max;
    }

    /**
     * @return DateTimeInterface|null
     */
    private function max()
    {
        return $this->max;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (!$this->min() && !$this->max()) {
            return $this->skip($val, 'date.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'date.skipped.empty-val');
        }

        if (is_string($val)) {
            try {
                $val= new DateTime($val);
            } catch (Exception $e) {
                return $this->failure($val, 'date.error.invalid-type');
            }
        }
        if (!($val instanceof DateTimeInterface)) {
            return $this->failure($val, 'date.error.invalid-type');
        }


        if ($this->min()) {
            $valid = $val >= $this->min();
            if (!$valid) {
                return $this->failure($val, 'date.failure.min');
            }
        }

        if ($this->max()) {
            $valid = $val <= $this->max();
            if (!$valid) {
                return $this->failure($val, 'date.failure.max');
            }
        }

        return $this->success($val, 'date.success');
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        $formattedMin = ($this->min() !== null) ? $this->min()->format('Y-m-d H:i:s'): '-';
        $formattedMax = ($this->max() !== null) ? $this->max()->format('Y-m-d H:i;s') : '-';
        return [
            'date.failure.min'            => sprintf('The date must be after "%s".', $formattedMin),
            'date.failure.max'            => sprintf('The date must be before "%s".', $formattedMax),
            'date.skipped.no-min-max'     => 'Date validation skipped, no min or max defined.',
            'date.skipped.empty-val'      => 'Date validation skipped, value is empty.',
            'date.error.invalid-type'   => 'Date validation skipped, value not a date-time.',
            'date.success'                => 'The date is valid.'
        ];
    }
}
