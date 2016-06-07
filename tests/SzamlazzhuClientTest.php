<?php

class SzamlazzhuClientTest extends PHPUnit_Framework_TestCase
{

    public function testSzamlazzhuClient()
    {

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

        $agent = new \Clapp\SzamlazzhuClient\SzamlazzHuClient($config);

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