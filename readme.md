Clapp/SzamlazzhuClient [![Build Status](https://travis-ci.org/clappcom/szamlazzhuclient.svg)](https://travis-ci.org/clappcom/szamlazzhuclient) [![Coverage](https://coveralls.io/repos/github/clappcom/szamlazzhuclient/badge.svg?branch=master)](https://coveralls.io/github/clappcom/szamlazzhuclient)
===
Nem hivatalos Számlázz.hu PHP kliens. Alpha verzió, production-ben nem ajánlott. Használat csak saját felelősségre.

Telepítés
---

```
composer require clapp/szamlazzhuclient:1.*@beta
```

Használat
---

### Számla létrehozása

#### Számla adatok és termékek manuális kitöltése

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

```

#### Számla létrehozása meglevő számla xml-ből

```php

$xmlContents = file_get_contents("path/to/szamla.xml");

$invoice = new Clapp\SzamlazzhuClient\Invoice($xmlContents);
```

#### Számla mezőinek kitöltése a saját rendszered Cart, Product, Customer és Merchant implementáció alapján

```php
// My/Webshop/Cart.php
/**
 * saját Kosár implementációd
 */
class Cart implements \Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract{
    /**
     * valahogyan eltárolod a kosár termékeit
     */
    protected $products = [];

    /**
     * implementálod az InvoiceableItemCollectionContract függvényét
     *
     * @return array
     * A számlára kerülő összes termék adatai.
     * A tömb elemei mind tömbök, vagy `Clapp\SzamlazzhuClient\Contract\InvoiceItemContract` instance-ok.
     */
    public function getInvoiceItemCollectionData(){
        $invoiceItems = [];
        foreach($this->products as $product){
            $invoiceItems[] = [
                'name' => $product->name,
                'quantity' => $product->quantity,
                'quantityUnit' => $product->quantityUnit,
                'netUnitPrice' => $product->netUnitPrice,
                'vatRate' => $product->vatRate,
                'netValue' => $product->netValue,
                'vatValue' => $product->vatValue,
                'grossValue' => $product->grossValue,
            ];
        }
        return $invoiceItems;
    }
}
```

```php
// My/Webshop/Product.php
/**
 * Saját Product implementációd
 */
class Product implements \Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract{
    public function getInvoiceItemData(){
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'quantityUnit' => $this->quantityUnit,
            'netUnitPrice' => $this->netUnitPrice,
            'vatRate' => $this->vatRate,
            'netValue' => $this->netValue,
            'vatValue' => $this->vatValue,
            'grossValue' => $this->grossValue,
        ];
    }
}

```

```php
// My/Webshop/User.php
/**
 * Saját User implementációd
 */
class User implements \Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract{

    public function getInvoiceCustomerData(){
        return [
            customerName: $this->first_name.' '.$this->last_name,
            customerBillingPostcode: $this->address->postcode,
            customerBillingCity: $this->address->city,
            customerBillingAddress: $this->address->address,
        ];
    }
}
```



```php
$invoice = new Clapp\SzamlazzhuClient\Invoice();

$invoice->items = $cart; //$cart egy My\Webshop\Cart instance
$invoice->customer = $user; //$user egy My\Webshop\User instance
$invoice->addItem($product); //$item egy My\Webshop\Product instance

try {
    $invoice->validateItems();
    $invoice->validateCustomer();
}catch(Illuminate\Validation\ValidationException $e){
    // var_dump( $e->validator->getMessages() );
}

```

### Számlából PDF generálás

```php
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

Dokumentáció
---

### Clapp\SzamlazzhuClient\Invoice

Egy számla adatait reprezentáló class.


#### `$invoice = new Clapp\SzamlazzhuClient\Invoice($attributes = [])`


- `$attributes` (opcionális):
    - `Clapp\SzamlazzhuClient\Invoice` másik számla
    - array számla mezők

#### `$invoice->validateCustomer()`

Ellenőrzi, hogy a számlán a Vevővel kapcsolatos adatok helyesen vannak-e kitöltve.

Dobhat:
    - `Illuminate\Validation\ValidationException`

#### `$invoice->validateMerchant()`

Ellenőrzi, hogy a számlán az Eladóval kapcsolatos adatok helyesen vannak-e kitöltve.

Dobhat:
    - `Illuminate\Validation\ValidationException`

#### `$invoice->validateItems()`

Ellenőrzi, hogy a számlán az Tételekkel kapcsolatos adatok helyesen vannak-e kitöltve.

Dobhat:
    - `Illuminate\Validation\ValidationException`

#### `$invoice->validateOrderDetails()`

Ellenőrzi, hogy a számlán az számla részleteivel kapcsolatos adatok helyesen vannak-e kitöltve.

Dobhat:
    - `Illuminate\Validation\ValidationException`

#### `$invoice->validate()`

Ellenőrzi, hogy a számla adatai helyesen vannak-e kitöltve. Ugyanaz, mint az előző valiációs függvények együtt.

Dobhat:
    - `Illuminate\Validation\ValidationException`

#### `$invoice->addItem($item)`

Egyetlen tétel hozzáadása a számlához. Ha többet szeretnél egyszerre, akkor lásd az `items` mezőt.

- `$item`:
    - `Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract` tétel
    - array tétel mezői

#### `$invoice->toArray()`

A számla által összeállított array. A mezők sorrendje a számla generáláshoz szükséges XSD sémának megfelelő.

#### Getter-setter mezők

Csoportos:

- `customer` - Egy vevő összes adatának beállítása vagy lekérdezése egyszerre.
    - `Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract` vevő
    - array vevő mezői
- `merchant` - Egy eladó összes adatának beállítása vagy lekérdezése egyszerre.
    - `Clapp\SzamlazzhuClient\Contract\InvoiceableMerchantContract` eladó
    - array eladó mezői
- `items` - A számla tételeinek beállítása vagy lekérdezése egyszerre.
    - `Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract` tételek
    - array tételek

Egyenként is állítható:

- `customerName` (kötelező) string
- `customerBillingPostcode` (kötelező) string
- `customerBillingCity` (kötelező) string
- `customerBillingAddress` (kötelező) string
- `customerEmail`: string email

> ha meg van adva, akkor erre az email címre kiküldi a számlát a Számlázz.hu

- `customerShouldReceiveNotification`: boolean

> kérheti, hogy érvényes email cím esetén mégse küldje el a Számla Agent a számlaértesítő email-t

- `customerTaxNumber` string
- `customerShippingName` string
- `customerShippingPostcode` string
- `customerShippingCity` string
- `customerShippingAddress` string
- `customerSignerName` string
- `customerPhone` string
- `customerComment` string
- `merchantBankName` (kötelező) string
- `merchantBankAccountNumber` (kötelező) string
- `merchantEmailReplyto` string
- `merchantEmailSubject` string
- `merchantEmailText` string
- `merchantSignerName`: string

> Ha a [Számlázz.hu-n a] beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt

- `signatureDate` (kötelező) - dátum
    - string dátum - bármilyen formátumban, amit a `Carbon\Carbon` értelmezni tud
    - `Carbon\Carbon` instance
- `settlementDate` (kötelező) - dátum
    - string dátum - bármilyen formátumban, amit a `Carbon\Carbon` értelmezni tud
    - `Carbon\Carbon` instance
- `dueDate` (kötelező) - dátum
    - string dátum - bármilyen formátumban, amit a `Carbon\Carbon` értelmezni tud
    - `Carbon\Carbon` instance
- `paymentMethod` (kötelező) string
- `currency` (kötelező) string három karakteres pénznem, pl. "HUF"
- `language` (kötelező) string egyike ezeknek: `hu`, `en`, `de`, `it`
- `comment` string
- `exchangeRateBank` string - melyik bank árfolyamán kell érteni az `exchangeRate`-t
- `exchangeRate` double - ha a `currency` nem "HUF", akkor megadható az árfolyam
- `orderNumber` string
- `paid` boolean - fizetve van-e a számla

Egy termék lehetséges mezői:

- `name` (kötelező) string
- `quantity` (kötelező) double
- `quantityUnit` (kötelező) string
- `netUnitPrice` (kötelező) double
- `vatRate` (kötelező) string
- `netValue` (kötelező) double
- `vatValue` (kötelező) double
- `grossValue` (kötelező) double
- `comment` string

### Clapp\SzamlazzhuClient\SzamlazzhuClient

A Számlázz.hu API-jával való kommunikációt valósítja meg.

```php
$client = new SzamlazzhuClient();
```

Kötelezően beállítandó mezők:
```php
$client->username = 'foo'; // string Számlázz.hu felhasználónév
$client->password = 'bar'; // string Számlázz.hu jelszó
```

#### `$client->generateInvoicePdf($invoice)`
PDF generálása a megadott számlából a Számlázz.hu API-ján keresztül.

- mixed `$invoice`:
    - `Clapp\SzamlazzhuClient\Invoice` számla
    - array számla mezői

- Dobhat:
    - `Clapp\SzamlazzhuClient\SzamlazzhuApiException`

- Return:
    - `Psr\Http\Message\StreamInterface` számla pdf

```php
try {
    $pdfContents = $client->generateInvoicePdf($invoice)
}catch( Clapp\SzamlazzhuClient\SzamlazzhuApiException $e ){
    // var_dump( $e->getCode(), $e->getMessage() );
}
file_put_contents('szamlam.pdf', $pdfContents);
```

### Clapp\SzamlazzhuClient\Contract\InvoiceableCustomerContract

Interface, ami egy számlázható Vevőt reprezentál.

Függvényei:
```php
/**
 * @return array A számlára kerülő vevő adatai.
 * [
 *  'customerName' => 'Kovacs Bt.',
 *  'customerBillingPostcode' => '2030',
 *  'customerBillingCity' => 'Érd',
 *  'customerBillingAddress' => 'Tárnoki út 23.',
 *  'customerEmail' => '', //ha meg van adva, akkor erre az email címre kiküldi a számlát a Számlázz.hu TESZT FIÓK ESETÉN BIZTONSÁGI OKOKBÓL NEM KÜLD A RENDSZER EMAILT. AZ EMAIL KÜLDÉS E-SZÁMLA ÉS PRÉMIUM CSOMAG ESETÉN MŰKÖDIK.
 *  'customerShouldReceiveNotification' => false, //kérheti, hogy érvényes email cím esetén mégse küldje el a Számla Agent a számlaértesítő email-t
 *  'customerTaxNumber' => '12345678-1-42',
 *  'customerShippingName' => 'Kovács Bt. postázási név', //a postázási adatok nem kötelezők
 *  'customerShippingPostcode' => '2040',
 *  'customerShippingCity' => 'Budaörs',
 *  'customerShippingAddress' => 'Szivárvány utca 8. VI.em. 42.',
 *  'customerSignerName' => 'Vevő Aláírója', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
 *  'customerPhone' => 'Tel:+3630-555-55-55, Fax:+3623-555-555',
 *  'customerComment' => 'A portáról felszólni a 214-es mellékre.',
 * ];
 */
public function getInvoiceCustomerData();
```

### Clapp\SzamlazzhuClient\Contract\InvoiceableItemCollectionContract

Interface, ami számlázható termékek listáját ("Tételek"-et) reprezentálja.

Függvényei:
```php
/**
 * @return array A számlára kerülő összes termék adatai.
 * A tömb elemei mind tömbök, vagy `Clapp\SzamlazzhuClient\Contract\InvoiceItemContract` instance-ok.
 */
public function getInvoiceItemCollectionData();
```

### Clapp\SzamlazzhuClient\Contract\InvoiceableItemContract

Interface, ami egy számlázható terméket ("Tétel"-t) reprezentál.

Függvényei:
```php
/**
 * @return array A számlára kerülő termék adatai.
 * [
 *  'name' => 'Elado izé',
 *  'quantity' => 1.0,
 *  'quantityUnit' => 'db',
 *  'netUnitPrice' => '10000',
 *  'vatRate' => '25', //ua. adható meg, mint a számlakészítés oldalon
 *  'netValue' => '10000.0',
 *  'vatValue' => '2500.0',
 *  'grossValue' => '12500.0',
 *  'comment' => 'tétel megjegyzés 1',
 * ];
 */
public function getInvoiceItemData();
```
### Clapp\SzamlazzhuClient\Contract\InvoiceableMerchantContract

Interface, ami egy számlázható Eladót reprezentál.

```php
/**
 * @return array
 * A számlára kerülő eladó adatai,
 * "az itt nem szereplő adatokat a Számlázz.hu felhasználói fiókból veszi a rendszer"
 * [
 *  'merchantBankName' => '',
 *  'merchantBankAccountNumber' => '',
 *  'merchantEmailReplyto' => '',
 *  'merchantEmailSubject' => '',
 *  'merchantEmailText' => '',
 *  'merchantSignerName' => '', //Nem kötelező adat. Ha a beállítások oldalon be van kapcsolva, akkor ez a név megjelenik az aláírásra szolgáló vonal alatt
 * ];
 */
public function getInvoiceMerchantData();
```
