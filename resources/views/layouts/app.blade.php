<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DNATH</title>
        <!-- Incluye tus estilos CSS aquí (como Tailwind) -->
        @vite('resources/css/app.css')
        <!-- Tailwind CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
       <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    </head>

    <body class="bg-gray-100 overflow-x-hidden">

        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

        <div class="antialiased bg-gray-50 ">

            <main class="p-4 md:ml-64 h-auto pt-20">    
                    @include('layouts.nav')
                    <!-- Sidebar -->
                    @include('layouts.sidebar')
                <!-- Contenido principal -->
                <div class="container mx-auto p-4">
                    
                    @yield('content') <!-- Aquí se insertará el contenido de las vistas -->
                    @yield('importar') <!-- Aquí se insertarán los scripts de las vistas -->
                </div>
            </main>
        </div>
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
