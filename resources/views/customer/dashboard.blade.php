<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Welcome, {{ Auth::user()->name }}</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Logout</button>
            </form>
        </div>
        <div class="p-4 bg-green-100 text-green-800 rounded">
            You are logged in! Your status is currently <strong>Online</strong>.
        </div>
    </div>
</body>
</html>
