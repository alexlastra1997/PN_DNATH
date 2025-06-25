<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Traslado Masivo</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h2>Resumen de Traslados Masivos</h2>

<table>
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Grado</th>
            <th>Apellidos y Nombres</th>
            <th>Unidad Origen</th>
            <th>ID Unidad Origen</th>
            <th>Función Origen</th>
            <th>ID Función Origen</th>
            <th>Unidad Destino</th>
            <th>ID Unidad Destino</th>
            <th>Función Destino</th>
            <th>ID Función Destino</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos1 as $d)
            <tr>
                <td>{{ $d['cedula'] ?? '-' }}</td>
                <td>{{ $d['grado'] ?? '-' }}</td>
                <td>{{ $d['apellidos_nombres'] ?? '-' }}</td>
                <td>{{ $d['unidad_origen'] ?? '-' }}</td>
                <td>{{ $d['id_unidad_origen'] ?? '-' }}</td>
                <td>{{ $d['funcion_origen'] ?? '-' }}</td>
                <td>{{ $d['id_funcion_origen'] ?? '-' }}</td>
                <td>{{ $d['unidad_destino'] ?? '-' }}</td>
                <td>{{ $d['id_unidad_destino'] ?? '-' }}</td>
                <td>{{ $d['funcion_destino'] ?? '-' }}</td>
                <td>{{ $d['id_funcion_destino'] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

</body>
</html>
