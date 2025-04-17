<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNATH</title>
    <!-- Incluye tus estilos CSS aquí (como Tailwind) -->
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

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
