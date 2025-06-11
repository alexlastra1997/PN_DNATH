@extends('layouts.app')

@section('content')
<div class="px-4 space-y-6">

    <h2 class="text-xl font-bold text-gray-700">Usuarios sin Asignación</h2>

    <form method="GET" class="bg-white p-4 rounded shadow space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1 text-sm">Grado</label>
                <select name="grado" class="w-full border p-2 rounded text-sm">
                    <option value="">-- Todos --</option>
                    @foreach ($grados as $g)
                        <option value="{{ $g }}" {{ request('grado') == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm">Tipo Personal</label>
                <select name="tipo_personal" class="w-full border p-2 rounded text-sm">
                    <option value="">-- Todos --</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo_personal') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Filtrar</button>
        </div>
    </form>

    <div class="bg-white p-4 rounded shadow">
        @if ($usuarios->count())
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Cédula</th>
                        <th class="border p-2">Grado</th>
                        <th class="border p-2">Nombre</th>
                        <th class="border p-2">Tipo Personal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2">{{ $u->cedula }}</td>
                            <td class="border p-2">{{ $u->grado }}</td>
                            <td class="border p-2">{{ $u->apellidos_nombres }}</td>
                            <td class="border p-2">{{ $u->tipo_personal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $usuarios->withQueryString()->links() }}</div>
        @else
            <p class="text-gray-500">No hay usuarios sin asignación.</p>
        @endif
    </div>

</div>
@endsection
