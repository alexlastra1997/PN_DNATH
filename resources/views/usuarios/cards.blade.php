<!-- component -->
    <div class="flex flex-col">
        <h2 class="mb-4 text-2xl font-bold">Servidores Policiales - DNATH </h2>
    
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div class="flex items-start rounded-xl bg-white p-4 shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-blue-100 bg-blue-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
    
            <div class="ml-4">
            <h2 class="font-semibold">{{ number_format($cantidadCedulasUnicas, 0, ',', '.') }} Servidores Policiales</h2>
            </div>
        </div>
    
        <div class="flex items-start rounded-xl bg-white p-4 shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-orange-100 bg-orange-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            </div>
    
            <div class="ml-4">
            <h2 class="font-semibold">{{$cuadroSuperiores }} Oficiales Superiores</h2>
            </div>
        </div>
        <div class="flex items-start rounded-xl bg-white p-4 shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-red-100 bg-red-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
    
            <div class="ml-4">
            <h2 class="font-semibold">{{$cuadroSubalternos }} Oficiales Subalternos</h2>
            </div>
        </div>
        <div class="flex items-start rounded-xl bg-white p-4 shadow-lg">
            <div class="flex h-12 w-12 items-center justify-center rounded-full border border-indigo-100 bg-indigo-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
    
            <div class="ml-4">
            <h2 class="font-semibold">{{$cuadroClases }} Clases Y Policías</h2>
            </div>
        </div>
        </div>
    </div>


  