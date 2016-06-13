<?php

use Faker\Factory as Faker;

abstract class TestCase extends PHPUnit_Framework_TestCase{

    public function __get($field){
        if ($field === "faker"){
            if (!isset($this->_faker)){
                $this->_faker = Faker::create();
            }
            return $this->_faker;
        }else {
            return parent::__get($field);
        }
    }

    protected $lastException = null;

    public function setLastException($e){
        $this->lastException = $e;
    }

    public function assertLastException($className){
        if (empty($this->lastException) || !is_a($this->lastException, $className)){
            $this->fail('Failed to assert that last exception is subclass of '.$className);
        }
    }

}
