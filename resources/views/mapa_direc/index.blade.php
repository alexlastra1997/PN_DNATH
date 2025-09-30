@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            <div class="mb-6">
                <h1 class="text-xl font-semibold text-gray-900 ">
                    Mapa Direc — Servicios (agrupado por prefijo)
                </h1>
                <p class="text-xs  dark:text-gray-400">
                    Se agrupa por la primera palabra del servicio (antes del primer <code>-</code>).
                    Solo cargos: DIRECTOR, SUBDIRECTOR, COMANDANTE, SUBCOMANDANTE, JEFE, SUBJEFE.
                </p>
            </div>

            @php
                $items  = collect($datos ?? []);
                $grupos = $items->groupBy('servicio_raiz');
            @endphp

            @if($grupos->isEmpty())
                <div class="rounded-2xl border border-slate-700 p-8 text-center text-slate-300">
                    No hay datos para mostrar.
                </div>
            @else
                {{-- 3 cards en la grilla (responsivo: 1 / 2 / 3) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4  gap-6">
                    @foreach($grupos as $raiz => $rows)
                        <article class="rounded-2xl overflow-hidden border border-slate-700 bg-slate-900 shadow bg-gray-900">
                            {{-- Header oscuro como en tu ejemplo --}}
                            <div class="px-4 py-3 bg-slate-800 border-b border-slate-700">
                                <h2 class="text-sm font-semibold text-100 tracking-wide uppercase text-white">
                                    {{ $raiz ?: 'SIN PREFIJO' }}
                                </h2>
                            </div>

                            {{-- Body oscuro con el botón verde tipo “pill” --}}
                            <div class="px-4 py-5 bg-slate-900">
                                <a
                                    href="{{ route('mapa_direc.raiz', ['raiz' => $raiz]) }}"
                                    class="block w-full text-center rounded-lg px-4 py-2 font-semibold uppercase tracking-wide
                       text-white bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700
                       transition-colors shadow-sm"
                                >
                                    Ver detalle
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
@endsection
