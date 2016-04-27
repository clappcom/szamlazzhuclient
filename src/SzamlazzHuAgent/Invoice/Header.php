<?php
/**
 * Created by PhpStorm.
 * User: Creev
 * Date: 2016.03.18.
 * Time: 12:33
 */

namespace SzamlazzHuAgent\Invoice;


class Header
{
    protected $date = '2000-01-01';
    protected $completeDate = '2000-01-01';
    protected $dueDate = '2000-01-01';
    protected $paymentType = 'Átutalás';
    protected $currency = 'Ft';
    protected $languageOfInvoice = 'hu';
    protected $comment = '';
    protected $rateBank = 'MNB';
    protected $rate = 0.0;
    protected $purchaseOrder = '';
    protected $depositInvoice = false;
    protected $endInvoice = false;
    protected $dijbekeroInvoice = false;
    protected $prefix = 'TEST';
    protected $paid = false;

    public function toArray() {
        return [
            "keltDatum" => $this->date,
            "teljesitesDatum" => $this->completeDate,
            "fizetesiHataridoDatum" => $this->dueDate,
            "fizmod" => $this->paymentType,
            "penznem" => $this->currency,
            "szamlaNyelve" => $this->languageOfInvoice,
            "megjegyzes" => $this->comment,
            "arfolyamBank" => $this->rateBank,
            "arfolyam" => $this->rate,
            "rendelesSzam" => $this->purchaseOrder,
            "elolegszamla" => ($this->depositInvoice) ? 'true' : 'false',
            "vegszamla" => ($this->endInvoice) ? 'true' : 'false',
            "dijbekero" => ($this->dijbekeroInvoice) ? 'true' : 'false',
            "szamlaszamElotag" => $this->prefix,
            "fizetve" => ($this->paid) ? 'true' : 'false'
        ];
    }
}