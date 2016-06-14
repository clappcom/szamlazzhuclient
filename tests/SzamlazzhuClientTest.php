<?php

use Clapp\SzamlazzhuClient\SzamlazzhuClient;
use Clapp\SzamlazzhuClient\Invoice;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract;
use Clapp\SzamlazzhuClient\SzamlazzhuApiException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class SzamlazzhuClientTest extends TestCase
{
    public function testMissingCredentials()
    {

        $invoice = $this->fakeInvoice();

        $client = new SzamlazzhuClient();

        $mock = new MockHandler([
            new Response(200, ['szlahu_error_code' => '10', 'szlahu_error' => urlencode('mocked error #1')]),
        ]);

        $client->setHandler(HandlerStack::create($mock));

        try {
            $pdfContents = $client->generateInvoicePdf($invoice);
        }catch(InvalidArgumentException $e){
            $this->setLastException($e);
        }
        $this->assertLastException(InvalidArgumentException::class);
    }

    public function testInvalidInvoice(){
        $invoice = new Invoice();

        $client = new SzamlazzhuClient();

        $mock = new MockHandler([
            new Response(200, ['szlahu_error_code' => '10', 'szlahu_error' => urlencode('mocked error #2')]),
        ]);

        $client->setHandler(HandlerStack::create($mock));

        try {
            $pdfContents = $client->generateInvoicePdf($invoice);
        }catch(InvalidArgumentException $e){
            $this->setLastException($e);
        }
        $this->assertLastException(InvalidArgumentException::class);
    }

    public function testApiErrorMessage(){
        $invoice = $this->fakeInvoice();

        $client = new SzamlazzhuClient();
        $client->username = 'foo';
        $client->password = 'bar';

        $mock = new MockHandler([
            new Response(200, ['szlahu_error_code' => '10', 'szlahu_error' => urlencode('mocked error #3')]),
        ]);

        $client->setHandler(HandlerStack::create($mock));

        try {
            $pdfContents = $client->generateInvoicePdf($invoice);
        }catch(SzamlazzhuApiException $e){
            $this->setLastException($e);
        }
        $this->assertLastException(SzamlazzhuApiException::class);
    }

    public function testSuccessfulPdfGeneration(){

        $invoice = $this->fakeInvoice();

        $client = new SzamlazzhuClient();
        $client->username = 'foo';
        $client->password = 'bar';

        $mock = new MockHandler([
            new Response(200, [], 'fakepdfcontents'),
        ]);
        $handler = HandlerStack::create($mock);

        $client->setHandler($handler);

        $pdfContents = $client->generateInvoicePdf($invoice);
    }



    protected function fakeInvoice(){
        $faker = $this->faker;
        $invoice = new Invoice();

        $invoice->customerName = $this->faker->name;
        $invoice->customerBillingPostcode = $this->faker->postCode;
        $invoice->customerBillingCity = $this->faker->city;
        $invoice->customerBillingAddress = $this->faker->address;


        $invoice->merchantBankName = $this->faker->name;
        $invoice->merchantBankAccountNumber = $this->faker->bankAccountNumber;

        $_items = [];
        for($i = 0; $i < 5; $i++){
            $item = [
                'name' => $faker->name,
                'quantity' => $faker->numberBetween(0,12),
                'quantityUnit' => 'db',
                'netUnitPrice' => $faker->numberBetween(50, 500),
                'vatRate' => '25',
                'netValue' => $faker->numberBetween(20,150),
                'vatValue' => $faker->numberBetween(20,150),
                'grossValue' => $faker->numberBetween(20,150),
            ];
            $item['netValue'] = $item['quantity'] * $item['netUnitPrice'];
            $item['vatValue'] = $item['netValue'] * ($item['vatRate'] / 100);
            $item['grossValue'] = $item['netValue'] + $item['vatValue'];

            $_items[] = $item;
        }

        $items = $this->getMock(InvoiceableItemCollectionContract::class);
        $items->method('getInvoiceItemCollectionData')
            ->willReturn($_items);
        $invoice->items = $items;

        $invoice->paymentMethod = 'utánvétel';
        $invoice->currency = 'HUF';
        $invoice->language = 'hu';

        $invoice->signatureDate = '2016-01-02';
        $invoice->settlementDate = '05/10/2015';
        $invoice->dueDate = \Carbon\Carbon::now();

        return $invoice;
    }

}
