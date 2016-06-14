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
use Exception;
use Illuminate\Validation\ValidationException;
use Clapp\SzamlazzhuClient\Traits\MutatorAccessibleAliasesTrait;
use Clapp\SzamlazzhuClient\Traits\FillableAttributesTrait;
use Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableMerchantContract;

class Invoice extends MutatorAccessible
{
    use MutatorAccessibleAliasesTrait, FillableAttributesTrait;

    public function __construct($attributes = []){
        $this->fill($attributes);
    }

    /**
     * "fejlec.szamlaNelve" lehetséges értékei
     */
    public $allowedLanguages = [
        'hu',
        'en',
        'de',
        'it',
    ];

    /**
     * aliasok a számla mezőire
     */
    protected $attributeAliases = [
        'customerName' => 'vevo.nev',
        'customerBillingPostcode' => 'vevo.irsz',
        'customerBillingCity' => 'vevo.telepules',
        'customerBillingAddress' => 'vevo.cim',
        'customerEmail' => 'vevo.email',
        'customerShouldReceiveNotification' => 'vevo.sendEmail',
        'customerTaxNumber' => 'vevo.adoszam',
        'customerShippingName' => 'vevo.postazasiNev',
        'customerShippingPostcode' => 'vevo.postazasiIrsz',
        'customerShippingCity' => 'vevo.postazasiTelepules',
        'customerShippingAddress' => 'vevo.postazasiCim',
        'customerSignerName' => 'vevo.alairoNeve',
        'customerPhone' => 'vevo.telefonszam',
        'customerComment' => 'vevo.megjegyzes',

        'merchantBankName' => 'elado.bank',
        'merchantBankAccountNumber' => 'elado.bankszamlaszam',
        'merchantEmailReplyto' => 'elado.emailReplyto',
        'merchantEmailSubject' => 'elado.emailTargy',
        'merchantEmailText' => 'elado.emailSzoveg',
        'merchantSignerName' => 'elado.alairoNeve',

        'paymentMethod' => 'fejlec.fizmod',
        'currency' =>'fejlec.penznem',
        'language' =>'fejlec.szamlaNyelve',
        'comment' =>'fejlec.megjegyzes',
        'exchangeRateBank' =>'fejlec.arfolyamBank',
        'exchangeRate' =>'fejlec.arfolyam',
        'orderNumber' =>'fejlec.rendelesSzam',
        'paid' =>'fejlec.fizetve',
    ];

    public function setLanguageAttribute($lang){
        if (!in_array($lang, $this->allowedLanguages)){
            throw new InvalidArgumentException("invalid language");
        }
        array_set($this->attributes, 'fejlec.szamlaNyelve', $lang);
    }

    public function setItemsAttribute($items){
        if ($items instanceof InvoiceableItemCollectionContract){
            $items = $items->getInvoiceItemCollectionData();
        }
        if (is_array($items)){
            if (!isset($this->attributes['tetelek']['tetel'])) $this->attributes['tetelek']['tetel'] = [];
            $this->attributes['tetelek']['tetel'] = [];
            foreach($items as $item){
                $this->addItem($item);
            }
        }else {
            throw new InvalidArgumentException("invalid items");
        }
    }

    public function getItemsAttribute(){
        return array_get($this->attributes, 'tetelek.tetel');
        /*return array_map(function($item){
            return new InvoiceItem($item);
        },array_get($this->attributes, 'tetelek'));*/
    }

    public function addItem($item){
        if ($item instanceof InvoiceableItemContract){
            $item = $item->getInvoiceItemData();
        }
        if (!isset($this->attributes['tetelek']['tetel'])) $this->attributes['tetelek']['tetel'] = [];
        $this->attributes['tetelek']['tetel'][] = (new InvoiceItem($item))->toArray();
    }

    public function setCustomerAttribute($customer){
        if ($customer instanceof InvoiceableCustomerContract){
            $customer = $customer->getInvoiceCustomerData();
        }
        $this->fill($customer);
    }

    public function setMerchantAttribute($customer){
        if ($customer instanceof InvoiceableMerchantContract){
            $customer = $customer->getInvoiceMerchantData();
        }
        $this->fill($customer);
    }
    /**
     * dátum mezők
     */
    public function setSignatureDateAttribute($date){
        $date = new Carbon($date);
        array_set($this->attributes, 'fejlec.keltDatum', $date->format('Y-m-d'));
    }

