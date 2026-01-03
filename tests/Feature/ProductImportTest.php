<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use App\Jobs\ImportProducts;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_csv_and_job_is_dispatched(): void
    {
        Bus::fake();

        $admin = Admin::factory()->create();
        
        $file = UploadedFile::fake()->create('products.csv', 1000); // 1MB file

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.products.import'), [
                'file' => $file,
            ]);

        $response->assertSessionHas('success');
        
        // Assert Job was pushed
        Bus::assertDispatched(ImportProducts::class);
    }
}
