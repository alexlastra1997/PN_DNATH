@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold">Provincia: {{ strtoupper($id) }}</h1>
    <p>Información de la provincia {{ $id }} aquí.</p>
</div>



@endsection


