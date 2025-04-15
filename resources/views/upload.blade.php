<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar Excel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Subir archivo Excel</h1>

        <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo" required class="mb-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Importar</button>
        </form>
    </div>
</body>
</html>
