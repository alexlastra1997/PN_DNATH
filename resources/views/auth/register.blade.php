<!-- component -->
@vite('resources/css/app.css')

<div class="bg-gray-100 flex justify-center items-center h-screen">
        <!-- Left: Image -->
    <div class="w-1/2 h-screen hidden lg:block">
    <img src="{{ asset('images/policia.jpg') }}" alt="Placeholder Image" class="object-cover w-full h-full">
    </div>
    <!-- Right: Login Form -->
    <div class="= w-full lg:w-1/2">
        <section class="bg-gray-50 dark:bg-gray-900">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                    <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                    Flowbite    
                </a>
                <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                            Registra tu cuenta
                        </h1>
                        @if ($errors->any())
                        <div style="color: red;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                
                        <div>
                            <label for="name">Nombre:</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                
                        <div>
                            <label for="email">Correo electrónico:</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                
                        <div>
                            <label for="password">Contraseña:</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                
                        <div>
                            <label for="password_confirmation">Confirmar contraseña:</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required>
                        </div>
                
                        <button type="submit">Registrar</button>
                    </form>
                    </div>
                </div>
            </div>
          </section>
    </div>
</div>