<?php

namespace Charcoal\Validator;

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
    private $php = false;

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
        if (isset($data['php'])) {
            $this->setPhp($data['php']);
        }
    }

    /**
     * @param integer|string|null $min The minimum allowed file size. Will be parsed.
     * @return void
     */
    private function setMin($min)
    {
        $this->min = $this->parseFileSize($min);
    }

    /**
     * Retrieves the minimum allowed  file size, in bytes.
     *
     * @return integer
     */
    private function min()
    {
        return $this->min;
    }

    /**
     * @param string|integer|null $max The maximum allowed date.
     * @return void
     */
    private function setMax($max)
    {
        $this->max = $this->parseFileSize($max);
    }

    /**
     * Retrieves the maximum allowed file size, in bytes.
     *
     * @return integer
     */
    private function max()
    {
        return $this->max;
    }

    /**
     * @param boolean $php Whether to validate against PHP maximum filesize setting.
     * @return void
     */
    private function setPhp($php)
    {
        $this->php = !!$php;
    }

    /**
     * @return boolean
     */
    private function php()
    {
        return $this->php;
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
     * Retrieve the maximum size (in bytes) allowed for an uploaded file
     * as configured in {@link http://php.net/manual/en/ini.php `php.ini`}.
     *
     * @return integer
     */
    private function maxFilesizeAllowedByPhp()
    {
        $postMaxSize = $this->parseFileSize(ini_get('post_max_size'));
        $uploadMaxFilesize = $this->parseFileSize(ini_get('upload_max_filesize'));

        return min($postMaxSize, $uploadMaxFilesize);
    }

    /**
     * @param mixed $val The value to validate.
     * @return \Charcoal\Validator\ValidationResult
     */
    public function validate($val)
    {
        if ($this->hasMinOrMaxOrPhp() === false) {
            return $this->skip($val, 'filesize.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($this->isValueEmpty($val) === true) {
            return $this->skip($val, 'filesize.skipped.empty-val');
        }

        if ($this->isFileValid($val) === false) {
            return $this->skip($val, 'filesize.failure.invalid-file');
        }

        $filesize = filesize($val);

        if ($this->validateMin($filesize) === false) {
            return $this->failure($val, 'filesze.failure.min');
        }

        if ($this->validateMax($filesize) === false) {
            return $this->failure($val, 'length.failure.max');
        }

        if ($this->validatePhp($filesize) === false) {
            return $this->failure($val, 'filesize.failure.php');
        }

        return $this->success($val, 'length.success');
    }

    /**
     * @return boolean
     */
    private function hasMinOrMaxOrPhp()
    {
        if (($this->min() === 0) &&
            ($this->max() === 0) &&
            ($this->php() === false)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param mixed $val The value to check for emptiness.
     * @return boolean
     */
    private function isValueEmpty($val)
    {
        if ($val === null || $val === '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $val The value to check if valid file.
     * @return boolean
     */
    private function isFileValid($val)
    {
        if (!is_string($val)) {
            return false;
        }
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
        if ($this->min() !== 0) {
            return ($filesize >= $this->min());
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
        if ($this->max() !== 0) {
            return ($filesize <= $this->max());
        } else {
            return true;
        }
    }

    /**
     * @param integer $filesize The file size to validate.
     * @return boolean
     */
    private function validatePhp($filesize)
    {
        if ($this->php() === true) {
            return ($filesize <= $this->maxFilesizeAllowedByPhp());
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
            'filesize.failure.min'            => sprintf('The file size must be at least %s bytes.', $this->min()),
            'filesize.failure.max'            => sprintf('The file size must be a maximum of %s bytes.', $this->max()),
            'filesize.failure.php'            => sprintf('The file size is greater than maximum allowed by PHP (%s)', $this->maxFilesizeAllowedByPhp()),
            'filesize.skipped.no-min-max'     => 'File size validation skipped, no min or max defined.',
            'filesize.skipped.empty-val'      => 'File size validation skipped, value is empty.',
            'filesize.failure.invalid-type'   => 'File size validation skipped, value does not appear to be a file.',
            'filesize.success'                => sprintf('The file size is between %s and %s bytes.', $this->min(), $this->max())
        ];
    }
}
