<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableItemContract {
    /**
     * @return array
     * A számlára kerülő termék adatai.
     * Az "afaErtek" és "bruttoErtek" mezőket automatikusan is kiszámolja a rendszer.
     * [
     *  'megnevezes' => 'Elado izé',
     *  'mennyiseg' => 1.0,
     *  'mennyisegiEgyseg' => 'db',
     *  'nettoEgysegar' => '10000',
     *  'afakulcs' => '25', //ua. adható meg, mint a számlakészítés oldalon
     *  'nettoErtek' => '10000.0',
     *  'afaErtek' => '2500.0',
     *  'bruttoErtek' => '12500.0',
     *  'megjegyzes' => 'tétel megjegyzés 1',
     * ];
     */
    public function getInvoiceItemData();
}
