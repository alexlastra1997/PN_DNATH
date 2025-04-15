<?php

use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\ServidorPolicialController;
use App\Http\Controllers\UsuarioController;


Route::get('/importar', [ImportExcelController::class, 'showForm']);
Route::post('/importar', [ImportExcelController::class, 'importar'])->name('importar.excel');
Route::get('/servidores', [ServidorPolicialController::class, 'index'])->name('servidores.index');

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('/usuarios/export-excel', [UsuarioController::class, 'exportExcel'])->name('usuarios.exportExcel');
Route::get('/usuarios/export-pdf', [UsuarioController::class, 'exportPDF'])->name('usuarios.exportPDF');
