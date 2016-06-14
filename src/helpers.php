<?php

if (!function_exists('sortArrayKeysToOrder')){
    function sortArrayKeysToOrder($array, $keysOrder){
        if (!is_array($array)) return $array;
        $keysOrder = array_flip($keysOrder);
        uksort($array, function($a, $b) use ($keysOrder){
            if (isset($keysOrder[$a]) && isset($keysOrder[$b])){
                return $keysOrder[$a] - $keysOrder[$b];
            }else {
                if (isset($keysOrder[$a]) && !isset($keysOrder[$b])){
                    return -1;
                }else {
                    return 1;
                }
            }
        });
        return $array;
}
}
