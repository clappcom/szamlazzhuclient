<?php

namespace Clapp\SzamlazzhuClient;

use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Illuminate\Validation\Validator as BaseValidator;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Illuminate\Translation\ArrayLoader;


class Validator extends BaseValidator{

    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = []){
        try{
            return new static(new IlluminateTranslator(new ArrayLoader(), 'en'), $data, $rules, $messages, $customAttributes);
        }catch(\Exception $e){
            return new static(new SymfonyTranslator('en'), $data, $rules, $messages, $customAttributes);
        }
    }
}
