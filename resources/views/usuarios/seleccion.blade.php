@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Resultado de selección</h1>

{{-- APTOS (seleccionados) --}}
<div class="bg-white rounded shadow overflow-x-auto mb-8">
  <div class="px-4 py-3 border-b">
    <h2 class="font-semibold">Aptos seleccionados ({{ $aptos->count() }})</h2>
  </div>
  <table class="min-w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-3 py-2 text-left">Cédula</th>
        <th class="px-3 py-2 text-left">Nombres</th>
        <th class="px-3 py-2 text-left">Grado</th>
        <th class="px-3 py-2 text-left">Provincia Trabaja</th>
        <th class="px-3 py-2 text-left">Estado Civil</th>
        <th class="px-3 py-2 text-left">Promoción</th>
      </tr>
    </thead>
    <tbody>
    @forelse($aptos as $u)
      <tr class="border-b">
        <td class="px-3 py-2">{{ $u->cedula }}</td>
        <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
        <td class="px-3 py-2">{{ $u->grado }}</td>
        <td class="px-3 py-2">{{ $u->provincia_trabaja }}</td>
        <td class="px-3 py-2">{{ $u->estado_civil }}</td>
        <td class="px-3 py-2">{{ $u->promocion }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">No hay seleccionados.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

{{-- NO APTOS (no seleccionados) --}}
<div class="bg-white rounded shadow overflow-x-auto">
  <div class="px-4 py-3 border-b">
    <h2 class="font-semibold">No aptos (no seleccionados) ({{ $no_aptos->count() }})</h2>
  </div>
  <table class="min-w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-3 py-2 text-left">Cédula</th>
        <th class="px-3 py-2 text-left">Nombres</th>
        <th class="px-3 py-2 text-left">Grado</th>
        <th class="px-3 py-2 text-left">Provincia Trabaja</th>
        <th class="px-3 py-2 text-left">Estado Civil</th>
        <th class="px-3 py-2 text-left">Promoción</th>
      </tr>
    </thead>
    <tbody>
    @forelse($no_aptos as $u)
      <tr class="border-b">
        <td class="px-3 py-2">{{ $u->cedula }}</td>
        <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
        <td class="px-3 py-2">{{ $u->grado }}</td>
        <td class="px-3 py-2">{{ $u->provincia_trabaja }}</td>
        <td class="px-3 py-2">{{ $u->estado_civil }}</td>
        <td class="px-3 py-2">{{ $u->promocion }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Todos fueron seleccionados.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
