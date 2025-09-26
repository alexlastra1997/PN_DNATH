{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')

@section('content')
    <section class="bg-white dark:bg-gray-900">
        <div class="grid lg:h-screen lg:grid-cols-2">

            {{-- Columna izquierda: formulario --}}
            <div class="flex justify-center items-center py-6 px-4 lg:py-0 sm:px-0">
                <form method="POST" action="{{ route('register') }}"
                      class="space-y-4 max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    @csrf

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Crea tu cuenta</h2>

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="text-red-600 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Nombre --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nombre completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                        dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                        dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Rol</label>
                        <select id="role" name="role" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                         dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Selecciona un rol</option>
                            @foreach (($roles ?? []) as $r)
                                <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
                                    {{ strtoupper($r) }}
                                </option>
                            @endforeach
                        </select>
                        @if (empty($roles))
                            <p class="mt-1 text-xs text-amber-600">No existen roles en la base de datos. Corre el seeder primero.</p>
                        @endif
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Contraseña</label>
                        <input type="password" name="password" id="password" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- Confirmación --}}
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg border
                       bg-blue-600 text-white hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                        Registrarme
                    </button>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Al registrarte aceptas nuestros Términos y Política de privacidad.
                    </p>
                </form>
            </div>

            {{-- Columna derecha decorativa --}}
            <div class="hidden lg:block bg-gray-50 dark:bg-gray-900"></div>
        </div>
    </section>
@endsection
