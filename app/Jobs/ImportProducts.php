<?php

namespace App\Jobs;

use App\Events\ProductImported;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        if (!file_exists($this->filePath)) return;

        $file = fopen($this->filePath, 'r');
        $header = fgetcsv($file);

        $batch = [];
        $batchSize = 100; // Reduced batch size since we are doing individual creates

        while (($row = fgetcsv($file)) !== false) {
             // Create one by one to get ID
             $product = \App\Models\Product::create([
                'name'  => $row[0] ?? 'Product',
                'description' => $row[1] ?? '',
                'price' => is_numeric($row[2] ?? null) ? $row[2] : 0,
                'stock' => is_numeric($row[3] ?? null) ? $row[3] : 0,
            ]);
            
            $batch[] = $product;

            if (count($batch) >= $batchSize) {
                broadcast(new ProductImported($batch));
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            broadcast(new ProductImported($batch));
        }

        fclose($file);
    }
}
