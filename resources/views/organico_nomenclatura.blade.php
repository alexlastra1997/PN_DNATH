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

    <!-- Grid Principal -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- SECCIÓN 1: NOMENCLATURAS (1/3) -->
        <section class="bg-gray-100 p-6 rounded-lg shadow col-span-1">
            <h2 class="text-xl font-bold mb-4">Sección 1: Nomenclaturas</h2>

            @if (!empty($nombresCards))
                <div class="flex flex-col space-y-4">
                    @foreach ($nombresCards as $nombre => $cantidad)
                        <a href="/nomenclatura/{{ implode('/', array_map('rawurlencode', array_merge($niveles, [$nombre]))) }}" class="block bg-white border border-gray-300 rounded-lg shadow p-4 hover:bg-gray-200 transition">
                            <div class="text-lg font-semibold mb-2">{{ $nombre }}</div>
                            <div class="text-gray-600 text-sm">{{ $cantidad }} registros</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500">
                    No hay más niveles para mostrar.
                </div>
            @endif
        </section>

        <!-- SECCIÓN 2: NUMÉRICOS POR GRADO (2/3) -->
        <section class="col-span-2 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Sección 2: Numéricos y Cargos</h2>

            <div class="flex space-x-4 mb-6">
                <a href="{{ route('nomenclatura.exportarExcel', implode('/', array_map('rawurlencode', $niveles))) }}" 
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Generar Reporte Excel
                </a>
                <a href="{{ route('nomenclatura.exportarPDF', implode('/', array_map('rawurlencode', $niveles))) }}" 
                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Generar Reporte PDF
                </a>
            </div>


            @if ($conteoGrados->count())
                <!-- CARDS DE GRADOS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($conteoGrados as $grado => $cantidad)
                        <div class="block bg-gray-50 border border-gray-200 rounded-lg shadow p-6">
                            <div class="text-lg font-semibold mb-2">{{ $grado ?? 'Sin Grado' }}</div>
                            <div class="text-gray-600 text-sm">{{ $cantidad }} numéricos</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 mt-8">
                    No hay datos numéricos o cargos para mostrar.
                </div>
            @endif
        </section>

    </div>
</div>
@endsection
