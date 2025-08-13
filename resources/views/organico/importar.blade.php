@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Importar Orgánico</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">{{ session('error') }}</div>
    @endif

    <form action="{{ route('organico.importar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="archivo" class="block w-full p-2 border rounded mb-4">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Importar
        </button>
    </form>
</div>
@endsection
