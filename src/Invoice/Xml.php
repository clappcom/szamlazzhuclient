<?php

namespace Clapp\SzamlazzhuClient\Invoice;

use LSS\Array2XML;
use Clapp\SzamlazzhuClient\Invoice;

class Xml
{

    private $body = '';
    private $path = '';
    private $invoice = null;
    private $filename = '';

    public function __construct($filename, Invoice $invoice)
    {
        $this->filename = $filename;
        $this->invoice = $invoice;
        $this->path = $invoice->getConfig()->getXmlStoragePath() . '/' . $filename . '.xml';

        $xmlData = $invoice->toArray();
        //$sxe = new \SimpleXMLElement('');
        //$this->body = self::arrayToXml($xmlData, $sxe)->asXML();


        $xmlData['@attributes'] = [
            "xmlns" => "http://www.szamlazz.hu/xmlszamla",
            "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
            "xsi:schemaLocation" => "http://www.szamlazz.hu/xmlszamla xmlszamla.xsd "
        ];

        $xml = Array2XML::createXML(
            'xmlszamla',
            $xmlData
        );
        $this->body = $xml->saveXML();

        file_put_contents($this->path, $this->body);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

}