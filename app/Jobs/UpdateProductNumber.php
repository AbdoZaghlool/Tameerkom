<?php

namespace App\Jobs;

use App\Product;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProductNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->products->count() > 0) {
            foreach ($this->products as $product_id => $quantity) {
                $product = Product::find($product_id);
                if ($product->sku >= $quantity) {
                    $product->update([
                        'sku' => $product->sku - $quantity
                    ]);
                }
            }
        }
    }
}
