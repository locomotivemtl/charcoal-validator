<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
class FilesizeRule extends Rule
{
    /**
     * @var int
     */
    private $min = 0;

    /**
     * @var int
     */
    private $max = 0;

    /**
     * @var bool
     */
    private $checkType = true;

    /**
     * @var bool
     */
    private $checkFile = true;

    /**
     * @param array<string, mixed> $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            $this->min = $this->parseFileSize($data['min']);
        }
        if (isset($data['max'])) {
            $this->max = $this->parseFileSize($data['max']);
        }
        if (isset($data['checkType'])) {
            $this->checkType = !!$data['checkType'];
        }
        if (isset($data['checkFile'])) {
            $this->checkFile = !!$data['checkFile'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        if ($this->min === 0 && $this->max === 0) {
            return $this->skip('filesize.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip('filesize.skipped.empty-val');
        }

        if (is_string($val) === false) {
            if ($this->checkType === true) {
                return $this->failure('filesize.failure.invalid-type');
            } else {
                return $this->skip('filesize.skipped.invalid-type');
            }
        }

        if ($this->isFileValid($val) === false) {
            if ($this->checkFile === true) {
                return $this->failure('filesize.failure.invalid-file');
            } else {
                return $this->skip('filesize.skipped.invalid-file');
            }
        }

        $filesize = (int)filesize($val);

        if ($this->validateMin($filesize) === false) {
            return $this->failure('filesize.failure.min');
        }

        if ($this->validateMax($filesize) === false) {
            return $this->failure('filesize.failure.max');
        }

        return $this->success('filesize.success');
    }

    /**
     * Parses a size string (with a suffix) into bytes.
     *
     * @param string|int|null $size The file size to parse. Can be "16k" or "200M", "3.5G" or "256" (bytes), for example.
     * @return int
     */
    private function parseFileSize($size): int
    {
        if (is_numeric($size)) {
            return intval($size);
        }

        if (!is_string($size)) {
            return 0;
        }

        $quant = 'bkmgtpezy';
        $unit = preg_replace('/[^' . $quant . ']/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            $size = (floatval($size) * floatval(pow(1024, (int)stripos($quant, $unit[0]))));
        }

        return intval(round((float)$size));
    }


    /**
     * @param mixed $val The value to check if valid file.
     * @return bool
     */
    private function isFileValid($val): bool
    {
        if (!is_file($val)) {
            return false;
        }
        if (!is_readable($val)) {
            return false;
        }
        return true;
    }

    /**
     * @param integer $filesize The file size to validate.
     * @return bool
     */
    private function validateMin($filesize): bool
    {
        if ($this->min !== 0) {
            return ($filesize >= $this->min);
        } else {
            return true;
        }
    }

    /**
     * @param integer $filesize The file size to validate.
     * @return bool
     */
    private function validateMax($filesize): bool
    {
        if ($this->max !== 0) {
            return ($filesize <= $this->max);
        } else {
            return true;
        }
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'filesize.failure.min' => sprintf('The file size must be at least %s bytes.', $this->min),
            'filesize.failure.max' => sprintf('The file size must be a maximum of %s bytes.', $this->max),
            'filesize.failure.invalid-type' => 'File must be a string',
            'filesize.failure.invalid-file' => 'File is invalid.',
            'filesize.skipped.invalid-type' => 'File size validation skipped, file is not a string',
            'filesize.skipped.invalid-file' => 'File size validation skipped, file is not valid',
            'filesize.skipped.no-min-max' => 'File size validation skipped, no min or max defined.',
            'filesize.skipped.empty-val' => 'File size validation skipped, value is empty.',
            'filesize.success' => sprintf('The file size is between %s and %s bytes.', $this->min, $this->max)
        ];
    }
}
