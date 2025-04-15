<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla {{ $table }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Tabla: {{ $table }}</h2>

        <form method="GET" action="{{ route('tabla.show', ['table' => $table]) }}" class="mb-4">
    <div class="flex items-center">
        <input type="text" name="filter" value="{{ request()->filter }}" placeholder="Buscar..." class="border p-2 rounded">
        <button type="submit" class="ml-2 bg-blue-600 text-white p-2 rounded">Filtrar</button>
    </div>
</form>

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-700 border">
        <thead class="text-xs uppercase bg-gray-200 text-gray-600">
            <tr>
                @foreach($columnas as $col)
                    <th class="px-4 py-2">
                        <a href="{{ route('tabla.show', ['table' => $table, 'sort' => $col, 'order' => (request()->order == 'asc' ? 'desc' : 'asc')]) }}">
                            {{ $col }}
                            @if (request()->sort == $col)
                                @if (request()->order == 'asc')
                                    <span>↑</span>
                                @else
                                    <span>↓</span>
                                @endif
                            @endif
                        </a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
                <tr class="bg-white border-b hover:bg-gray-50">
                    @foreach($columnas as $col)
                        <td class="px-4 py-2">{{ $registro->$col }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $registros->links() }}
</div>

<div class="mb-4">
    <a href="{{ route('tabla.export.excel', ['table' => $table]) }}" class="bg-green-600 text-white p-2 rounded mr-2">Exportar a Excel</a>
    <a href="{{ route('tabla.export.pdf', ['table' => $table]) }}" class="bg-red-600 text-white p-2 rounded">Exportar a PDF</a>
</div>


    </div>
</body>
</html>
