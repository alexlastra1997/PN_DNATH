<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar Excel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">
    <h1 class="text-2xl font-bold mb-6">Importar archivo Excel</h1>

    <form action="{{ route('import.importar') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-1/2">
        @csrf
        <input type="file" name="archivo" required class="mb-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Importar</button>
    </form>
</body>
</html>
