<table>
    <thead>
        <tr>
            <th>Nomenclatura</th>
            <th>Grado</th>
            <th>Función</th>
            <th>Cantidad Ideal</th>
            <th>Cantidad Efectiva</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registros as $registro)
            <tr>
                <td>{{ $registro->nomenclatura }}</td>
                <td>{{ $registro->grado }}</td>
                <td>{{ $registro->funcion }}</td>
                <td>{{ $registro->cantidad_ideal }}</td>
                <td>{{ $registro->efectivo }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@php
    $resagados = collect();
    foreach ($registros as $registro) {
        if ($registro->usuarios) {
            foreach ($registro->usuarios as $usuario) {
                if ($usuario->grado !== $registro->grado) {
                    $resagados->push($usuario);
                }
            }
        }
    }
@endphp

@if ($resagados->count())
    <table>
        <thead>
            <tr>
                <th colspan="4">RESAGADOS</th>
            </tr>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Grado</th>
                <th>Nomenclatura</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resagados as $resagado)
                <tr style="background-color: #cbe6ff">
                    <td>{{ $resagado->cedula }}</td>
                    <td>{{ $resagado->apellidos_nombres }}</td>
                    <td>{{ $resagado->grado }}</td>
                    <td>{{ $resagado->nomeclatura_efectivo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
