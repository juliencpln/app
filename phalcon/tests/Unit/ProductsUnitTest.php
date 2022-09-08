<?php

declare(strict_types=1);

namespace Tests\Unit;

require './vendor/autoload.php';

class CompaniesUnitTest extends AbstractUnitTest
{
    public function testProductStock(): void
    {
        $product = new \Products();
        $product->name = "product B";
        $product->price = "2999.99";
        $product->tax = "10";
        $product->stock = 800;

        if ($product->save() === true){
            $stock = $product->getStock($product->id);
            $this->assertSame("800", $stock);
            $this->assertIsNumeric($stock);

        }
    }


}