    public function getSignatureDateAttribute(){
        $ret = Carbon::now();
        if (array_has($this->attributes, 'fejlec.keltDatum')){
            $ret = new Carbon(array_get($this->attributes, 'fejlec.keltDatum'));
        }
        return $ret->hour(0)->minute(0)->second(0);
    }

    public function setSettlementDateAttribute($date){
        $date = new Carbon($date);
        array_set($this->attributes, 'fejlec.teljesitesDatum', $date->format('Y-m-d'));
    }

    public function getSettlementDateAttribute(){
        $ret = Carbon::now();
        if (array_has($this->attributes, 'fejlec.teljesitesDatum')){
            $ret = new Carbon(array_get($this->attributes, 'fejlec.teljesitesDatum'));
        }
        return $ret->hour(0)->minute(0)->second(0);
    }

    public function setDueDateAttribute($date){
        $date = new Carbon($date);
        array_set($this->attributes, 'fejlec.fizetesiHataridoDatum', $date->format('Y-m-d'));
    }

    public function getDueDateAttribute(){
        $ret = Carbon::now();
        if (array_has($this->attributes, 'fejlec.fizetesiHataridoDatum')){
            $ret = new Carbon(array_get($this->attributes, 'fejlec.fizetesiHataridoDatum'));
        }
        return $ret->hour(0)->minute(0)->second(0);
    }

