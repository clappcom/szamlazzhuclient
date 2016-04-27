<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:53
 */

namespace SzamlazzHuAgent;


class Config
{
    protected $agentUrl = 'https://www.szamlazz.hu/szamla/';
    protected $username = '';
    protected $password = '';
    protected $eInvoice = true;
    protected $tokenPassword = '';
    protected $downloadPdf = true;
    protected $downloadPdfQty = 2;
    protected $responseVersion = 1;
    protected $aggregator = '';
    protected $xmlStoragePath = '';
    protected $pdfStoragePath = '';
    protected $cookieStoragePath = '';

    public function __construct(Array $config)
    {
        foreach($config as $key => $param) {
            $this->$key = $param;
        }
    }

    public function getXmlStoragePath() {
        return $this->xmlStoragePath;
    }

    public function getCookieStoragePath() {
        return $this->cookieStoragePath;
    }

    public function getPdfStoragePath() {
        return $this->pdfStoragePath;
    }

    public function getDownloadPdf() {
        return $this->downloadPdf;
    }

    public function getAgentUrl() {
        return $this->agentUrl;
    }

    public function toArray() {
        return [
            "felhasznalo" => $this->username,
            "jelszo" => $this->password,
            "eszamla" => $this->eInvoice,
            "kulcstartojelszo" => $this->tokenPassword,
            "szamlaLetoltes" => $this->downloadPdf,
            "szamlaLetoltesPld" => $this->downloadPdfQty,
            "valaszVerzio" => $this->responseVersion,
            "aggregator" => $this->aggregator
        ];
    }
}