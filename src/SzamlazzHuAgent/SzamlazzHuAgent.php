<?php

namespace SzamlazzHuAgent;
use SebastianBergmann\Exporter\Exception;
use SzamlazzHuAgent\Exception\AuthenticationException;
use SzamlazzHuAgent\Invoice\Pdf;

/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:21
 */
class SzamlazzHuAgent
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


}