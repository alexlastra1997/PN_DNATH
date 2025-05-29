@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Resultado de Factibilidad</h1>

    <table class="min-w-full border bg-white rounded shadow">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-2">Nombre</th>
                <th class="p-2">Unidad</th>
                <th class="p-2">Factibilidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr class="border-t">
                    <td class="p-2">{{ $usuario->apellidos_nombres }}</td>
                    <td class="p-2">{{ $usuario->unidad }}</td>
                    <td class="p-2 font-bold">
                        <span class="@if($usuario->factibilidad >= 80) text-green-600
                                    @elseif($usuario->factibilidad >= 60) text-yellow-500
                                    @else text-red-600 @endif">
                            {{ $usuario->factibilidad }}%
                            @if($usuario->factibilidad >= 80)
                                (Factible)
                            @elseif($usuario->factibilidad >= 60)
                                (Posibilidad)
                            @else
                                (Aún no factible)
                            @endif
                        </span>
                    </td>
                </tr>
                
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('usuarios.factibilidad.pdf', request()->query()) }}"
   class="inline-block mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
   Descargar PDF
</a>


    <a href="{{ route('usuarios.seleccionados') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Volver</a>
</div>
@endsection
