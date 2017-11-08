<?php

namespace Charcoal\Validator;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 *
 */
class FilesizeValidator extends AbstractValidator
{
    /**
     * @var integer
     */
    private $min = 0;

    /**
     * @var integer
     */
    private $max = 0;

    /**
     * @var boolean
     */
    private $checkType = true;

    /**
     * @var boolean
     */
    private $checkFile = true;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            $this->min = $this->parseFileSize($data['min']);
        }
        if (isset($data['max'])) {
            $this->max = $this->parseFileSize($data['max']);
        }
        if (isset($data['check_type'])) {
            $this->checkType = !!$data['check_type'];
        }
        if (isset($data['check_file'])) {
            $this->checkFile = !!$data['check_file'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return \Charcoal\Validator\ValidationResult
     */
    public function validate($val)
    {
        if ($this->min === 0 && $this->max === 0) {
            return $this->skip($val, 'filesize.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'filesize.skipped.empty-val');
        }

        if (is_string($val) === false) {
            if ($this->checkType === true) {
                return $this->failure($val, 'filesize.failure.invalid-type');
            } else {
                return $this->skip($val, 'filesize.skipped.invalid-type');
            }
        }

        if ($this->isFileValid($val) === false) {
            if ($this->checkFile === true) {
                return $this->failure($val, 'filesize.failure.invalid-file');
            } else {
                return $this->skip($val, 'filesize.skipped.invalid-file');
            }
        }

        $filesize = filesize($val);

        if ($this->validateMin($filesize) === false) {
            return $this->failure($val, 'filesize.failure.min');
        }

        if ($this->validateMax($filesize) === false) {
            return $this->failure($val, 'filesize.failure.max');
        }

        return $this->success($val, 'filesize.success');
    }

    /**
     * Parses a size string (with a suffix) into bytes.
     *
     * @param string|integer|null $size The file size to parse. Can be "16k" or "200M", "3.5G" or "256" (bytes), for example.
     * @return integer
     */
    private function parseFileSize($size)
    {
        if (is_numeric($size)) {
            return intval($size);
        }

        if (!is_string($size)) {
            return 0;
        }

        $quant = 'bkmgtpezy';
        $unit = preg_replace('/[^'.$quant.']/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            $size = ($size * pow(1024, stripos($quant, $unit[0])));
        }

        return round($size);
    }


    /**
     * @param mixed $val The value to check if valid file.
     * @return boolean
     */
    private function isFileValid($val)
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
     * @return boolean
     */
    private function validateMin($filesize)
    {
        if ($this->min !== 0) {
            return ($filesize >= $this->min);
        } else {
            return true;
        }
    }

    /**
     * @param integer $filesize The file size to validate.
     * @return boolean
     */
    private function validateMax($filesize)
    {
        if ($this->max !== 0) {
            return ($filesize <= $this->max);
        } else {
            return true;
        }
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'filesize.failure.min'            => sprintf('The file size must be at least %s bytes.', $this->min),
            'filesize.failure.max'            => sprintf('The file size must be a maximum of %s bytes.', $this->max),
            'filesize.failure.invalid-type'   => 'File must be a string',
            'filesize.failure.invalid-file'   => 'File is invalid.',
            'filesize.skipped.invalid-type'   => 'File size validation skipped, file is not a string',
            'filesize.skipped.invalid-file'   => 'File size validation skipped, file is not valid',
            'filesize.skipped.no-min-max'     => 'File size validation skipped, no min or max defined.',
            'filesize.skipped.empty-val'      => 'File size validation skipped, value is empty.',
            'filesize.success'                => sprintf('The file size is between %s and %s bytes.', $this->min, $this->max)
        ];
    }
}
