<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace Clapp\SzamlazzhuClient;

use InvalidArgumentException;

use Carbon\Carbon;

class Invoice extends MutatorAccessible
{

    public function setLanguageAttribute($lang){
        $languages = [
            'hu',
            'en',
            'fr',
        ];
        if (!in_array($lang, $languages)){
            throw new InvalidArgumentException("invalid language");
        }
        $this->attributes['language'] = $lang;
    }

    public function setSignatureDateAttribute($date){
        $date = new Carbon($date);
        $this->attributes['signature_date'] = $date;
    }

    public function getSignatureDateAttribute(){
        if (empty($this->attributes['signature_date'])){
            return Carbon::now();
        }
        return new Carbon($this->attributes['signature_date']);
    }
}
