{{-- resources/views/layouts/guest.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNATH – Acceso</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 antialiased">
{{-- Aquí NO hay navbar ni sidebar --}}
@yield('content')
@stack('scripts')
</body>
</html>
