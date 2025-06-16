<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        .verde { background-color: #c6efce; }
        .amarillo { background-color: #ffeb9c; }
        .rojo { background-color: #ffc7ce; }
        .azul { background-color: #cfe2f3; }
    </style>
</head>
<body>
    <h2>Cargos</h2>
    <table>
        <thead>
            <tr>
                <th>Nomenclatura</th>
                <th>Grado</th>
                <th>Función</th>
                <th>Cantidad Ideal</th>
                <th>Cantidad Efectiva</th>
                <th>Diferencia</th>
                <th>Nombres</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
                <tr class="{{ $registro->color }}">
                    <td>{{ $registro->nomenclatura }}</td>
                    <td>{{ $registro->grado }}</td>
                    <td>{{ $registro->funcion }}</td>
                    <td>{{ $registro->cantidad_ideal }}</td>
                    <td>{{ $registro->cantidad_efectiva }}</td>
                    <td>{{ $registro->diferencia }}</td>
                    <td>
                        @foreach($registro->usuarios as $usuario)
                            {{ $usuario->apellidos_nombres }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($resagados) > 0)
        <h2>Resagados</h2>
        <table>
            <thead>
                <tr class="azul">
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Nomenclatura</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resagados as $r)
                    <tr class="azul">
                        <td>{{ $r->cedula }}</td>
                        <td>{{ $r->apellidos_nombres }}</td>
                        <td>{{ $r->grado }}</td>
                        <td>{{ $r->nomeclatura_efectivo }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>