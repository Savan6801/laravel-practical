<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\ImportProducts;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $path = $request->file('file')->store('imports');

        ImportProducts::dispatch(storage_path('app/' . $path));

        return back()->with('success', 'Import started');
    }
}
