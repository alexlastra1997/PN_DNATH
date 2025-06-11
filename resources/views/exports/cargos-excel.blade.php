<table>
    <thead>
        <tr>
            <th>Nomenclatura</th>
            <th>Función</th>
            <th>Tipo Personal</th>
            <th>Grado</th> <!-- 👈 Grado antes -->
            <th>Cantidad Ideal</th>
            <th>Efectivo</th>
            <th>Estado</th> <!-- 👈 Nueva columna -->
        </tr>
    </thead>
    <tbody>
        @foreach ($registros as $registro)
        <tr>
            <td>{{ $registro->nomenclatura }}</td>
            <td>{{ $registro->funcion }}</td>
            <td>{{ $registro->tipo_personal }}</td>
            <td>{{ $registro->grado }}</td> <!-- 👈 Grado aquí -->
            <td>{{ $registro->cantidad_ideal }}</td>
            <td>{{ $registro->efectivo }}</td>
            <td>{{ $registro->estado }}</td> <!-- 👈 Estado aquí -->
        </tr>
        @endforeach
    </tbody>
</table>
