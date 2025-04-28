@extends('layouts.app')

@section('content')

<section class="bg-white ">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl ">SISTEMA INTEGRAL DE LA DNATH</h1>
            
        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            <img src="{{ asset('images/DNATH2.jpeg') }}"alt="Placeholder Image" class="object-fill w-full h-full">
        </div>                
    </div>
</section>
    
    <div class="py-16 ">  
        <div class="container m-auto px-6 text-gray-500 md:px-12 xl:px-0">

            <div class="mx-auto grid gap-6 md:w-3/4 lg:w-full lg:grid-cols-2 items-stretch">
                
                <div class="  rounded-2xl shadow-xl px-8 py-12 sm:px-12 lg:px-8 grid  gap-8 grid-cols-1 bg-white ">
                    
                    <div class="flex flex-col  ">
                        <div class="  rounded-3xl p-4">
                            <div class="flex-none lg:flex">
                                <div class=" h-full w-full lg:h-48 lg:w-48   lg:mb-0 mb-3">
                                    <img src="{{ asset('images/policia.jpg') }}"alt="Placeholder Image" class="object-fill w-full h-full">
                                </div>
                                <div class="flex items-start rounded-xl bg-white p-4 ">
                                    <div class="ml-4">
                                    <h2 class="font-semibold text-6xl">{{$cantidadCedulasUnicas }} <br> <a href="" class="text-3xl">Servidores Policiales</a></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl px-8 py-4 sm:px-12 lg:px-8">
                    @include('usuarios.cards')
                </div>
                
                 
                
            </div>

            <div class="bg-white rounded-2xl shadow-xl px-8 py-12 sm:px-12 lg:px-8 ">
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-4">Servidores policiales por provincia</h3>
                    <canvas id="barChartProvincias" height="100"></canvas>
                </div>
                
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById('barChartProvincias').getContext('2d');
                    const barChartProvincias = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($provinciasData->keys()) !!},
                            datasets: [{
                                label: 'Cantidad de usuarios',
                                data: {!! json_encode($provinciasData->values()) !!},
                                backgroundColor: '#4F46E5',
                                borderColor: '#4338CA',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
                
            </div>
        </div>
    </div>

@endsection