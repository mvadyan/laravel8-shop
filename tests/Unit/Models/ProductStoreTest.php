<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use PHPUnit\Framework\TestCase;

class ProductStoreTest extends \Tests\TestCase
{
   // use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $product = Product::factory()->create()->first();

        $dbProduct = Product::first();
        $this->assertNotNull($dbProduct);

        $this->assertTrue($product->id == $dbProduct->id);
    }
}
