<?php

use Clapp\SzamlazzhuClient\MutatorAccessible;
use Clapp\SzamlazzhuClient\Invoice;
use Carbon\Carbon;

class MutatorAccessibleTest extends TestCase{
    public function testBaseClass(){

        $model = new MutatorAccessible();
        $this->assertEquals($model->foo, null);

        $model->foo = 'bar';
        $this->assertEquals($model->foo, 'bar');

    }

    public function testExtendedClassSetterGetter(){

        $model = new Invoice();
        $this->assertEquals($model->foo, null);

        $model->foo = 'bar';
        $this->assertEquals($model->foo, 'bar');

    }

    public function testExtendedClassSetterMutator(){

        $model = new Invoice();
        $this->assertEquals($model->language, null);

        $this->expectException('InvalidArgumentException');

        $model->language = "foo";
        $this->assertEquals($model->language, null);

        $model->language = "hu";
        $this->assertEquals($model->language, "hu");


    }

    public function testExtendedClassDateSetterMutatorException(){

        $model = new Invoice();

        $this->expectException(Exception::class);
        $model->signatureDate = 'asd';
    }

    public function testExtendedClassDateSetterMutator(){

        $model = new Invoice();

        $model->signatureDate = '2016-06-11';

        $this->assertTrue($model->signatureDate instanceof Carbon);

        $this->assertEquals((string)$model->signatureDate, "2016-06-11 00:00:00");

        $model->signatureDate = '05/10/2016';

        $this->assertTrue($model->signatureDate instanceof Carbon);

        $this->assertEquals((string)$model->signatureDate, "2016-05-10 00:00:00");
    }

    public function testExtendedClassGetterMutator(){

        $model = new Invoice();
        $this->assertTrue($model->signatureDate instanceof Carbon);
    }
}
