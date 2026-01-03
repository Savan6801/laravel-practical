<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input type="email" name="email" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Password</label>
                <input type="password" name="password" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-6">
                <label class="block mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border p-2 rounded" required>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded">Register</button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-600">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
