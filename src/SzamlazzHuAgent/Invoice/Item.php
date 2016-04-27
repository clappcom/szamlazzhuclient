<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace SzamlazzHuAgent\Invoice;


class Item
{

    protected $name;

    public function toArray()
    {
        return [
            "megnevezes" => "Megnevezés",
            "mennyiseg" => 1,
            "mennyisegiEgyseg" => "hó",
            "nettoEgysegar" => 1000,
            "afakulcs" => 27,
            "nettoErtek" => 1000,
            "afaErtek" => 270,
            "bruttoErtek" => 1270,
            "megjegyzes" => "Megjegyzés",
        ];
    }

}