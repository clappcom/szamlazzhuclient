<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace Clapp\SzamlazzhuClient\Invoice;


class Buyer
{

    /*
     * <nev>Kovacs Bt.</nev>
    <irsz>2030</irsz>
    <telepules>Érd</telepules>
    <cim>Tárnoki út 23.</cim>
    <!-- <email>vevoneve@example.org</email> -->
    <!-- <sendEmail>true</sendEmail> -->
    <adoszam>12345678-1-42</adoszam>
    <postazasiNev>Kovács Bt. postázási név</postazasiNev>
    <postazasiIrsz>2040</postazasiIrsz>
    <postazasiTelepules>Budaörs</postazasiTelepules>
    <postazasiCim>Szivárvány utca 8. VI.em. 82.</postazasiCim>
    <!-- <alairoNeve>Vevő Aláírója</alairoNeve> -->
    <!-- <azonosito></azonosito> -->
    <!-- <telefonszam>+3630-555-55-55, Fax:+3623-555-555</telefonszam> -->
    <!-- <megjegyzes>A portáról felszólni a 214-es mellékre.</megjegyzes> -->*/

    protected $name = "";
    protected $zipcode = "";
    protected $city = "";
    protected $address = "";
    protected $email = "";
    protected $sendEmail = "";
    protected $taxNumber = "";
    protected $postName = "";
    protected $postZipcode = "";
    protected $postCity = "";
    protected $postAddress = "";
    protected $signName = "";
    protected $identifier = "";
    protected $phoneNumber = "";
    protected $comment = "";

    public function __set($name, $value)
    {
        $this->$name = $value;
    }


    public function toArray() {
        return [
            "nev" => $this->name,
            "irsz" => $this->zipcode,
            "telepules" => $this->city,
            "cim" => $this->address,
            "adoszam" => $this->taxNumber
        ];
    }
}