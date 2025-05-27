<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Importar Excel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Importar archivo Excel</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif


        <form action="{{ route('cargos.importar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">Seleccionar archivo Excel:</label>
                <input type="file" name="archivo" class="border border-gray-300 rounded p-2 w-full">
                @error('archivo')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Importar
            </button>
        </form>

        <form action="{{ route('cargos.eliminarTodo') }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar todos los registros?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                 Eliminar todo
            </button>
        </form>
    </div>
</body>
</html>
