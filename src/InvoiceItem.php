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
        return $this->attributes;
    }

    public function toArray(){
        return $this->attributes;
    }
}