    /**
     * Egy Customer validálásához használható szabályok
     */
    protected function getCustomerValidationRules(){
        return [
            "vevo.nev" => "required|string",
            "vevo.irsz" => "required",
            'vevo.telepules' => 'required|string',
            'vevo.cim' => 'required|string',
            'vevo.email' => 'email',
            'vevo.sendEmail' => 'boolean',
            'vevo.adoszam' => 'string',
            'vevo.postazasiNev' => 'string',
            'vevo.postazasiIrsz' => 'string',
            'vevo.postazasiTelepules' => 'string',
            'vevo.postazasiCim' => 'string',
            'vevo.alairoNeve' => 'string',
            'vevo.telefonszam' => 'string',
            'vevo.megjegyzes' => 'string',
        ];
    }
    /**
     * Egy Customer validálása
     * @throws Exception
     */
    public function validateCustomer(){
        $validator = Validator::make($this->toArray(), $this->getCustomerValidationRules());
        if ($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }
    /**
     * Egy Merchant validálásához használható szabályok
     */
    protected function getMerchantValidationRules(){
        return [
            'elado' => 'required|array',
            'elado.bank' => 'string',
            'elado.bankszamlaszam' => 'string',
            'elado.emailReplyto' => 'string',
            'elado.emailTargy' => 'string',
            'elado.emailSzoveg' => 'string',
            'elado.alairoNeve' => 'string',
        ];
    }
    /**
     * Egy Merchant validálása
     * @throws Exception
     */
    public function validateMerchant(){
        $validator = Validator::make($this->toArray(), $this->getMerchantValidationRules());
        if ($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }
    /**
     * A számla termékeinek validálásához használható szabályok
     */
    protected function getItemsValidationRules(){
        return [
            'tetelek' => 'required|array',
            'tetelek.0.*.tetel.megnevezes' => 'required',
            'tetelek.0.*.tetel.mennyiseg' => 'required|numeric|min:0',
            'tetelek.0.*.tetel.mennyisegiEgyseg' => 'required|string',
            'tetelek.0.*.tetel.nettoEgysegar' => 'required|numeric|min:0',
            'tetelek.0.*.tetel.afakulcs' => 'required|string',
            'tetelek.0.*.tetel.nettoErtek' => 'required|numeric|min:0',

            'tetelek.0.*.tetel.afaErtek' => 'required|numeric|min:0',
            'tetelek.0.*.tetel.bruttoErtek' => 'required|numeric|min:0',

            'tetelek.0.*.tetel.megjegyzes' => 'string'
        ];
    }
    /**
     * A számla termékeinek validálása
     * @throws Exception
     */
    public function validateItems(){
        $validator = Validator::make($this->toArray(), $this->getItemsValidationRules());
        if ($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }
    /**
     * A számla kiegészítő adatainak validálásához használható szabályok
     */
    protected function getOrderDetailsValidationRules(){
        return [
            //'beallitasok.eszamla' => 'required|boolean',

            'fejlec.keltDatum' => ['required','date:Y-m-d'],
            'fejlec.teljesitesDatum' => ['required','date:Y-m-d'],
            'fejlec.fizetesiHataridoDatum' => ['required','date:Y-m-d'],
            'fejlec.fizmod' => 'required|string',
            'fejlec.penznem' => 'required|string',
            'fejlec.szamlaNyelve' => 'required|string|in:'.implode(',',$this->allowedLanguages),

            'fejlec.megjegyzes' => 'string',
            'fejlec.arfolyamBank' => 'string',
            'fejlec.arfolyam' => 'numeric',
            'fejlec.rendelesSzam' => 'string',
            'fejlec.elolegszamla' => 'boolean',
            'fejlec.vegszamla' => 'boolean',
            'fejlec.helyesbitoszamla' => 'boolean',
            'fejlec.helyesbitettSzamlaszam' => 'boolean',
            'fejlec.dijbekero' => 'boolean',
            'fejlec.szamlaszamElotag' => 'string',
            'fejlec.fizetve' => 'boolean',
        ];
    }
    /**
     * A számla kiegészítő adatainak validálása
     * @throws Exception
     */
    public function validateOrderDetails(){
        $validator = Validator::make($this->toArray(), $this->getOrderDetailsValidationRules());
        if ($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }
    /**
     * A teljes számla validálása
     * @throws Exception
     */
    public function validate(){
        return $this->validateItems() &&
            $this->validateMerchant() &&
            $this->validateCustomer() &&
            $this->validateOrderDetails();
    }

    /**
     * az xml schemának nem mindegy, hogy milyen sorrendben vannak a key-ek a számlában
     *
     * ez "sorrendbe" rakja őket
     */
    protected function sortAttributes(){
        $invoiceKeysOrder = ['beallitasok', 'fejlec', 'elado', 'vevo', 'fuvarlevel', 'tetelek'];
        $customerKeysOrder = ['nev', 'orszag', 'irsz', 'telepules', 'cim', 'email', 'sendEmail', 'adoszam', 'adoszamEU', 'postazasiNev',
            'postazasiOrszag', 'postazasiIrsz', 'postazasiTelepules', 'postazasiCim', 'vevoFokonyv', 'azonosito', 'alairoNeve', 'telefonszam', 'megjegyzes'];
        $merchantKeysOrder = ['bank', 'bankszamlaszam', 'emailReplyto', 'emailTargy', 'emailSzoveg', 'alairoNeve'];
        $settingsKeysOrder = ['felhasznalo', 'jelszo', 'eszamla', 'kulcstartojelszo', 'szamlaLetoltes', 'szamlaLetoltesPld', 'valaszVerzio', 'aggregator'];
        $headerKeysOrder = ['keltDatum', 'teljesitesDatum', 'fizetesiHataridoDatum', 'fizmod', 'penznem', 'szamlaNyelve', 'megjegyzes', 'arfolyamBank', 'arfolyam', 'rendelesSzam', 'elolegszamla', 'vegszamla', 'helyesbitoszamla', 'helyesbitettSzamlaszam', 'dijbekero', 'szallitolevel', '', 'logoExtra', 'szamlaszamElotag', 'fizetendoKorrekcio', 'fizetve', 'arresAfa'];

        if (isset($this->attributes)) $this->attributes = \sortArrayKeysToOrder($this->attributes, $invoiceKeysOrder);

        $aliases = [
            'beallitasok' => $settingsKeysOrder,
            'fejlec' => $headerKeysOrder,
            'elado' => $merchantKeysOrder,
            'vevo' => $customerKeysOrder,
        ];

        foreach ($aliases as $name => $keysOrder){
            if (array_has($this->attributes, $name)) {
                array_set(
                    $this->attributes,
                    $name,
                    \sortArrayKeysToOrder(
                        array_get($this->attributes, $name),
                        $keysOrder
                    )
                );
            }
        }
    }

    public function toArray(){
        $this->sortAttributes();
        return $this->attributes;
    }
}
