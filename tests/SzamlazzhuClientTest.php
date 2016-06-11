<?php

use Clapp\SzamlazzhuClient\SzamlazzhuClient;

class SzamlazzhuClientTest extends TestCase
{

    public function testSzamlazzhuClient()
    {

        var_dump(SzamlazzhuClient::raw()); exit;

        /**
         * nem megadható mezők (értelmetlen őket változtatni):
         * - eszamla
         * - szamlaLetoltes
         * - valaszVerzio
         * - elolegszamla
         * - vegszamla
         * - helyesbitoszamla
         * - helyesbitettSzamlaszam
         * - dijbekero
         * - szamlaszamElotag
         * - fizetve
         * - afaErtek
         * - bruttoErtek
         *
         * sensible defaulttal rendelkező opcionális mezők:
         * - keltDatum (ma)
         * - teljesitesDatum (ma)
         * - fizetesiHataridoDatum (+30nap)
         * - penznem (HUF)
         * - szamlaNyelve (hu)
         * - megjegyzes (üres)
         * - arfolyamBank (üres)
         * - arfolyam (üres)
         * - szamlaszamElotag (üres)
         * - afaErtek (kiszámolt)
         * - bruttoErtek (kiszámolt)
         * - megjegyzes (üres)
         */
        $invoice = (new Invoice())
            ->setItems($cart)
            ->addItem($cartItem)
            ->setMerchant($merchant)
            ->setCustomer($customer);

        $invoice->getItems();
        $invoice->getMerchant();
        $invoice->getCustomer();

        $invoice->items = $cart;


        $invoice->signatureDate = new Carbon();
        $invoice->fulfilmentDate = new Carbon();
        $invoice->dueDate = new Carbon();
        $invoice->paymentMethod = "foo";
        $invoice->currency = "bar";
        $invoice->language = "hu"; //de, en, it
        $invoice->comment = "foo bar";
        $invoice->exchangeRate = 0.0;
        $invoice->exchangeRateBank = "FooBank";
        $invoice->orderNumber = "uniquefoo1234";
        $invoice->bankNumberPrefix = "";

        $arr = $invoice->toArray();
        $invoice = new Invoice($arr);

        try {
            $invoice->validateItems();
            $invoice->validateMerchant();
            $invoice->validateCustomer();
            $invoice->validate(); //all
        }catch(Exception $e){

        }


        $client = new SzamlazzhuClient();
        $client->username = "Teszt01";
        $client->password = "s3cr3t";
        try {
            $pdfContents = $client->generateInvoicePdf($invoice);
        }catch(Exception $e){

        }



        $invoice = new \Clapp\SzamlazzhuClient\Invoice();

        $invoice->setInvoiceHeader(new \Clapp\SzamlazzhuClient\Invoice\Header());
        $invoice->setBuyer(new \Clapp\SzamlazzhuClient\Invoice\Buyer());
        $invoice->setSeller(new \Clapp\SzamlazzhuClient\Invoice\Seller());
        $invoice->addItem(new \Clapp\SzamlazzhuClient\Invoice\Item());
        $invoice->addItem(new \Clapp\SzamlazzhuClient\Invoice\Item());

        $config = new \Clapp\SzamlazzhuClient\Config([
            'agentUrl' => 'https://www.szamlazz.hu/szamla/',
            'username' => 'invoicebot',
            'password' => 'Invoicebot9010',
            'xmlStoragePath' => dirname(__FILE__) . '/storage/xml',
            'pdfStoragePath' => dirname(__FILE__) . '/storage/pdf',
            'cookieStoragePath' => dirname(__FILE__) . '/storage',
        ]);

        $agent = new \Clapp\SzamlazzhuClient\SzamlazzhuClient($config);

        $response = null;

        /*try
        {*/
            $response = $agent->send($invoice);
/*
        }catch(\Clapp\SzamlazzhuClient\Exception\AuthenticationException $e) {
            var_dump($e->getMessage());
        }catch(HttpException $e) {
            var_dump($e->getMessage());
        }catch(Exception $e) {
            var_dump($e->getMessage());
        }

*/
        $this->assertInstanceOf(\Clapp\SzamlazzhuClient\Response::class, $response);
    }

}
