{{-- resources/views/usuarios/partials/_resultado_contenido.blade.php --}}
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Usuarios del documento</h1>

    @php
        // Detecta si llegó la colección paginada (cuando hay cédulas válidas en sesión)
        $total = (isset($usuarios) && method_exists($usuarios, 'total')) ? $usuarios->total() : 0;
        $paginado = isset($usuarios) && method_exists($usuarios, 'links');
    @endphp

    @if(!$paginado)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded">
            <p>No hay cédulas cargadas aún. Ve a <a class="underline" href="{{ route('usuarios.opciones') }}">Usuarios → Opciones</a> y sube tu documento.</p>
        </div>
    @else
        <div class="mb-3 text-sm text-gray-600">Total encontrados: <strong>{{ $total }}</strong></div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Cédula</th>
                    <th class="px-3 py-2 text-left">Grado</th>
                    <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                    <th class="px-3 py-2 text-left">Provincia Trabaja</th>
                    <th class="px-3 py-2 text-left">Provincia Vive</th>
                </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $u)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $u->cedula }}</td>
                        <td class="px-3 py-2">{{ $u->grado }}</td>
                        <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                        <td class="px-3 py-2">{{ $u->provincia_trabaja }}</td>
                        <td class="px-3 py-2">{{ $u->provincia_vive }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No hay usuarios que coincidan con el documento cargado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usuarios->onEachSide(1)->links() }}
        </div>
    @endif
</div>
