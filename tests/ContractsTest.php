<?php

use Clapp\SzamlazzhuClient\Invoice;
use Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableMerchantContract;

class ContractsTest extends TestCase{
    public function testInvoiceableCustomerContract(){
        $mock = $this->getMockBuilder(InvoiceableCustomerContract::class)
                     ->setMethods(array('getInvoiceCustomerData'))
                     ->getMock();
        $mock->expects($this->once())
             ->method('getInvoiceCustomerData');
        $invoice = new Invoice();
        $invoice->customer = $mock;
    }

    public function testInvoiceableItemCollectionContract(){

        $mock = $this->getMockBuilder(InvoiceableItemCollectionContract::class)
                     ->setMethods(array('getInvoiceItemCollectionData'))
                     ->getMock();
        $mock->expects($this->once())
             ->method('getInvoiceItemCollectionData')
             ->willReturn([]);
        $invoice = new Invoice();
        $invoice->items = $mock;
    }

    public function testInvoiceableItemContract(){
        $mock = $this->getMockBuilder(InvoiceableItemContract::class)
                     ->setMethods(array('getInvoiceItemData'))
                     ->getMock();
        $mock->expects($this->once())
             ->method('getInvoiceItemData');
        $invoice = new Invoice();
        $invoice->addItem($mock);
    }

    public function testInvoiceableMerchantContract(){
        $mock = $this->getMockBuilder(InvoiceableMerchantContract::class)
                     ->setMethods(array('getInvoiceMerchantData'))
                     ->getMock();
        $mock->expects($this->once())
             ->method('getInvoiceMerchantData');
        $invoice = new Invoice();
        $invoice->merchant = $mock;
    }
}
