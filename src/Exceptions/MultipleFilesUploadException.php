<?php namespace Trikey\FileUploader\Exceptions;

class MultipleFilesUploadException extends \Exception {

    private $messages;

    public function __construct(array $messages) {
        $this->messages = $messages;
    }

    public function getMessages() {
        return $this->messages;
    }
}
