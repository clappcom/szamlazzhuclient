<?php
namespace Clapp\SzamlazzhuClient;

use Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract;
use Clapp\SzamlazzhuClient\Traits\MutatorAccessibleAliasesTrait;
use Clapp\SzamlazzhuClient\Traits\FillableAttributesTrait;

class InvoiceItem extends MutatorAccessible implements InvoiceableItemContract{
    use MutatorAccessibleAliasesTrait, FillableAttributesTrait;

    public function __construct($attributes = []){
        $this->fill($attributes);
    }

    protected $attributeAliases = [
        'name' => 'megnevezes',
        'quantity' => 'mennyiseg',
        'quantityUnit' => 'mennyisegiEgyseg',
        'netUnitPrice' => 'nettoEgysegar',
        'vatRate' => 'afakulcs',
        'netValue' => 'nettoErtek',
        'vatValue' => 'afaErtek',
        'grossValue' => 'bruttoErtek',
        'comment' => 'megjegyzes',
    ];

    public function getInvoiceItemData(){
        $this->sortAttributes();
        return $this->attributes;
    }

    /**
     * az xml schemának nem mindegy, hogy milyen sorrendben vannak a key-ek a számlában
     *
     * ez "sorrendbe" rakja őket
     */
    protected function sortAttributes(){
        $itemKeysOrder = ['megnevezes', 'azonosito', 'mennyiseg', 'mennyisegiEgyseg', 'nettoEgysegar', 'afakulcs', 'arresAfaAlap', 'nettoErtek', 'afaErtek', 'bruttoErtek', 'megjegyzes', 'tetelFokonyv'];
        if (!empty($this->attributes)) {
            $this->attributes = \sortArrayKeysToOrder($this->attributes,$itemKeysOrder);
        }
    }

    public function toArray(){
        return $this->getInvoiceItemData();
    }
}
