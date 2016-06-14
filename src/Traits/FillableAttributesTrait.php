<?php
namespace Clapp\SzamlazzhuClient\Traits;

use InvalidArgumentException;

trait FillableAttributesTrait{
    public function fill($attributes = []){
        if (empty($attributes)) return;
        if (method_exists($attributes, 'toArray')){
            $attributes = $attributes->toArray();
        }
        if (empty($attributes)) return;

        if (!is_array($attributes)) {
            throw new InvalidArgumentException('Method "'.__METHOD__.'" requires an array');
        }

        foreach($attributes as $attr => $val){
            $this->setAttribute($attr, $val);
        }
    }
}
