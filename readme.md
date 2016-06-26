Clapp/SzamlazzhuClient [![Build Status](https://travis-ci.org/clappcom/szamlazzhuclient.svg)](https://travis-ci.org/clappcom/szamlazzhuclient) [![Coverage](https://coveralls.io/repos/github/clappcom/szamlazzhuclient/badge.svg?branch=master)](https://coveralls.io/github/clappcom/szamlazzhuclient)
===
Nem hivatalos Számlázz.hu PHP kliens. Kísérleti verzió, production-ben nem ajánlott. Használat csak saját felelősségre.

Telepítés
---

```
composer require clapp/szamlazzhuclient:1.*@beta
```

Egyszerű minta
---

```php
$invoice = new Clapp\SzamlazzhuClient\Invoice();

$invoice->customerName = "Foo Bar";
$invoice->customerBillingPostcode = "1234";
$invoice->customerBillingCity = "Budapest";
$invoice->customerBillingAddress = "Foo utca 1.";

$invoice->merchantBankName = "FooBank";
$invoice->merchantBankAccountNumber = "12345678-12345678-12345678";

$invoice->items = [
    [
        'name' => 'Minta Termék',
        'quantity' => 2,
        'quantityUnit' => 'db',
        'netUnitPrice' => 100,
        'vatRate' => '25',
        'netValue' => 200,
        'vatValue' => 50,
        'grossValue' => 250,
    ],
    [
        'name' => 'Minta Termék 2',
        'quantity' => 1,
        'quantityUnit' => 'db',
        'netUnitPrice' => 100,
        'vatRate' => '25',
        'netValue' => 100,
        'vatValue' => 25,
        'grossValue' => 125,
    ],
];

$invoice->paymentMethod = 'utánvétel';
$invoice->currency = 'HUF';
$invoice->language = 'hu';

$invoice->signatureDate = '2016-01-02';
$invoice->settlementDate = '05/10/2015';
$invoice->dueDate = '2016-01-02';

try {
    /**
     * számla mezőinek ellenőrzése
     */
    $invoice->validate();
}catch(Illuminate\Validation\ValidationException $e){
    /**
     * hibás vagy hiányzó mezők
     */
    // var_dump( $e->validator->getMessages() );
}

$client = new Clapp\SzamlazzhuClient\SzamlazzhuClient();
$client->username = /* Számlázz.hu felhasználónév */;
$client->password = /* Számlázz.hu jelszó */;

try {
    $pdfContents = $client->generateInvoicePdf($invoice);
}catch(Clapp\SzamlazzhuClient\SzamlazzhuApiException $e){
    // var_dump( $e->getCode(), $e->getMessage() ); //API-ból származó hibakód és hibaüzenet
}
file_put_contents("szamlam.pdf", $pdfContents);
```

További minták és használat
---

[Github wikiben](https://github.com/clappcom/szamlazzhuclient/wiki/Használat)

Dokumentáció
---

[Github wikiben](https://github.com/clappcom/szamlazzhuclient/wiki/Dokumentáció)
