<?php

namespace Clapp\SzamlazzhuClient;

use Symfony\Component\Translation\Translator;
use Illuminate\Validation\Validator as BaseValidator;

class Validator extends BaseValidator{

    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = []){
        return new static(new Translator('en'), $data, $rules, $messages, $customAttributes);
    }
}
