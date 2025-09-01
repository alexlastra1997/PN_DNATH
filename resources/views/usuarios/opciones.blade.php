@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto p-6 bg-white rounded-lg shadow">
        <h1 class="text-xl font-bold mb-4">Cargar documento</h1>
        <form action="{{ route('usuarios.cargar_documento') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="archivo" accept=".xlsx,.xls,.csv,.txt" required
                   class="block w-full border rounded p-2 text-sm">
            @error('archivo')
            <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
            <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-gray-800">Cargar y ver resultados</button>
        </form>
    </div>
@endsection
