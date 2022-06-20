<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    //exception code
    public static $INVALID_FIELD = 700;
    public static $INVALID_RECORD = 701;
    
    // Redefine the exception so message isn't optional

    private $errorCode;
    private $allErrors;

    public function __construct($message, $errorCode, $allErrors = array()) {

        // make sure everything is assigned properly
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->allErrors = $allErrors;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function getAllErrors() {
        return $this->allErrors;
    }
}
