<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableItemContract {
    /**
     * @return array
     * A számlára kerülő termék adatai.
     * Az "afaErtek" és "bruttoErtek" mezőket automatikusan is kiszámolja a rendszer.
     * [
     *  'name' => 'Elado izé',
     *  'quantity' => 1.0,
     *  'quantityUnit' => 'db',
     *  'netUnitPrice' => '10000',
     *  'vatRate' => '25', //ua. adható meg, mint a számlakészítés oldalon
     *  'netValue' => '10000.0',
     *  'vatValue' => '2500.0',
     *  'grossValue' => '12500.0',
     *  'comment' => 'tétel megjegyzés 1',
     * ];
     */
    public function getInvoiceItemData();
}
