<?php

namespace Charcoal\Validator;

/**
 */
class FiletypeValidator extends AbstractValidator
{
    /**
     * @var string[]
     */
    private $accepted = [];


    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['accepted'])) {
            $this->setAccepted($data['accepted']);
        }
    }

    /**
     * @param string[] $accepted The accepted mimetypes.
     * @return void
     */
    private function setAccepted(array $accepted)
    {
        $this->accepted = $accepted;
    }

    /**
     * @return string[]
     */
    private function accepted()
    {
        return $this->accepted;
    }


    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (empty($this->accepted())) {
            return $this->skip($val, 'filetype.skipped.no-accepted');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'filetype.skipped.empty-val');
        }

        if (is_string($val)) {
            $val = mime_content_type($val);
        } else {
            return $this->skip($val, 'filetype.skipped.invalid-file');
        }

        $valid = in_array($val, $this->accepted());
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
}
