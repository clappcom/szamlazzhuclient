<?php

namespace Clapp\SzamlazzhuClient;
use SebastianBergmann\Exporter\Exception;
use Clapp\SzamlazzhuClient\Exception\AuthenticationException;
use Clapp\SzamlazzhuClient\Invoice\Pdf;
use LSS\Array2XML;

/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:21
 */
class SzamlazzhuClient
{

    private $config = null;
    /*
     * config
     *  invoiceStoragePath
     *  username
     *  password
     *  eInvoice
     *  tokenPassword
     *  downloadPDF
     *  downloadPDFQty
     *  responseVersion
     *  aggregator
     */

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function send(Invoice $invoice)
    {
        $invoice->setConfig($this->config);
        $invoice->generateXml();

        $request = new Request();
        $invoiceXml = $invoice->getXml();

        return $request->send($invoiceXml);

    }

    /**
     * ideiglenes teszthez
     */
    public static function raw(){
        $items = [];

        for ($i = 0; $i < 4; $i++){
            $items[] = [
                'megnevezes' => 'Elado izé',
                'mennyiseg' => 1.0,
                'mennyisegiEgyseg' => 'db',
                'nettoEgysegar' => '10000',
                'afakulcs' => '25', //ua. adható meg, mint a számlakészítés oldalon
                'nettoErtek' => '10000.0',
                'afaErtek' => '2500.0',
                'bruttoErtek' => '12500.0',
                'megjegyzes' => 'tétel megjegyzés 1',
            ];
        }

        $invoice = [
            'beallitasok' => [
                'felhasznalo' => 'teszt01', //a Számlázz.hu-s felhasználó
                'jelszo' => 'teszt01', //a Számlázz.hu-s felhasználó jelszava
                'eszamla' => true, //„true” ha e-számlát kell készíteni
                'szamlaLetoltes' => true, //„true” ha a válaszban meg szeretnénk kapni az elkészült PDF számlát
                'valaszVerzio' => 1, //1: egyszerű szöveges válaszüzenetet vagy pdf-et ad vissza. 2: xml válasz, ha kérte a pdf-et az base64 kódolással benne van az xml-ben.
            ],
            'fejlec' => [
                'keltDatum' => '2010-09-12', //a dátum formátum kötött
                'teljesitesDatum' => '2010-09-10',
                'fizetesiHataridoDatum' => '2010-09-20',
                'fizmod' => 'Átutalás', //értékkészlet: böngészőből történő számlakészítés során látható
                'penznem' => 'HUF', //értékkészlet: böngészőből történő számlakészítés során látható
                'szamlaNyelve' => 'hu', //lehet: de, en, it
                'megjegyzes' => 'Számla megjegyzés',
                'arfolyamBank' => 'MNB', //devizás számla esetén meg kell adni, hogy melyik bank árfolyamával számoltuk a számlán a forintos ÁFA értéket
                'arfolyam' => 0.0, //devizás számla esetén meg kell adni, hogy melyik bank árfolyamával számoltuk a számlán a forintos ÁFA értéket
                'rendelesSzam' => '',
                'elolegszamla' => false,
                'vegszamla' => false,
                'helyesbitoszamla' => false,
                'helyesbitettSzamlaszam' => false,
                'dijbekero' => false,
                'szamlaszamElotag' => 'Kérem töltse ki!',
                'fizetve' => true,
            ],
            'elado' => [ //Az eladó adatai, az itt nem szereplő adatokat a Számlázz.hu felhasználói fiókból veszi a rendszer
                'bank' => '',
                'bankszamlaszam' => '',
                'emailReplyto' => '',
                'emailTargy' => '',
                'emailSzoveg' => '',
                'alairoNeve' => '', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
            ],
            'vevo' => [
                'nev' => 'Kovacs Bt.',
                'irsz' => '2030',
                'telepules' => 'Érd',
                'cim' => 'Tárnoki út 23.',
                'email' => '', //ha meg van adva, akkor erre az email címre kiküldi a számlát a Számlázz.hu TESZT FIÓK ESETÉN BIZTONSÁGI OKOKBÓL NEM KÜLD A RENDSZER EMAILT. AZ EMAIL KÜLDÉS E-SZÁMLA ÉS PRÉMIUM CSOMAG ESETÉN MŰKÖDIK.
                'sendEmail' => false, //kérheti, hogy érvényes email cím esetén mégse küldje el a Számla Agent a számlaértesítő email-t
                'adoszam' => '12345678-1-42',
                'postazasiNev' => 'Kovács Bt. postázási név', //a postázási adatok nem kötelezők
                'postazasiIrsz' => '2040',
                'postazasiTelepules' => 'Budaörs',
                'postazasiCim' => 'Szivárvány utca 8. VI.em. 42.',
                'alairoNeve' => 'Vevő Aláírója', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
                'telefonszam' => 'Tel:+3630-555-55-55, Fax:+3623-555-555',
                'megjegyzes' => 'A portáról felszólni a 214-es mellékre.',
            ],
            'tetelek' => $items,
        ];


        $invoiceDocument = Array2XML::createXML(
            'xmlszamla',
            $invoice
        );


        $node = $invoiceDocument->getElementsByTagName('xmlszamla')->item(0);
        $node->setAttribute('xmlns','http://www.szamlazz.hu/xmlszamla');
        $node->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $node->setAttribute('xsi:schemaLocation','http://www.szamlazz.hu/xmlszamla xmlszamla.xsd');
        return $invoiceDocument->saveXML();
    }


}
