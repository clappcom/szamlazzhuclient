<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableItemCollectionContract {
    /**
     * @return array
     * A számlára kerülő összes termék adatai.
     * A tömb elemei mind tömbök, vagy `Clapp\SzamlazzhuClient\Contract\InvoiceItemContract` instance-ok.
     */
    public function getInvoiceItemCollectionData();
}
