@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    @if (!empty($niveles))
        <div class="mb-6 text-sm text-gray-600">
            <nav class="flex items-center space-x-2">
                <a href="{{ route('nomenclatura.index') }}" class="text-blue-600 hover:underline">Inicio</a>
                @foreach ($niveles as $index => $nivel)
                    <span>/</span>
                    <a href="/nomenclatura/{{ implode('/', array_map('rawurlencode', array_slice($niveles, 0, $index + 1))) }}" class="text-blue-600 hover:underline">
                        {{ $nivel }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-6">
        {{ !empty($niveles) ? 'Navegando: ' . implode(' / ', $niveles) : 'Seleccione un Nivel' }}
    </h1>

    @if (!empty($nombresCards))
        <!-- Mostrar niveles como Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($nombresCards as $nombre => $cantidad)
                <a href="/nomenclatura/{{ implode('/', array_map('rawurlencode', array_merge($niveles, [$nombre]))) }}" class="block bg-white border border-gray-200 rounded-lg shadow p-6 hover:bg-gray-100 transition">
                    <div class="text-xl font-semibold mb-2">{{ $nombre }}</div>
                    <div class="text-gray-600 text-sm">{{ $cantidad }} registros</div>
                </a>
            @endforeach
        </div>
    @elseif (!empty($registrosFinales))
        <!-- Mostrar tabla de registros finales -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Registros finales:</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nombre</th>
                            <th class="py-3 px-6 text-left">Nomenclatura</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($registrosFinales as $registro)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6">{{ $registro->id }}</td>
                                <td class="py-3 px-6">{{ $registro->nombre ?? '-' }}</td>
                                <td class="py-3 px-6">{{ $registro->nomenclatura }}</td>
                            </tr>
                        @endforeach
                       
                    </tbody>
                    
                </table>
            </div>
             
        </div>
    @else
        <!-- No hay más niveles -->
        <div class="col-span-3 text-center text-gray-500">
            No hay más niveles para mostrar.
        </div>
    @endif

</div>
@endsection
