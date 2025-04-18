<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\ServidorPolicialController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Login
// Muestra el formulario de login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Procesa el formulario de login
Route::post('/', [LoginController::class, 'login']);

// Cierra la sesión del usuario
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Registro
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas con auth
Route::middleware('auth')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
    Route::get('/usuarios/export-excel', [UsuarioController::class, 'exportExcel'])->name('usuarios.exportExcel');
    Route::get('/usuarios/export-pdf', [UsuarioController::class, 'exportPDF'])->name('usuarios.exportPDF');
    
    Route::get('/importar', [ImportExcelController::class, 'showForm']);
    Route::post('/importar', [ImportExcelController::class, 'importar'])->name('importar.excel');

    Route::get('/servidores', [ServidorPolicialController::class, 'index'])->name('servidores.index');
});

