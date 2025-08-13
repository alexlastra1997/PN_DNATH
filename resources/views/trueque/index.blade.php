@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Trueque Carcelario - Relevo por Provincia</h2>

    <form action="{{ route('trueque.procesar') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block mb-1">Archivo: Personal en Cárceles</label>
            <input type="file" name="archivo1" required class="border p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Archivo: Personal Libre</label>
            <input type="file" name="archivo2" required class="border p-2 w-full">
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">Procesar Trueque</button>
    </form>
</div>
@endsection

