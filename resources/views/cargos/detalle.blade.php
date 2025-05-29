@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Manual de Cargos: {{ $numero }}</h1>

    <table class="min-w-full bg-white shadow rounded border text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">Nomenclatura</th>
                <th class="p-2 border">Cargo</th>
                <th class="p-2 border">Dir. Máx</th>
                <th class="p-2 border">Dir. Mín</th>
                <th class="p-2 border">Téc. Máx</th>
                <th class="p-2 border">Téc. Mín</th>
                <th class="p-2 border">Ideal</th>
                <th class="p-2 border">Real</th>
                <th class="p-2 border">Dif.</th>
                <th class="p-2 border text-center">Servidor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cargos as $cargo)
            <tr class="border-t">
                <td class="p-2 border">{{ $cargo->numero }}</td>
                <td class="p-2 border">{{ $cargo->cargo ?? '-' }}</td>
                <td class="p-2 border">{{ $cargo->directivo_maximo ?? '-' }}</td>
                <td class="p-2 border">{{ $cargo->directivo_minimo ?? '-' }}</td>
                <td class="p-2 border">{{ $cargo->tecnico_maximo ?? '-' }}</td>
                <td class="p-2 border">{{ $cargo->tecnico_minimo ?? '-' }}</td>
                <td class="p-2 border text-center">{{ $cargo->org_cantidad_ideal ?? '-' }}</td>
                <td class="p-2 border text-center">{{ $cargo->org_cantidad_real ?? '-' }}</td>
                <td class="p-2 border text-center">
                    @if($cargo->org_diferencia > 0)
                        <span class="text-green-600 font-bold">{{ $cargo->org_diferencia }}</span>
                    @elseif($cargo->org_diferencia < 0)
                        <span class="text-red-600 font-bold">{{ $cargo->org_diferencia }}</span>
                    @elseif($cargo->org_diferencia === 0)
                        <span class="text-gray-600">0</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-2 border text-center">
                    <a href="{{ route('cargos.ocupado', $cargo->cargo) }}"
                    class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                        Ver ocupantes
                    </a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

    <a href="{{ route('cargos.cards') }}"
       class="mt-6 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        ← Volver a tarjetas
    </a>
</div>
@endsection
