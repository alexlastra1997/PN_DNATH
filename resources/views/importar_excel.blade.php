{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('importar')

    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4 ">Importar archivo Excel</h1>
       
        <div class="flex flex-wrap">

        </div>
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('delete'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Éxito:</strong>
                <span class="block sm:inline">{{ session('delete') }}</span>
            </div>
        @endif


        <form action="{{ route('importar.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1 font-medium">Seleccionar archivo Excel:</label>
                <input type="file" name="archivo" class="border border-gray-300 p-2 w-full rounded">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Importar
            </button>
        </form>

        {{-- Botón para eliminar todos los usuarios --}}
        <form action="{{ route('importar.excel.eliminar.todos') }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar todos los usuarios?');" class="mt-6">
            @csrf
            @method('DELETE')

            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Eliminar Base de Datos
            </button>
        </form>
    </div>
    
@endsection

