<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Usuarios PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h2>Listado de Usuarios</h2>

    <table>
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Grado</th>
                <th>Nombre</th>
                <th>Titulo Senecyt</th>
                <th>Capacitacion</th>
                <th>Unidad</th>
                <th>Funcion que cumple</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->cedula }}</td>
                    <td>{{ $usuario->grado }}</td>
                    <td>{{ $usuario->apellidos_nombres }}</td>
                    <td>{{ $usuario->titulos_senescyt }}</td>
                    <td>{{ $usuario->capacitacion }}</td>
                    <td>{{ $usuario->unidad }}</td>
                    <td>{{ $usuario->funcion }}</td>

                    capacitacion
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay usuarios para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
