{{-- Hoja: Comandantes Subzonales --}}
@php
    $norm = function ($s) {
        $s = trim((string)$s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
        return preg_replace('/\s+/u', ' ', $s) ?? '';
    };
@endphp

<table>
    <tr>
        <td colspan="10"><strong>Comandantes Subzonales — NDESC Z{{ $zona }} / {{ $subzonaNombre }}</strong></td>
    </tr>
    <tr>
        <th>BAS</th>
        <th>Cédula</th>
        <th>Apellidos y Nombres</th>
        <th>Grado</th>
        <th>Promoción</th>
        <th>Función Efectiva</th>
        <th>Fecha Efectiva</th>
        <th>Tipo traslado</th>
        <th>Grados válidos (orgánico)</th>
        <th>Nomenclatura Efectiva</th>
    </tr>

    @foreach ($leadersSubzona as $u)
        @php
            $cargoKey   = $norm($u->funcion_efectiva ?? '');
            $validosSet = $requisitosGrado['SIN DISTRITO'][$cargoKey] ?? [];
            $validos    = implode(', ', array_keys($validosSet));
        @endphp
        <tr>
            <td>—</td>
            <td>{{ $u->cedula }}</td>
            <td>{{ $u->apellidos_nombres }}</td>
            <td>{{ $u->grado }}</td>
            <td>{{ $u->promocion }}</td>
            <td>{{ $u->funcion_efectiva }}</td>
            <td>{{ $u->fecha_efectiva }}</td>
            <td>{{ $u->estado_efectivo ?? '-' }}</td>
            <td>{{ $validos }}</td>
            <td>{{ $u->nomenclatura_efectiva }}</td>
        </tr>
    @endforeach
</table>
