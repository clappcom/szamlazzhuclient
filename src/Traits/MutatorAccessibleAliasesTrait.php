<?php
namespace Clapp\SzamlazzhuClient\Traits;

trait MutatorAccessibleAliasesTrait{
    public function hasAttributeAlias($key){
        return array_key_exists($key, $this->attributeAliases);
    }

    public function getAttributeAlias($key){
        return array_get($this->attributeAliases, $key);
    }

    public function getAttribute($key){
        if (!$this->hasGetMutator($key) && $this->hasAttributeAlias($key)){
            $key = $this->getAttributeAlias($key);
            return array_get($this->attributes, $key);
        }else {
            return parent::getAttribute($key);
        }
    }

    public function setAttribute($key, $value){
        if (!$this->hasSetMutator($key) && $this->hasAttributeAlias($key)){
            $key = $this->getAttributeAlias($key);
            return array_set($this->attributes, $key, $value);
        }else {
            return parent::setAttribute($key, $value);
        }
    }
}
