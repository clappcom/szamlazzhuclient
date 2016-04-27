<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:53
 */

namespace SzamlazzHuAgent;

use SzamlazzHuAgent\Exception\AuthenticationException;
use SzamlazzHuAgent\Invoice\Pdf;
use SzamlazzHuAgent\Invoice\Xml;

class Request
{

    public function send(Xml $invoiceXml)
    {
        //TODO Ide jön a curl küldés

        $config = $invoiceXml->getInvoice()->getConfig();

        $cookieStorage = $config->getCookieStoragePath();
        $pdfStorage = $config->getPdfStoragePath();

        // cookie file teljes elérési útja a szerveren
        $cookie_file = $cookieStorage . '/szamlazz_cookie.txt';

        // ebbe a fájlba menti a pdf-et, ha az xml-ben kértük
        $pdf_file = $pdfStorage . '/' . $invoiceXml->getFilename() . '.pdf';

        // ezt az xml fájlt küldi a számla agentnek
        $xmlfile = $invoiceXml->getPath();

        // a számla agentet ezen az urlen lehet elérni
        $agent_url = $config->getAgentUrl();
        // ha kérjük a számla pdf-et, akkor legyen true
        $szamlaletoltes = $config->getDownloadPdf();

        // ha még nincs --> létrehozzuk a cookie file-t --> léteznie kell, hogy a CURL írhasson bele
        if (!file_exists($cookie_file)) {
            file_put_contents($cookie_file, '');
        }

        // a CURL inicializálása
        $ch = curl_init($agent_url);

        // A curl hívás esetén tanúsítványhibát kaphatunk az SSL tanúsítvány valódiságától
        // függetlenül, ez az alábbi CURL paraméter állítással kiküszöbölhető,
        // ilyenkor nincs külön SSL ellenőrzés:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // POST-ban küldjük az adatokat
        curl_setopt($ch, CURLOPT_POST, true);

        // Kérjük a HTTP headert a válaszba, fontos információk vannak benne
        curl_setopt($ch, CURLOPT_HEADER, true);

        // változóban tároljuk a válasz tartalmát, nem írjuk a kimenetbe
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Beállítjuk, hol van az XML, amiből számlát szeretnénk csinálni (= file upload)
        // az xmlfile-t itt fullpath-al kell megadni. 5.5 vagy annál nagyobb verziójú PHP esetén
        // már a CURLFile osztály használata szükséges az xml fájl feltöltéséhez:
        // http://stackoverflow.com/questions/17032990/can-anyone-give-me-an-example-for-phps-curlfile-class
        // http://php.net/manual/en/class.curlfile.php
        // Kb így néz ki CURLFile használatával:
        //    curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile'=>new CURLFile($xmlfile, 'application/xml', ‘filenev')));
        // És még egy opciót szükséges ilyenkor beállítani:
        //    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile' => new \CURLFile($xmlfile, 'application/xml', 'filenev')));

        //curl_setopt($ch, CURLOPT_POSTFIELDS, array('action-xmlagentxmlfile' => '@' . $xmlfile));

        // 30 másodpercig tartjuk fenn a kapcsolatot (ha valami bökkenő volna)
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Itt állítjuk be, hogy az érkező cookie a $cookie_file-ba kerüljön mentésre
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

        // Ha van már cookie file-unk, és van is benne valami, elküldjük a Számlázz.hu-nak
        if (file_exists($cookie_file) && filesize($cookie_file) > 0) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }

        // elküldjük a kérést a Számlázz.hu felé, és eltároljuk a választ
        $agent_response = curl_exec($ch);


        // kiolvassuk a curl-ból volt-e hiba
        $http_error = curl_error($ch);

        // ezekben a változókban tároljuk a szétbontott választ
        $agent_header = '';
        $agent_body = '';
        $agent_http_code = '';

        // lekérjük a válasz HTTP_CODE-ját, ami ha 200, akkor a http kommunikáció rendben volt
        // ettől még egyáltalán nem biztos, hogy a számla elkészült
        $agent_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // a válasz egy byte kupac, ebből az első "header_size" darab byte lesz a header
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        // a header tárolása, ebben lesznek majd a számlaszám, bruttó nettó összegek, errorcode, stb.
        $agent_header = substr($agent_response, 0, $header_size);

        // a body tárolása, ez lesz a pdf, vagy szöveges üzenet
        $agent_body = substr($agent_response, $header_size);

        // a curl már nem kell, lezárjuk
        curl_close($ch);

        // a header soronként tartalmazza az információkat, egy tömbbe teszük a külön sorokat
        $header_array = explode("\n", $agent_header);

        // ezt majd true-ra állítjuk ha volt hiba
        $volt_hiba = false;

        // ebben lesznek a hiba információk, plusz a bodyban
        $agent_error = '';
        $agent_error_code = '';

        //var_dump($header_array);

        // menjünk végig a header sorokon, ami "szlahu"-val kezdődik az érdekes nekünk és írjuk ki
        foreach ($header_array as $val) {
            if (substr($val, 0, strlen('szlahu')) === 'szlahu') {
                //echo urldecode($val) . '<br>';
                // megvizsgáljuk, hogy volt-e hiba
                if (substr($val, 0, strlen('szlahu_error:')) === 'szlahu_error:') {
                    // sajnos volt
                    $volt_hiba = true;
                    $agent_error = substr($val, strlen('szlahu_error:'));
                }
                if (substr($val, 0, strlen('szlahu_error_code:')) === 'szlahu_error_code:') {
                    // sajnos volt
                    $volt_hiba = true;
                    $agent_error_code = substr($val, strlen('szlahu_error_code:'));
                }
            }
        }

        // ha volt http hiba dobunk egy kivételt
        if ($http_error != "") {
            throw new \HttpException($http_error);
        }

        if ($volt_hiba) {

            // ha a számla nem készült el kiírjuk amit lehet
            //echo 'Agent hibakód: ' . $agent_error_code . '<br>';
            //echo 'Agent hibaüzenet: ' . urldecode($agent_error) . '<br>';
            //echo 'Agent válasz: ' . urldecode($agent_body) . '<br>';

            switch($agent_error_code)
            {
                case 7:
                    throw new AuthenticationException($agent_error);
                default:
                    throw new \Exception('Számlakészítés sikertelen:' . $agent_error_code . ' - ' . $agent_error);
            }

        } else {

            // ha nem volt hiba feldolgozzuk a válaszban érkezett pdf-et vagy szöveges információt
            if ($szamlaletoltes) {
                // lementjük a pdf file-t
                $pdf = new Pdf($pdf_file, $agent_body);

                $response = new Response();
                return $response->setParam('pdf', $pdf);

            } else {
                // ha nem kértük a pdf-et akkor szöveges információ jött a válaszban, ezt kiírjuk
                echo urldecode($agent_body);
            }
        }


        return new Response();
    }

}