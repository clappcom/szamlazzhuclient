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
}
