<?php

namespace App\Exceptions\Custom;

class ValidationException extends \Exception
{
    private $messages;
    
    /**
     * constructor
     * @param array $validationMessages
     */
    public function __construct(array $validationMessages)
    {
        $this->messages = $validationMessages;
    }
    
    /**
     * generate json response
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
