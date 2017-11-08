<?php

namespace Charcoal\Validator;

use InvalidArgumentException;

use Charcoal\Validator\Validator as AbstractValidator;

/**
 */
class FiletypeValidator extends AbstractValidator
{
    /**
     * @var string[]
     */
    private $accepted = [];

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
     * @throws InvalidArgumentException If the accepted argument is not an array or a comma-separated string.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['accepted'])) {
            if (is_string($data['accepted'])) {
                $this->accepted = array_map('trim', explode(',', $data['accepted']));
            } elseif (is_array($data['accepted'])) {
                $this->accepted = $data['accepted'];
            } else {
                throw new InvalidArgumentException(
                    'Accepted must be an array or comma-separated string of mimetypes.'
                );
            }
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
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (empty($this->accepted)) {
            return $this->skip($val, 'filetype.skipped.no-accepted');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'filetype.skipped.empty-val');
        }

        if (is_string($val) === false) {
            if ($this->checkType === true) {
                return $this->failure($val, 'filetype.failure.invalid-type');
            } else {
                return $this->skip($val, 'filetype.skipped.invalid-type');
            }
        }

        if ($this->isFileValid($val) === false) {
            if ($this->checkFile == true) {
                return $this->failure($val, 'filetype.failure.invalid-file');
            } else {
                return $this->skip($val, 'filetype.skipped.invalid-file');
            }
        }

        $mimetype = mime_content_type($val);

        $valid = in_array($mimetype, $this->accepted);
        if ($valid === false) {
            return $this->failure($val, 'filetype.failure.accepted');
        }

        return $this->success($val, 'filetype.success');
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'filetype.failure.accepted'       => 'The file is not of an accepted mimetype.',
            'filetype.skipped.no-accepted'    => 'File type validation skipped, no accepted mimetypes defined.',
            'filetype.skipped.empty-val'      => 'File type validation skipped, value is empty.',
            'filetype.skipped.invalid-type'   => 'File type validation skipped, value not a string (file path).',
            'filetype.success'                => 'The file type is accepted.'
        ];
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
}
