@extends('layouts.app')

@section('content')
<body class="bg-gray-100">
    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Subir archivos de traslado</h1>
        <form action="{{ route('traslados.procesar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="titulo" class="block text-sm font-medium">Título del traslado</label>
    <input type="text" name="titulo" id="titulo" class="border p-2 w-full mb-4" placeholder="Ej: Traslado nombre" required>

    <input type="file" name="archivo1" required class="mb-2">
    <input type="file" name="archivo2" required class="mb-2">
    
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Generar Traslado</button>
</form>
    </div>
</body>
@endsection


