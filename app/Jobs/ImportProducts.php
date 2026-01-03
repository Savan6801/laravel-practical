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
        if (!file_exists($this->filePath)) return;

        $file = fopen($this->filePath, 'r');
        $header = fgetcsv($file);

        $batch = [];
        $batchSize = 500;

        while (($row = fgetcsv($file)) !== false) {

            $batch[] = [
                'name'  => $row[0] ?? 'Product',
                'price' => is_numeric($row[2] ?? null) ? $row[2] : 0,
                'stock' => is_numeric($row[3] ?? null) ? $row[3] : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('products')->insert($batch);
                broadcast(new ProductImported($batch));
                $batch = [];
            }
        }


        if ($batch) {
            DB::table('products')->insert($batch);
            broadcast(new ProductImported($batch));
        }

        fclose($file);
    }
}
