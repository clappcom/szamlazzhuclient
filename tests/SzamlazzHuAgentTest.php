<?php

class SzamlazzHuAgentTest extends PHPUnit_Framework_TestCase
{

    public function testSzamlazzHuAgent()
    {

        $invoice = new \SzamlazzHuAgent\Invoice();

        $invoice->setInvoiceHeader(new \SzamlazzHuAgent\Invoice\Header());
        $invoice->setBuyer(new \SzamlazzHuAgent\Invoice\Buyer());
        $invoice->setSeller(new \SzamlazzHuAgent\Invoice\Seller());
        $invoice->addItem(new \SzamlazzHuAgent\Invoice\Item());
        $invoice->addItem(new \SzamlazzHuAgent\Invoice\Item());

        $config = new \SzamlazzHuAgent\Config([
            'agentUrl' => 'https://www.szamlazz.hu/szamla/',
            'username' => 'invoicebot',
            'password' => 'Invoicebot9010',
            'xmlStoragePath' => dirname(__FILE__) . '/storage/xml',
            'pdfStoragePath' => dirname(__FILE__) . '/storage/pdf',
            'cookieStoragePath' => dirname(__FILE__) . '/storage',
        ]);

        $agent = new \SzamlazzHuAgent\SzamlazzHuAgent($config);

        $response = null;

        /*try
        {*/
            $response = $agent->send($invoice);
/*
        }catch(\SzamlazzHuAgent\Exception\AuthenticationException $e) {
            var_dump($e->getMessage());
        }catch(HttpException $e) {
            var_dump($e->getMessage());
        }catch(Exception $e) {
            var_dump($e->getMessage());
        }

*/
        $this->assertInstanceOf(\SzamlazzHuAgent\Response::class, $response);
    }

}