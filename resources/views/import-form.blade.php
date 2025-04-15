<!DOCTYPE html>
<html>
<head>
    <title>Importar Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-xl mx-auto bg-white p-8 shadow rounded">
        <h1 class="text-2xl font-bold mb-4">Importar archivo Excel</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('importar.excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo" class="mb-4" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Importar</button>
        </form>
    </div>

</body>
</html>
