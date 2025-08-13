@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-lg font-bold mb-4">Importar Reporte Orgánico</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('reporte-organico.importar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="archivo" class="border p-2 mb-4 w-full" required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Subir Archivo
        </button>
    </form>
</div>
@endsection
