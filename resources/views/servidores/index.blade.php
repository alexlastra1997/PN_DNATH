<!-- resources/views/servidores/index.blade.php -->

@extends('layouts.app')
@section('import')
   
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'custom-blue': '#1DA1F2',
                    'custom-gray': '#F5F8FA',
                }
            }
        }
    }
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script>    

    <div class="container mx-auto p-4">

    

        <!-- Formulario de filtro -->
        

        
        <!-- Tabla de servidores -->
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border p-2">Cédula</th>
                    <th class="border p-2">Nombre</th>
                    <th class="border p-2">Hijos (18 años)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($servidores as $servidor)
                    <tr>
                        <td class="border p-2">{{ $servidor->cedula }}</td>
                        <td class="border p-2">{{ $servidor->apellidos_nombres }}</td>
                        <td class="border p-2">{{ $servidor->hijos18 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="border p-2 text-center">No se encontraron registros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection 


