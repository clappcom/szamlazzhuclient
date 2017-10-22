<?php

if (!function_exists('illuminate_array_where')){
    /**
     * Note: Copied from Laravel 5.2 to circumvent a breaking change on "array_where" in Laravel 5.3
     *
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    function illuminate_array_where($array, callable $callback)
    {
        $filtered = [];
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }
}

if (!function_exists('sortArrayKeysToOrder')){
    function sortArrayKeysToOrder($array, $keysOrder){
        if (!is_array($array)) return $array;
        $keysOrder = array_flip($keysOrder);
        $nonExistingItems = illuminate_array_where($array, function($key, $value) use ($keysOrder){
            return !isset($keysOrder[$key]);
        });
        $existingItems = illuminate_array_where($array, function($key, $value) use ($keysOrder){
            return isset($keysOrder[$key]);
        });

        uksort($existingItems, function($a, $b) use ($keysOrder){
            return $keysOrder[$a] - $keysOrder[$b];
        });

        $array = array_merge($existingItems, $nonExistingItems);
        return $array;
    }
}


