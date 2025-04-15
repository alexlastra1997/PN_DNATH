<table>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Grado</th>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Unidad</th>
            <th>Función</th>
            <th>Tipo de Personal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->cedula }}</td>
                <td>{{ $usuario->grado }}</td>
                <td>{{ $usuario->apellidos_nombres }}</td>
                <td>{{ $usuario->sexo }}</td>
                <td>{{ $usuario->unidad }}</td>
                <td>{{ $usuario->funcion }}</td>
                <td>{{ $usuario->tipo_personal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
