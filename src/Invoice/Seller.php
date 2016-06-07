<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace Clapp\SzamlazzhuClient\Invoice;


class Seller
{

    protected $bankName = "OBER";
    protected $accountNumber = "11111111-22222222-33333333";
    protected $emailReplyto = "Számla értesítő";
    protected $emailSubject = "";
    protected $emailMessage = "";

    public function toArray() {
        return [
            "bank" => $this->bankName,
            "bankszamlaszam" => $this->accountNumber,
            "emailReplyto" => $this->emailReplyto,
            "emailTargy" => $this->emailSubject,
            "emailSzoveg" => $this->emailMessage
        ];
    }

}