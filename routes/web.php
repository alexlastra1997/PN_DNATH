<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\ServidorPolicialController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BuscarVacanteController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NomenclaturaController;
use App\Http\Controllers\OrganicoEfectivoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\ReporteOrganicoController;
use App\Http\Controllers\TrasladoController;

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
    Route::get('/usuarios/seleccionados', [UsuarioController::class, 'seleccionados'])->name('usuarios.seleccionados');
    Route::get('/usuarios/factibilidad', [UsuarioController::class, 'factibilidad'])->name('usuarios.factibilidad');
    Route::get('/usuarios/factibilidad/pdf', [UsuarioController::class, 'exportarFactibilidadPdf'])->name('usuarios.factibilidad.pdf');

    Route::delete('/usuarios/seleccionados/{id}', [UsuarioController::class, 'eliminarSeleccionado'])->name('usuarios.eliminarSeleccionado');
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
    Route::get('/usuarios/export/{type}', [UsuarioController::class, 'export'])->name('usuarios.export');

    Route::get('/usuarios/agregar/{id}', [UsuarioController::class, 'agregar'])->name('usuarios.agregar');


    Route::get('/cargos/cards', [CargoController::class, 'cards'])->name('cargos.cards');
    Route::get('/cargos/cards/{sigla}', [CargoController::class, 'detallePorSigla'])->name('cargos.detalle');
    Route::get('/cargos/ocupado/{cargo}', [CargoController::class, 'ocupadoPorUsuarios'])->name('cargos.ocupado');
    Route::delete('/cargos/eliminar-todo', [CargoController::class, 'eliminarTodo'])->name('cargos.eliminarTodo');

    Route::get('/importar', [ImportExcelController::class, 'showForm']);
    Route::post('/importar', [ImportExcelController::class, 'importar'])->name('importar.excel');
    Route::delete('/importar-excel/eliminar-todos', [ImportExcelController::class, 'eliminarTodos'])->name('importar.excel.eliminar.todos');
    Route::get('/importar-cargos', [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/importar-cargos', [CargoController::class, 'importar'])->name('cargos.importar');
    Route::get('/provincias', [ProvinciaController::class, 'index'])->name('provincias.index');
    Route::get('/provincias/{id}', [ProvinciaController::class, 'show'])->name('provincias.show');
    Route::get('/servidores', [ServidorPolicialController::class, 'index'])->name('servidores.index');

    Route::get('/reporte-organico/importar', [ReporteOrganicoController::class, 'showForm'])->name('reporte.form');
    Route::post('/reporte-organico/importar', [ReporteOrganicoController::class, 'importar'])->name('reporte.importar');


    Route::get('/organico-efectivo', [OrganicoEfectivoController::class, 'index'])->name('organico.efectivo');
    Route::post('/organico-efectivo/agregar', [OrganicoEfectivoController::class, 'agregar'])->name('organico.efectivo.agregar');
    Route::get('/organico-efectivo/seleccionados', [OrganicoEfectivoController::class, 'mostrarSeleccionados'])->name('organico.efectivo.seleccionados');
    Route::post('/organico-efectivo/limpiar', [OrganicoEfectivoController::class, 'limpiarCarrito'])->name('organico.efectivo.limpiar');
    Route::get('/organico-efectivo/buscar-vacante', [OrganicoEfectivoController::class, 'buscarVacante'])->name('organico.efectivo.buscarVacante');
    Route::post('/organico-efectivo/evaluar', [OrganicoEfectivoController::class, 'evaluar'])->name('organico.efectivo.evaluar');

    Route::get('/nomenclatura/{niveles?}', [OrganicoEfectivoController::class, 'nomenclatura'])
    ->where('niveles', '.*')
    ->name('nomenclatura.index');

    Route::get('/nomenclatura/{niveles?}', [NomenclaturaController::class, 'nomenclatura'])
    ->where('niveles', '.*')
    ->name('nomenclatura.index');
    
    Route::get('/nomenclatura-excel/{niveles?}', [NomenclaturaController::class, 'exportarExcel'])
    ->where('niveles', '.*')
    ->name('nomenclatura.exportarExcel');

    Route::get('/nomenclatura-pdf/{niveles?}', [NomenclaturaController::class, 'exportarPDF'])
    ->where('niveles', '.*')
    ->name('nomenclatura.exportarPDF');


   
Route::get('/traslados', [TrasladoController::class, 'index'])->name('traslados.index');
Route::post('/traslados/procesar', [TrasladoController::class, 'procesar'])->name('traslados.procesar');

// Redirección si alguien accede a la ruta POST con GET
Route::get('/traslados/procesar', function () {
    return redirect()->route('traslados.index');
});
});

