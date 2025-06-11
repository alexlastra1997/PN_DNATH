<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cargos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h1>Reporte de Cargos</h1>

    @foreach ($registros as $registro)
        <h2>{{ $registro->nomenclatura }} - {{ $registro->funcion }}</h2>
        <p><strong>Tipo Personal:</strong> {{ $registro->tipo_personal }}</p>
        <p><strong>Grado:</strong> {{ $registro->grado }}</p>
        <p><strong>Cantidad Ideal:</strong> {{ $registro->cantidad_ideal }}</p>
        <p><strong>Efectivo:</strong> {{ $registro->efectivo }}</p>

        @if ($registro->usuarios->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Apellidos y Nombres</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registro->usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->cedula }}</td>
                            <td>{{ $usuario->apellidos_nombres }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p><em>No hay usuarios en este cargo.</em></p>
        @endif

        <hr>
    @endforeach

</body>
</html>
