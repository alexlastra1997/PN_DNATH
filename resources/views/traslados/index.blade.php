<!-- resources/views/traslados/index.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Traslados Aleatorios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Subir archivos de traslado</h1>
        <form action="{{ route('traslados.procesar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Archivo CAMBIO DE:</label>
                <input type="file" name="archivo1" class="border p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Archivo CAMBIO A:</label>
                <input type="file" name="archivo2" class="border p-2 w-full" required>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Generar archivo de traslado
            </button>
        </form>
    </div>
</body>
</html>
