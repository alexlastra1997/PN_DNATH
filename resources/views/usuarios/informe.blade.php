<?php
@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Informe de Calificaciones</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Generado: {{ $generado }}</p>
                </div>
                <div class="flex gap-2 print:hidden">
                    <a href="{{ route('usuarios.carrito') }}" class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm">
                        Volver al carrito
                    </a>
                    <button onclick="window.print()" class="px-3 py-2 rounded-md bg-primary-700 hover:bg-primary-800 text-sm text-white">
                        Imprimir
                    </button>
                </div>
            </div>

            {{-- ANEXO 1: APTOS --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden page">
                <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">ANEXO 1 — SERVIDORES APTOS</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ count($aptos) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-3 py-2 w-14">#</th>
                            <th class="px-3 py-2">CÉDULA</th>
                            <th class="px-3 py-2">APELLIDOS NOMBRES</th>
                            <th class="px-3 py-2">GRADO</th>
                            <th class="px-3 py-2">NOVEDAD</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($aptos as $i => $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2">{{ $i + 1 }}</td>
                                <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                <td class="px-3 py-2">{{ $row['grado'] }}</td>
                                <td class="px-3 py-2">
                                    @if(($row['novedad'] ?? 'SIN_NOVEDAD') === 'NOVEDAD')
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-yellow-100 text-yellow-800 mr-2">NOVEDAD</span>
                                        <span>{{ $row['detalle_novedad'] }}</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">SIN NOVEDAD</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-500">Sin registros de APTOS.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Salto de página al imprimir --}}
            <div class="break-after print:block"></div>

            {{-- ANEXO 2: NO APTOS --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden page">
                <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">ANEXO 2 — SERVIDORES NO APTOS</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ count($noAptos) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-3 py-2 w-14">#</th>
                            <th class="px-3 py-2">CÉDULA</th>
                            <th class="px-3 py-2">APELLIDOS NOMBRES</th>
                            <th class="px-3 py-2">GRADO</th>
                            <th class="px-3 py-2">NOVEDAD</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($noAptos as $i => $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2">{{ $i + 1 }}</td>
                                <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                <td class="px-3 py-2">{{ $row['grado'] }}</td>
                                <td class="px-3 py-2">
                                    @if(($row['novedad'] ?? 'SIN_NOVEDAD') === 'NOVEDAD')
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-yellow-100 text-yellow-800 mr-2">NOVEDAD</span>
                                        <span>{{ $row['detalle_novedad'] }}</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">SIN NOVEDAD</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-500">Sin registros de NO APTOS.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    {{-- Estilos de impresión --}}
    <style>
        @media print {
            .print\:hidden { display: none !important; }
            body { background: #fff !important; }
            .page { box-shadow: none !important; border: none !important; }
            .break-after { page-break-after: always; }
        }
    </style>
@endsection
