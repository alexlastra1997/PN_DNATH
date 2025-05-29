<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe de Factibilidad</title>
    <style>
    @page {
        margin: 120px 50px 100px 50px; /* margen: top, right, bottom, left */
    }

    body {
        font-family: sans-serif;
        font-size: 12px;
    }

    header {
        position: fixed;
        top: -100px;
        left: 0;
        right: 0;
        height: 80px;
        text-align: center;
    }

    footer {
        position: fixed;
        bottom: -60px;
        left: 0;
        right: 0;
        height: 50px;
        text-align: center;
    }

    .content {
        margin-top: 20px;
    }

    .barra {
        height: 15px;
        background-color: #ddd;
        position: relative;
        margin-bottom: 10px;
    }

    .relleno {
        height: 100%;
        background-color: #3490dc;
        text-align: right;
        padding-right: 5px;
        color: white;
        font-size: 11px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 6px;
        font-size: 11px;
    }

    th {
        background: #f3f3f3;
    }

    .semaforo-verde { color: green; }
    .semaforo-amarillo { color: orange; }
    .semaforo-rojo { color: red; }
</style>

</head>
<body>

<header>
    <img src="{{ public_path('images/pn.png') }}" style="width: 100%; max-height: 90px;">
</header>

<footer>
    <img src="{{ public_path('images/pn2.png') }}" style="width: 100%; max-height: 60px;">
</footer>

<div class="content">
    <h3 style="text-align:center;">Informe de Evaluación de Factibilidad de Cargo</h3>
    <p><strong>Generado por:</strong> {{ $user->name ?? 'Usuario' }} <br>
    <strong>Fecha:</strong> {{ $fecha }}</p>

    @foreach($usuarios as $usuario)
        <h4>{{ $usuario->apellidos_nombres }} – {{ $usuario->cedula }}</h4>

        <div class="barra">
            <div class="relleno" style="width: {{ $usuario->factibilidad }}%;">
                {{ $usuario->factibilidad }}%
            </div>
        </div>

        <p class="@if($usuario->factibilidad >= 80) semaforo-verde
                  @elseif($usuario->factibilidad >= 60) semaforo-amarillo
                  @else semaforo-rojo @endif">
            @if($usuario->factibilidad >= 80)
                Factible
            @elseif($usuario->factibilidad >= 60)
                Posibilidad
            @else
                Aún no factible
            @endif
        </p>

        <table>
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>Esperado</th>
                    <th>Valor del Usuario</th>
                    <th>¿Cumple?</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuario->detalle as $fila)
                    <tr>
                        <td>{{ $fila['campo'] }}</td>
                        <td>{{ strtoupper($fila['esperado']) }}</td>
                        <td>{{ strtoupper($fila['usuario']) }}</td>
                        <td style="text-align:center;">{{ $fila['cumple'] ? 'SI' : 'NO' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>

</body>

</html>
