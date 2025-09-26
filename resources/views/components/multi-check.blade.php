@props([
    'id',
    'name',                 {{-- ej: "nomenclatura[]" --}}
    'placeholder' => 'Selecciona…',
    'searchPlaceholder' => 'Buscar…',
    'options' => [],
    'selected' => [],
])

@php
    $opts = collect($options)->map(fn($v) => (string)$v)->values();
    $sel  = collect($selected)->map(fn($v) => (string)$v)->values()->all();
@endphp

<div class="relative" data-multicheck id="{{ $id }}Root">
    {{-- Botón que abre el dropdown --}}
    <button type="button" id="{{ $id }}Btn" data-dropdown-toggle="{{ $id }}Drop"
            class="w-full inline-flex items-center justify-between px-3 py-1.5 text-xs font-medium rounded-lg
                   text-gray-900 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300
                   dark:text-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-800">
        <span class="truncate">{{ $placeholder }}</span>
        <svg class="w-4 h-4 ml-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M5.23 7.21a.75.75 0 011.06.02L10 11.18l3.71-3.95a.75.75 0 111.08 1.04l-4.25 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"/>
        </svg>
    </button>

    {{-- Panel del dropdown (ancho grande como en tu captura) --}}
    <div id="{{ $id }}Drop" class="z-20 hidden w-[520px] p-3 bg-white rounded-lg shadow dark:bg-gray-800">
        {{-- Buscador --}}
        <div class="mb-2">
            <label for="{{ $id }}Search" class="sr-only">Buscar</label>
            <input id="{{ $id }}Search" data-search type="text" placeholder="{{ $searchPlaceholder }}"
                   class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2
                          bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500
                          dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        {{-- Lista con checkboxes --}}
        <ul class="space-y-1 max-h-64 overflow-y-auto" data-list>
            @foreach($opts as $opt)
                @php $checked = in_array($opt, $sel, true); @endphp
                <li>
                    <label class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                        <input type="checkbox"
                               name="{{ $name }}"      {{-- ← envía el valor en el submit --}}
                               value="{{ $opt }}"
                               data-text="{{ $opt }}"
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                            @checked($checked)>
                        <span class="ml-2 text-sm truncate">{{ $opt }}</span>
                    </label>
                </li>
            @endforeach
        </ul>

        {{-- Barra de acciones como en la imagen: seleccionar todo / filtrados / limpiar --}}
        <div class="flex items-center gap-2 mt-3">
            <button type="button" data-select-all
                    class="text-xs px-3 py-1.5 rounded-full bg-blue-700 text-white hover:bg-blue-800
                           focus:outline-none focus:ring-4 focus:ring-blue-300
                           dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Seleccionar todo
            </button>

            <button type="button" data-select-visible
                    class="text-xs px-3 py-1.5 rounded-full bg-emerald-700 text-white hover:bg-emerald-800
                           focus:outline-none focus:ring-4 focus:ring-emerald-300
                           dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800">
                Seleccionar filtrados
            </button>

            <button type="button" data-clear
                    class="text-xs px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200
                           focus:outline-none focus:ring-4 focus:ring-gray-300
                           dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                Limpiar
            </button>

            <button type="button" data-apply
                    class="ml-auto text-xs px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-black/80
                           dark:bg-gray-600 dark:hover:bg-gray-500">
                Cerrar
            </button>
        </div>
    </div>

    {{-- Chips con selección actual (opcional) --}}
    <div class="mt-1 min-h-[1.25rem]" data-badges>
        @if(count($sel))
            @foreach(collect($sel)->take(3) as $tag)
                <span class="mr-1 mb-1 inline-block text-[10px] px-2 py-0.5 rounded bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100">{{ $tag }}</span>
            @endforeach
            @if(count($sel) > 3)
                <span class="text-[10px] text-gray-500">+{{ count($sel) - 3 }}</span>
            @endif
        @else
            <span class="text-[10px] text-gray-400">Sin selección</span>
        @endif
    </div>
</div>
