<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Logout</button>
            </form>
        </div>

        <!-- Import Section -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Bulk Import Products</h2>
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data"
                class="flex gap-4" id="importCsvForm">
                @csrf
                <input type="file" name="file" class="border p-2 rounded" required>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded" id="importCsv">Import CSV</button>
            </form>
            @if(session('success'))
            <div class="mt-4 text-green-600">{{ session('success') }}</div>
            @endif

            <!-- Import Progress Container -->
            <div id="import-progress" class="mt-4 hidden p-4 bg-blue-50 border border-blue-200 rounded">
                <h3 class="font-bold text-blue-800 mb-2">Importing... <span id="import-count">0</span> rows completed
                </h3>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <div id="live-import-stream" class="mt-2 text-sm text-gray-600 h-20 overflow-y-auto">
                    <!-- Live logs here -->
                </div>
            </div>
        </div>

        <!-- Real-Time Status Section -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Online Users (Real-Time)</h2>
            <div id="online-users" class="grid grid-cols-4 gap-4">
                <!-- Users will be populated here via JS -->
                <div class="p-4 bg-gray-50 border rounded flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span>Admin User (You)</span>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Product Management</h2>
            <div class="mb-4">
                {{-- <a href="{{ route('admin.products.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Add New
                    Product</a> --}}
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2">Name</th>
                        <th class="border-b p-2">Price</th>
                        <th class="border-b p-2">Stock</th>
                        <th class="border-b p-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="showImportRecords">
                    <!-- Products will be listed here -->
                    @foreach(\App\Models\Product::orderBy('id', 'desc')->take(10)->get() as $product)
                    <tr>
                        <td class="border-b p-2 showImportRecords">{{ $product->name }}</td>
                        <td class="border-b p-2 showImportRecords">${{ $product->price }}</td>
                        <td class="border-b p-2 showImportRecords">{{ $product->stock }}</td>
                        <td class="border-b p-2 showImportRecords">
                            <a href="#" class="text-blue-600">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('local', {
            cluster: 'mt1',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                }
            }
        });

        var channel = pusher.subscribe('presence-online');

        channel.bind('pusher:subscription_succeeded', function(members) {
            updateMembers(members);
        });

        channel.bind('pusher:member_added', function(member) {
            addMember(member);
        });

        channel.bind('pusher:member_removed', function(member) {
            removeMember(member);
        });

        // Listen for new products
        var productChannel = pusher.subscribe('products');
        productChannel.bind('created', function(data) {
             const tbody = document.querySelector('table tbody');
             const tr = document.createElement('tr');
             
             tr.className = "bg-green-50 transition-colors duration-1000";
             tr.innerHTML = `
                <td class="border-b p-2">${data.product.name} <span class="text-xs bg-green-500 text-white px-1 rounded">NEW</span></td>
                <td class="border-b p-2">$${data.product.price}</td>
                <td class="border-b p-2">${data.product.stock}</td>
                <td class="border-b p-2">
                    <a href="#" class="text-blue-600">Edit</a>
                </td>
             `;
             tbody.insertBefore(tr, tbody.firstChild);
             
             // Remove highlight after 2 seconds
             setTimeout(() => {
                 tr.classList.remove('bg-green-50');
             }, 2000);
        });

        // Listen for batch imports via the same products channel
        productChannel.bind('product.imported', function(data) {
            const container = document.getElementById('import-progress');
            container.classList.remove('hidden');
            
            const countEl = document.getElementById('import-count');
            let currentCount = parseInt(countEl.innerText) || 0;
            currentCount += data.products.length;
            countEl.innerText = currentCount;
            
            const logContainer = document.getElementById('live-import-stream');
            const tbody = document.querySelector('table tbody');
            
            // Process the batch
            data.products.forEach(item => {
                 // Add to log
                 const div = document.createElement('div');
                 div.className = "text-xs text-gray-600 mb-1";
                 div.innerText = `[${new Date().toLocaleTimeString()}] Imported: ${item.name}`;
                 logContainer.prepend(div);

                 // Add to main table - MATCH GENERIC STYLE
                 const tr = document.createElement('tr');
                 tr.className = "bg-green-50 border-b transition-colors duration-1000"; 
                 tr.innerHTML = `
                    <td class="border-b p-2">${item.name} <span class="text-xs bg-green-500 text-white px-1 rounded">NEW</span></td>
                    <td class="border-b p-2">$${item.price}</td>
                    <td class="border-b p-2">${item.stock}</td>
                    <td class="border-b p-2">
                        <a href="#" class="text-blue-600">Edit</a>
                    </td>
                 `;
                 tbody.insertBefore(tr, tbody.firstChild);

                 // Remove highlight after 2 seconds
                 setTimeout(() => {
                     tr.className = "bg-white border-b hover:bg-gray-50 transition-colors";
                 }, 2000);
            });
        });

        function updateMembers(members) {
            const container = document.getElementById('online-users');
            container.innerHTML = '';
            
            members.each(function(member) {
                addMember(member);
            });
        }

        function addMember(member) {
            const container = document.getElementById('online-users');
            const el = document.createElement('div');
            el.id = 'user-' + member.id;
            el.className = 'p-4 bg-gray-50 border rounded flex items-center gap-2';
            el.innerHTML = `
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span>${member.info.name} (${member.info.type})</span>
            `;
            container.appendChild(el);
        }

        function removeMember(member) {
            const el = document.getElementById('user-' + member.id);
            if (el) {
                el.remove();
            }
        }

        $(document).on('click','#importCsv',function() {
            var formData = new FormData($('#importCsvForm')[0]);
            
            $.ajax({
                url: "{{ route('admin.products.import') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    // Show loading or disable button if needed
                    $('#importCsv').prop('disabled', true).text('Uploading...');
                },
                success: function(response) {
                    // Clear the existing items so only new ones appear
                    $('table tbody').empty();
                    $('#importCsv').prop('disabled', false).text('Import CSV');
                    // Optional: Show success message toast
                },
                error: function(xhr) {
                    alert('Error uploading file');
                    $('#importCsv').prop('disabled', false).text('Import CSV');
                }
            });
        });
    </script>
</body>

</html>