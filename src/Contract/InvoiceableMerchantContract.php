<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableMerchantContract {
    /**
     * @return array
     * A számlára kerülő eladó adatai,
     * "az itt nem szereplő adatokat a Számlázz.hu felhasználói fiókból veszi a rendszer"
     * [
     *  'bank' => '',
     *  'bankszamlaszam' => '',
     *  'emailReplyto' => '',
     *  'emailTargy' => '',
     *  'emailSzoveg' => '',
     *  'alairoNeve' => '', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
     * ];
     */
    public function getInvoiceMerchantData();
}
