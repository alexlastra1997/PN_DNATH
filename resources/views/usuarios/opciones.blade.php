@extends('layouts.app')

@section('content')
    <section class="py-6">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded p-6 shadow">
            <h2 class="text-lg font-semibold mb-4">Cargar universo de cédulas</h2>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <form action="{{ route('usuarios.masivo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Archivo Excel (xlsx/xls/csv)</label>
                    <input type="file" name="archivo" required class="block w-full text-sm">
                    @error('archivo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Procesar</button>
                    <a href="{{ route('usuarios.resultados') }}" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700">Ver resultados</a>
                </div>
            </form>
        </div>
    </section>
@endsection
