@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Resultado del Cambio Aleatorio</h2>

    <div class="mb-6">
        <a href="{{ $download_link }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Descargar Excel
        </a>
    </div>

    <h3 class="text-lg font-semibold mb-2">Usuarios del Documento: Cambio DE (actualizados)</h3>
    <div class="overflow-auto mb-6">
        <table class="min-w-full text-xs border">
            <thead class="bg-gray-100">
                <tr>
                    @foreach ($headers as $header)
                        <th class="border px-2 py-1">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($dataA as $row)
                    <tr>
                        @foreach ($headers as $key => $header)
                            <td class="border px-2 py-1">{{ $row[$key] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-semibold mb-2">Usuarios del Documento: Cambio A (actualizados)</h3>
    <div class="overflow-auto">
        <table class="min-w-full text-xs border">
            <thead class="bg-gray-100">
                <tr>
                    @foreach ($headers as $header)
                        <th class="border px-2 py-1">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($dataB as $row)
                    <tr>
                        @foreach ($headers as $key => $header)
                            <td class="border px-2 py-1">{{ $row[$key] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
