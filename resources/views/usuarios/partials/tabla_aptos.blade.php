<table class="w-full text-sm text-gray-700 border">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-1">Cédula</th>
            <th class="p-1">Nombre</th>
            <th class="p-1">Promoción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($aptos as $usuario)
        <tr>
            <td class="p-1">{{ $usuario->cedula }}</td>
            <td class="p-1">{{ $usuario->apellidos_nombres }}</td>
            <td class="p-1">{{ $usuario->promocion }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
