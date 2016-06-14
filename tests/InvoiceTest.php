<?php

use Clapp\SzamlazzhuClient\Invoice;
use Illuminate\Validation\ValidationException;

use Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract;
use Clapp\SzamlazzhuClient\Contract\InvoiceableMerchantContract;

class InvoiceTest extends TestCase{
    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testEmptyInvoiceValidate(){
        $invoice = new Invoice();
        $invoice->validate();
    }
    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testEmptyInvoiceValidateItems(){
        $invoice = new Invoice();
        $invoice->validateItems();
    }
    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testEmptyInvoiceValidateMerchant(){
        $invoice = new Invoice();
        $invoice->validateMerchant();
    }
    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testEmptyInvoiceValidateCustomer(){
        $invoice = new Invoice();
        $invoice->validateCustomer();
    }
    /**
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testEmptyInvoiceValidateOrderDetails(){
        $invoice = new Invoice();
        $invoice->validateOrderDetails();
    }

    public function testInvoiceAttributeAlias(){
        $invoice = new Invoice();

        //$this->assertEquals($invoice->vevo, null);

        $invoice->customerName = "foo";

        $this->assertEquals($invoice->customerName, "foo");
        $this->assertEquals($invoice->vevo['nev'], "foo");


        $invoice->customerName = "bar";

        $this->assertEquals($invoice->vevo['nev'], "bar");
        $this->assertEquals($invoice->customerName, "bar");
    }

    public function testUnknownKeysSort(){
        $invoice = new Invoice();
        $invoice->fill([
            'aaakey' => 'bar',
            'bbbkey' => 'bar',
            'paymentMethod' => 'foo',
            'ccckey' => 'bar',
        ]);
        $sortedKeys = array_keys($invoice->toArray());
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('aaakey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('bbbkey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('ccckey', $sortedKeys));

        $invoice = new Invoice();
        $invoice->fill([
            'aaakey' => 'bar',
            'bbbkey' => 'bar',
            'currency' => 'bar',
            'paymentMethod' => 'foo',
            'ccckey' => 'bar',
        ]);
        $sortedKeys = array_keys($invoice->toArray());
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('aaakey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('bbbkey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('ccckey', $sortedKeys));

        $invoice = new Invoice();
        $invoice->fill([
            'aaakey' => 'bar',
            'bbbkey' => 'bar',
            'currency' => 'bar',
            'comment' => 'bar',
            'paymentMethod' => 'foo',
            'orderNumber' => 'foo',
            'ccckey' => 'bar',
        ]);
        $sortedKeys = array_keys($invoice->toArray());
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('aaakey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('bbbkey', $sortedKeys));
        $this->assertTrue(array_search('fejlec', $sortedKeys) < array_search('ccckey', $sortedKeys));
    }

    public function testPrefillInvoiceAttributes(){
        $name = $this->faker->name;
        $invoice = new Invoice([
            'customerName' => $name,
        ]);
        $this->assertEquals($name, $invoice->customerName);
    }

    public function testInvalidInvoiceFill(){
        try {
            $invoice = new Invoice("invalid fill");
        }catch(InvalidArgumentException $e){
            $this->setLastException($e);
        }
        $this->assertLastException(InvalidArgumentException::class);
    }

    public function testInvalidItemsAttribute(){
        $invoice = new Invoice();
        try {
            $invoice->items = "not_an_array";
        }catch(InvalidArgumentException $e){
            $this->setLastException($e);
        }
        $this->assertLastException(InvalidArgumentException::class);
    }

    public function testInvoiceValidateCustomer(){
        $invoice = new Invoice();

        try {
            $invoice->validateCustomer();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->customerName = $this->faker->name;
        $invoice->customerBillingPostcode = $this->faker->postCode;
        $invoice->customerBillingCity = $this->faker->city;

        try {
            $invoice->validateCustomer();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->customerBillingAddress = $this->faker->address;

        $this->assertEquals($invoice->validateCustomer(), true);
    }

    public function testInvoiceValidateMerchant(){
        $invoice = new Invoice();
        $invoice = new Invoice($invoice);

        try {
            $invoice->validateMerchant();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->merchantBankName = $this->faker->name;
        $invoice->merchantBankAccountNumber = $this->faker->bankAccountNumber;

        $this->assertEquals($invoice->validateMerchant(), true);
    }

    public function testInvoiceValidateItems(){
        $faker = $this->faker;
        $invoice = new Invoice();
        try {
            $invoice->validateItems();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $_items = [];
        for($i = 0; $i < 5; $i++){
            $_items[] = [
                'name' => $faker->name,
                'quantity' => $faker->numberBetween(0,12),
                'quantityUnit' => 'db',
                'netUnitPrice' => $faker->numberBetween(50, 500),
                'vatRate' => '25',
                'netValue' => $faker->numberBetween(20,150),
                'vatValue' => $faker->numberBetween(20,150),
                'grossValue' => $faker->numberBetween(20,150),
            ];
        }

        $items = $this->getMock(InvoiceableItemCollectionContract::class);
        $items->method('getInvoiceItemCollectionData')
            ->willReturn($_items);
        $invoice->items = $items;

        $this->assertEquals($invoice->validateItems(), true);
        $this->assertEquals(count($invoice->items), count($_items));
    }

    public function testValidateOrderDetails(){
        $faker = $this->faker;
        $invoice = new Invoice();
        try {
            $invoice->validateOrderDetails();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->paymentMethod = 'utánvétel';
        $invoice->currency = 'HUF';
        $invoice->language = 'hu';

        try {
            $invoice->validateOrderDetails();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $now = \Carbon\Carbon::now();

        $invoice->signatureDate = '2016-01-02';
        $invoice->settlementDate = '05/10/2015';
        $invoice->dueDate = $now;

        $now->hour(0)->minute(0)->second(0);

        $this->assertEquals((string)$invoice->signatureDate, (string)(new \Carbon\Carbon('2016-01-02')));
        $this->assertEquals((string)$invoice->settlementDate, (string)(new \Carbon\Carbon('05/10/2015')));
        $this->assertEquals((string)$invoice->dueDate, (string)(new \Carbon\Carbon($now)));

        $this->assertEquals($invoice->validateOrderDetails(), true);
    }

    public function testValidateInvoice(){
        $faker = $this->faker;
        $invoice = new Invoice();
        try {
            $invoice->validate();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->customerName = $this->faker->name;
        $invoice->customerBillingPostcode = $this->faker->postCode;
        $invoice->customerBillingCity = $this->faker->city;
        $invoice->customerBillingAddress = $this->faker->address;

        try {
            $invoice->validate();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->merchantBankName = $this->faker->name;
        $invoice->merchantBankAccountNumber = $this->faker->bankAccountNumber;

        try {
            $invoice->validate();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $_items = [];
        for($i = 0; $i < 5; $i++){
            $_items[] = [
                'name' => $faker->name,
                'quantity' => $faker->numberBetween(0,12),
                'quantityUnit' => 'db',
                'netUnitPrice' => $faker->numberBetween(50, 500),
                'vatRate' => '25',
                'netValue' => $faker->numberBetween(20,150),
                'vatValue' => $faker->numberBetween(20,150),
                'grossValue' => $faker->numberBetween(20,150),
            ];
        }

        $items = $this->getMock(InvoiceableItemCollectionContract::class);
        $items->method('getInvoiceItemCollectionData')
            ->willReturn($_items);
        $invoice->items = $items;

        try {
            $invoice->validate();
        }catch(Exception $e){
            $this->setLastException($e);
        }
        $this->assertLastException(ValidationException::class);

        $invoice->paymentMethod = 'utánvétel';
        $invoice->currency = 'HUF';
        $invoice->language = 'hu';

        $invoice->signatureDate = '2016-01-02';
        $invoice->settlementDate = '05/10/2015';
        $invoice->dueDate = \Carbon\Carbon::now();

        $this->assertEquals($invoice->validate(), true);

        $invoiceCopy = new Invoice($invoice);
        $this->assertEquals($invoiceCopy->validate(), true);

        $this->assertEquals(json_encode($invoiceCopy->toArray()), json_encode($invoice->toArray()));
    }
}

