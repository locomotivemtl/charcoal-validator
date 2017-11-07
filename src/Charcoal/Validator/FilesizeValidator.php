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
    private $min;

    /**
     * @var integer
     */
    private $max;

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
     * @return integer|null
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
     * @return integer|null
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
        if (!$this->min() && !$this->max() && !$this->php()) {
            return $this->skip($val, 'filesize.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'filesize.skipped.empty-val');
        }

        if (!is_string($val)) {
            return $this->skip($val, 'filesize.skipped.invalid-file');
        }

        if (!is_file($val) || !is_readable($val)) {
            return $this->skip($val, 'filesize.skipped.not-a-file');
        }

        $val = filesize($val);

        if ($this->min()) {
            $valid = $val >= $this->min();
            if (!$valid) {
                return $this->failure($val, 'filesze.failure.min');
            }
        }

        if ($this->max()) {
            $valid = $val <= $this->max();
            if (!$valid) {
                return $this->failure($val, 'length.failure.max');
            }
        }

        if ($this->php()) {
            $valid = $val <= $this->maxFilesizeAllowedByPhp();
            if (!$valid) {
                return $this->failure($val, 'filesize.failure.php');
            }
        }

        return $this->success($val, 'length.success');
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'filesize.failure.min'            => sprintf('The file size must be at least %s bytes.', $this->min()),
            'filesize.failure.max'            => sprintf('The file size must be a maximum of %s bytes.', $this->max()),
            'filesize.failure.php'          => sprintf('The file size is greater than maximum allowed by PHP (%s)', $this->maxFilesizeAllowedByPhp()),
            'length.skipped.no-min-max'     => 'File size validation skipped, no min or max defined.',
            'length.skipped.empty-val'      => 'File size validation skipped, value is empty.',
            'length.skipped.invalid-type'   => 'File size validation skipped, value does not appear to be a file.',
            'length.success'                => sprintf('The file size is between %s and %s bytes.', $this->min(), $this->max())
        ];
    }
}
