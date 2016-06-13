<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableCustomerContract {
    /**
     * @return array
     * A számlára kerülő vevő adatai.
     * [
     *  'customerName' => 'Kovacs Bt.',
     *  'customerBillingPostcode' => '2030',
     *  'customerBillingCity' => 'Érd',
     *  'customerBillingAddress' => 'Tárnoki út 23.',
     *  'customerEmail' => '', //ha meg van adva, akkor erre az email címre kiküldi a számlát a Számlázz.hu TESZT FIÓK ESETÉN BIZTONSÁGI OKOKBÓL NEM KÜLD A RENDSZER EMAILT. AZ EMAIL KÜLDÉS E-SZÁMLA ÉS PRÉMIUM CSOMAG ESETÉN MŰKÖDIK.
     *  'customerShouldReceiveNotification' => false, //kérheti, hogy érvényes email cím esetén mégse küldje el a Számla Agent a számlaértesítő email-t
     *  'customerTaxNumber' => '12345678-1-42',
     *  'customerShippingName' => 'Kovács Bt. postázási név', //a postázási adatok nem kötelezők
     *  'customerShippingPostcode' => '2040',
     *  'customerShippingCity' => 'Budaörs',
     *  'customerShippingAddress' => 'Szivárvány utca 8. VI.em. 42.',
     *  'customerSignerName' => 'Vevő Aláírója', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
     *  'customerPhone' => 'Tel:+3630-555-55-55, Fax:+3623-555-555',
     *  'customerComment' => 'A portáról felszólni a 214-es mellékre.',
     * ];
     */
    public function getInvoiceCustomerData();
}
