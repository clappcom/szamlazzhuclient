<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace Clapp\SzamlazzhuClient;

use Clapp\SzamlazzhuClient\Invoice\Buyer;
use Clapp\SzamlazzhuClient\Invoice\DeliveryLetter;
use Clapp\SzamlazzhuClient\Invoice\Header;
use Clapp\SzamlazzhuClient\Invoice\Item;
use Clapp\SzamlazzhuClient\Invoice\Seller;
use Clapp\SzamlazzhuClient\Invoice\Xml;

class Invoice
{
    private $config = null;
    private $header = null;
    private $seller = null;
    private $buyer = null;
    private $deliveryLetter = null;
    private $items = [];
    private $xml = null;


    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function getConfig() {
        return $this->config;
    }

    public function setInvoiceHeader(Header $header)
    {
        $this->header = $header;
    }

    public function setSeller(Seller $seller)
    {
        $this->seller = $seller;
    }

    public function setBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;
    }

    public function setDeliveryLetter(DeliveryLetter $letter)
    {
        $this->deliveryLetter = $letter;
    }

    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function removeItem(Item $item)
    {

    }

    public function clearItems()
    {
        $this->items = [];
    }

    private function getItemsInArray()
    {
        $items = [];

        foreach($this->items as $item) {
            $items[] = $item->toArray();
        }

        return ['tetel' => $items];
    }

    public function generateXml()
    {
        $this->xml = new Xml('tesztxml', $this);
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function toArray()
    {
        return [
            'beallitasok' => $this->config->toArray(),
            'fejlec' => $this->header->toArray(),
            'elado' => $this->seller->toArray(),
            'vevo' => $this->buyer->toArray(),
            'tetelek' => $this->getItemsInArray(),
        ];
    }

}