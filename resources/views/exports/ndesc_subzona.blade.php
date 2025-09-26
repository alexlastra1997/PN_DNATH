{{-- Hoja: Subzona (Resumen + Personal SIN DISTRITO) --}}
@php
    $norm = function ($s) {
        $s = trim((string)$s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
        return preg_replace('/\s+/u', ' ', $s) ?? '';
    };
@endphp

<table>
    <tr><td colspan="10"><strong>Subzona — NDESC Z{{ $zona }} / {{ $subzonaNombre }}</strong></td></tr>
</table>

<table>
    <tr><td colspan="4"><strong>Resumen (Orgánico vs Actual)</strong></td></tr>
    <tr>
        <th>Cargo</th>
        <th>Ideal</th>
        <th>Actual</th>
        <th>Estado</th>
    </tr>
    @foreach ($resumen as $r)
        <tr>
            <td>{{ $r['cargo'] }}</td>
            <td>{{ $r['ideal'] }}</td>
            <td>{{ $r['actual'] }}</td>
            <td>{{ $r['estado'] }}</td>
        </tr>
    @endforeach
</table>

<table>
    <tr><td colspan="10"></td></tr>
    <tr><td colspan="10"><strong>Personal (SIN DISTRITO)</strong></td></tr>
    <tr>
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
    @foreach ($lista as $u)
        @php
            $cargoKey   = $norm($u->funcion_efectiva ?? '');
            $validosSet = $requisitosGrado[$distrito][$cargoKey] ?? [];
            $validos    = implode(', ', array_keys($validosSet));
        @endphp
        <tr>
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
