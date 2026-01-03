<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products</h1>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Create New</a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="border-b p-2">ID</th>
                    <th class="border-b p-2">Name</th>
                    <th class="border-b p-2">Price</th>
                    <th class="border-b p-2">Category</th>
                    <th class="border-b p-2">Stock</th>
                    <th class="border-b p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="border-b p-2">{{ $product->id }}</td>
                    <td class="border-b p-2">{{ $product->name }}</td>
                    <td class="border-b p-2">{{ $product->price }}</td>
                    <td class="border-b p-2">{{ $product->category }}</td>
                    <td class="border-b p-2">{{ $product->stock }}</td>
                    <td class="border-b p-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 mr-2">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $products->links() }}
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
