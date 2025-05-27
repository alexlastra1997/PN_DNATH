<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\ServidorPolicialController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProvinciaController;

// Login
// Muestra el formulario de login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Procesa el formulario de login
Route::post('/', [LoginController::class, 'login']);

// Cierra la sesión del usuario
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Registro
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Rutas protegidas con auth
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');

    Route::post('/usuarios/cart/add/{id}', [UsuarioController::class, 'agregarACarrito'])->name('usuarios.cart.add');
    Route::get('/usuarios/cart', [UsuarioController::class, 'verCarrito'])->name('usuarios.cart');

    Route::post('/usuarios/cart/remove/{id}', [UsuarioController::class, 'eliminarDelCarrito'])->name('usuarios.cart.remove');
    Route::post('/usuarios/cart/clear', [UsuarioController::class, 'vaciarCarrito'])->name('usuarios.cart.clear');
    Route::get('/usuarios/export/{type}', [UsuarioController::class, 'export'])->name('usuarios.export');

    
    Route::get('/importar', [ImportExcelController::class, 'showForm']);
    Route::post('/importar', [ImportExcelController::class, 'importar'])->name('importar.excel');
    Route::delete('/importar-excel/eliminar-todos', [ImportExcelController::class, 'eliminarTodos'])->name('importar.excel.eliminar.todos');

    Route::get('/importar-cargos', [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/importar-cargos', [CargoController::class, 'importar'])->name('cargos.importar');
    Route::delete('/cargos/eliminar-todo', [CargoController::class, 'eliminarTodo'])->name('cargos.eliminarTodo');

    Route::get('/provincias', [ProvinciaController::class, 'index'])->name('provincias.index');

    Route::get('/provincias/{id}', [ProvinciaController::class, 'show'])->name('provincias.show');

    Route::get('/servidores', [ServidorPolicialController::class, 'index'])->name('servidores.index');
});

