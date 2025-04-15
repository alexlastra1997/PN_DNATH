<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Lista de Usuarios</h1>

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

</body>
</html>
