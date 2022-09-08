<?php

declare(strict_types=1);

namespace Tests\Unit;

require './vendor/autoload.php';

class ProductsUnitTest extends AbstractUnitTest
{
    public function testCompanyBalance(): void
    {
        $company = new \Companies();
        $company->name = "Company B";
        $company->balance = 80000;
        $company->country = "France";

        if ($company->save() === true){
            $balance = $company->getBalance($company->id);
            $this->assertSame("80000", $balance);
            $this->assertIsNumeric($balance);
        }
    }
}


