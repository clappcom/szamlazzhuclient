<?php

if (!function_exists('sortArrayKeysToOrder')){
    function sortArrayKeysToOrder($array, $keysOrder){
        if (!is_array($array)) return $array;
        $keysOrder = array_flip($keysOrder);
        $nonExistingItems = array_where($array, function($key, $value) use ($keysOrder){
            return !isset($keysOrder[$key]);
        });
        $existingItems = array_where($array, function($key, $value) use ($keysOrder){
            return isset($keysOrder[$key]);
        });

        uksort($existingItems, function($a, $b) use ($keysOrder){
            return $keysOrder[$a] - $keysOrder[$b];
        });

        $array = array_merge($existingItems, $nonExistingItems);
        return $array;
    }
}
