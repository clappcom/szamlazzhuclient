<?php

namespace Clapp\SzamlazzhuClient\Invoice;


class Pdf
{

    protected $filename;
    protected $path;

    public function __construct($pdfFile, $content)
    {

        file_put_contents($pdfFile, $content);

    }

}