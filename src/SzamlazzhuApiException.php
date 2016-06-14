<?php

namespace Clapp\SzamlazzhuClient;

use Exception;

class SzamlazzhuApiException extends Exception{
    public function __construct($message, $code = 0, Exception $previous = null) {
        if (is_string($message)){
            try {
                $message = urldecode($message);
            }catch(Exception $e){}
        }

        parent::__construct($message, $code, $previous);
    }
}
