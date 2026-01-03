<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10 flex justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6">Create Product</h2>
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Description</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label class="block mb-2">Price</label>
                    <input type="text" name="price" class="w-full border p-2 rounded" required>
                </div>
                <div class="w-1/2">
                    <label class="block mb-2">Stock</label>
                    <input type="number" name="stock" class="w-full border p-2 rounded" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block mb-2">Category</label>
                <input type="text" name="category" class="w-full border p-2 rounded">
            </div>
            <div class="flex justify-between">
                <a href="{{ route('admin.products.index') }}" class="text-gray-600 py-2">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Product</button>
            </div>
        </form>
    </div>
</body>
</html>
