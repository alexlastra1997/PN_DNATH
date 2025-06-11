@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Evaluación de Grados para el Cargo</h1>

    @if($cargoSugerido)
        <div class="bg-blue-100 text-blue-800 p-4 rounded border border-blue-300 mb-4">
            ℹ️ Se utilizó un cargo similar: <strong>{{ $cargo->cargo }}</strong>
        </div>
    @endif

    <div class="mb-4">
        {!! $mensaje !!}
    </div>

    <div class="mb-4">
        {!! $mensajeB !!}
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-2">{{ $usuarioA['apellidos_nombres'] }}</h2>
        <p><strong>Grado:</strong> {{ $usuarioA['grado'] }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-2">{{ $usuarioB->apellidos_nombres }}</h2>
        <p><strong>Grado:</strong> {{ $usuarioB->grado }}</p>
        <p><strong>Función:</strong> {{ $usuarioB->funcion }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-2">Datos del Cargo</h2>
        <p><strong>Cargo:</strong> {{ $cargo->cargo }}</p>
        <p><strong>Directivo mínimo:</strong> {{ $cargo->directivo_minimo }}</p>
        <p><strong>Directivo máximo:</strong> {{ $cargo->directivo_maximo }}</p>
        <p><strong>Técnico mínimo:</strong> {{ $cargo->tecnico_minimo }}</p>
        <p><strong>Técnico máximo:</strong> {{ $cargo->tecnico_maximo }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('organico.efectivo.seleccionados') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            ← Volver a Resultados
        </a>
    </div>
</div>
@endsection
