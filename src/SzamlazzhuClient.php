<?php

namespace Clapp\SzamlazzhuClient;

use LSS\Array2XML;
use Clapp\SzamlazzhuClient\Invoice;
use GuzzleHttp\Client as HttpClient;
use Clapp\SzamlazzhuClient\Traits\MutatorAccessibleAliasesTrait;
use InvalidArgumentException;

/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:21
 */
class SzamlazzhuClient extends MutatorAccessible
{
    use MutatorAccessibleAliasesTrait;

    protected $apiBase = 'https://www.szamlazz.hu/';

    protected $attributeAliases = [
        'username' => 'felhasznalo',
        'password' => 'jelszo',
    ];
    /**
     * A megadott számla adatokból PDF számlát készít a szamlazz.hu API-ján át
     * @param  Invoice|array $invoice számla
     * @return [type]          [description]
     */
    public function generateInvoicePdf($invoice){
        $invoice = new Invoice($invoice);

        $originalInvoice = $invoice;

        $invoice = $this->addRequiredInvoiceFields($invoice);

        $client = new HttpClient([
            'base_uri' => $this->apiBase,
            'timeout'  => 20.0,
            //'cookies' => true,
        ]);

        $requestData = $this->transformRequestData($invoice);

        $response = $client->request('POST', '/szamla/', $requestData);

        $responseBody = $this->transformResponse($response);

        return $responseBody;
    }
    /**
     * PDF-fé alakítja a választ, vagy Exceptiont dob, ha ez nem lehetséges.
     * @param $response
     * @return Stream pdf tartalma
     */
    protected function transformResponse($response){
        $apiErrorCode = array_get($response->getHeader('szlahu_error_code'),0);
        if (!empty($apiErrorCode) && $apiErrorCode > 0){

            $apiErrorMessage = array_get($response->getHeader('szlahu_error'),0);

            throw new SzamlazzhuApiException(
                $apiErrorMessage,
                $apiErrorCode
            );
        }
        return $response->getBody();
    }
    /**
     * Hozzáadja a PDF elkészítéséhez szükséges mezőket az Invoice-hoz
     * @param Invoice $invoice
     * @return Invoice $invoice
     */
    protected function addRequiredInvoiceFields(Invoice $invoice){
        if ($this->username === null || $this->password === null) {
            throw new InvalidArgumentException('missing username and password');
        }
        $beallitasok = $invoice->beallitasok;
        if (empty($beallitasok)) $invoice->beallitasok = [];

        $beallitasok['felhasznalo'] = $this->username;
        $beallitasok['jelszo'] = $this->password;
        $beallitasok['eszamla'] = false; //„true” ha e-számlát kell készíteni
        $beallitasok['szamlaLetoltes'] = true; //„true” ha a válaszban meg szeretnénk kapni az elkészült PDF számlát
        $beallitasok['valaszVerzio'] = 1; //1: egyszerű szöveges válaszüzenetet vagy pdf-et ad vissza. 2: xml válasz, ha kérte a pdf-et az base64 kódolással benne van az xml-ben.

        $invoice->beallitasok = $beallitasok;
        return $invoice;
    }
    /**
     * Átalakítja az Invoice-t olyan formátumra, amit az API el tud fogadni
     * @param Invoice $invoice
     * @return array requestData
     */
    protected function transformRequestData(Invoice $invoice){
        $body = $this->transformRequestBody($invoice);

        return [
            'multipart' => [
                [
                    'name'     => 'action-xmlagentxmlfile',
                    'contents' => $body,
                    /*'filename' => 'filename.txt',
                    'headers'  => [
                        'X-Foo' => 'this is an extra header to include'
                    ]*/
                ]
            ]
        ];
    }
    /**
     * Átalakítja az Invoice-t xml-lé, hogy az api is értelmezni tudja
     * @param Invoice $invoice
     * @return string xml
     */
    protected function transformRequestBody(Invoice $invoice){
        $invoiceDocument = Array2XML::createXML(
            'xmlszamla',
            $invoice->toArray()
        );
        $node = $invoiceDocument->getElementsByTagName('xmlszamla')->item(0);
        $node->setAttribute('xmlns','http://www.szamlazz.hu/xmlszamla');
        $node->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $node->setAttribute('xsi:schemaLocation','http://www.szamlazz.hu/xmlszamla xmlszamla.xsd');
        return $invoiceDocument->saveXML();
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
