@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <div class="flex justify-center space-x-4">
        <form action="{{ route('usuarios.masivo') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center space-y-2 border p-4 rounded shadow">
            @csrf
            <label class="font-semibold">Subir Excel de Cédulas</label>
            <input type="file" name="archivo" class="border rounded p-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Masivo</button>
        </form>

        <a href="#" class="bg-green-500 text-white px-4 py-2 rounded flex items-center justify-center">Individual</a>
    </div>

</div>
@endsection
