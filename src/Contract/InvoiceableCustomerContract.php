<?php
namespace Clapp\SzamlazzhuClient\Contract;

interface InvoiceableCustomerContract {
    /**
     * @return array
     * A számlára kerülő vevő adatai.
     * [
     *  'nev' => 'Kovacs Bt.',
     *  'irsz' => '2030',
     *  'telepules' => 'Érd',
     *  'cim' => 'Tárnoki út 23.',
     *  'email' => '', //ha meg van adva, akkor erre az email címre kiküldi a számlát a Számlázz.hu TESZT FIÓK ESETÉN BIZTONSÁGI OKOKBÓL NEM KÜLD A RENDSZER EMAILT. AZ EMAIL KÜLDÉS E-SZÁMLA ÉS PRÉMIUM CSOMAG ESETÉN MŰKÖDIK.
     *  'sendEmail' => false, //kérheti, hogy érvényes email cím esetén mégse küldje el a Számla Agent a számlaértesítő email-t
     *  'adoszam' => '12345678-1-42',
     *  'postazasiNev' => 'Kovács Bt. postázási név', //a postázási adatok nem kötelezők
     *  'postazasiIrsz' => '2040',
     *  'postazasiTelepules' => 'Budaörs',
     *  'postazasiCim' => 'Szivárvány utca 8. VI.em. 42.',
     *  'alairoNeve' => 'Vevő Aláírója', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
     *  'telefonszam' => 'Tel:+3630-555-55-55, Fax:+3623-555-555',
     *  'megjegyzes' => 'A portáról felszólni a 214-es mellékre.',
     * ];
     */
    public function getInvoiceCustomerData();
}
