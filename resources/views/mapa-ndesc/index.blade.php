@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 ">
            Mapa NDESC · Zonas y Subzonas
        </h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($zonas as $zonaKey => $subzonas)
                <div class="rounded-2xl shadow bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-extrabold tracking-wide text-gray-800 dark:text-gray-100">
                            ZONA {{ substr($zonaKey, 1) }}
                        </h2>
                    </div>

                    <div class="p-4 grid grid-cols-1 gap-2">
                        @forelse ($subzonas as $sz)
                            @php $slug = Str::slug(mb_strtolower($sz,'UTF-8'), '-'); @endphp
                            <a
                                href="{{ route('ndesc.subzona.show', ['zona' => substr($zonaKey,1), 'subzonaSlug' => $slug]) }}"
                                class="inline-flex items-center justify-center rounded-lg px-3 py-2
                                   bg-emerald-600 text-white text-sm font-medium
                                   hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                                title="Ver personal SZ {{ $sz }}"
                            >
                                SZ {{ $sz }}
                            </a>
                        @empty
                            <span class="text-sm text-gray-500 dark:text-gray-400">Sin subzonas detectadas.</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
