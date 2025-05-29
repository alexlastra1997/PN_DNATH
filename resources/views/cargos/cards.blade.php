@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Cargos agrupados por número</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @foreach($siglas as $sigla)
        <a href="{{ route('cargos.detalle', $sigla) }}"
           class="block bg-white shadow-md rounded-lg p-6 text-center hover:bg-blue-100">
            <h3 class="text-xl font-semibold text-blue-600">{{ $sigla }}</h3>
            <p class="text-gray-500">Ver cargos</p>
        </a>
    @endforeach
</div>

</div>
@endsection
