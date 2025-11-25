@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-3xl px-4 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Cargar universo (Excel)</h1>

            @if(session('error'))
                <div class="mb-4 rounded-md bg-red-100 text-red-800 px-3 py-2 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('seleccion.masivo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Archivo (xlsx/xls/csv)</label>
                    <input type="file" name="archivo" required class="block w-full text-sm file:mr-4 file:px-3 file:py-2 file:rounded-md file:border-0 file:bg-primary-700 file:text-white hover:file:bg-primary-800 dark:file:bg-primary-600 dark:hover:file:bg-primary-700"/>
                    @error('archivo') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-md bg-primary-700 text-white hover:bg-primary-800">Cargar y filtrar</button>
                    <a href="{{ route('seleccion.resultados') }}" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100">Ir a resultados</a>
                </div>
            </form>
        </div>
    </section>
@endsection
