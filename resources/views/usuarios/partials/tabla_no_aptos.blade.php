<table class="w-full text-sm text-red-800 border">
    <thead>
        <tr class="bg-red-100 font-bold">
            <th class="p-1">Cédula</th>
            <th class="p-1">Nombre</th>
            <th class="p-1">Promoción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($no_aptos as $usuario)
        <tr class="bg-red-50">
            <td class="p-1">{{ $usuario->cedula }}</td>
            <td class="p-1">{{ $usuario->apellidos_nombres }}</td>
            <td class="p-1">{{ $usuario->promocion }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
