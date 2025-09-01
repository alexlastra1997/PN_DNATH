
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNATH</title>

    {{-- Carga Tailwind + tu JS (incluye Flowbite desde app.js) --}}
    @vite(['resources/css/app.css','resources/js/app.js'])

    {{-- Meta opcionales --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body class="bg-gray-100 overflow-x-hidden antialiased">

{{-- Navbar y Sidebar solo en app --}}
@include('layouts.nav')
@include('layouts.sidebar')

<main class="p-4 md:ml-64 h-auto pt-20">
    <div class="container mx-auto p-4">
        @yield('content')
        @yield('importar')
    </div>
</main>

{{-- Stack para scripts específicos de cada vista --}}
@stack('scripts')
</body>
</html>

<script>
    const modal = document.getElementById('filtroModal');
    const backdrop = document.getElementById('modal-backdrop');

    // Escuchar eventos de apertura y cierre del modal
    modal.addEventListener('show.tw.modal', () => {
        backdrop.classList.remove('hidden');
    });

    modal.addEventListener('hide.tw.modal', () => {
        backdrop.classList.add('hidden');
    });
</script>
