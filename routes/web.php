<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\InvitadoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\OrdenDeServicioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\ArtistaController;
use App\Models\Equipment;
use App\Models\OrdenDeServicio;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard',[Dashboard::class,'home'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::resource('usuarios',UserController::class)->middleware('auth');
Route::resource('invitados', InvitadoController::class)->middleware('auth');
Route::resource('roles', RoleController::class)->middleware('auth');
Route::resource('clientes',ClienteController::class)->middleware('auth');
/** rutas soft v2.0 */

Route::resource('proyectos', ProyectoController::class)->middleware('auth');
Route::resource('actividades', ActividadController::class)->middleware('auth')->parameters(['actividades' => 'actividad']);
Route::resource('tareas', TareaController::class)->middleware('auth');


/* routes v3.0 Eventos */

Route::resource('artistas', ArtistaController::class);
Route::post('/artista/{id}', [ArtistaController::class, 'getArtistaData'])->name('artista.data');




/** soft v1.0  */
Route::resource('equipos',EquipoController::class)->middleware('auth');
Route::resource('ordenes-de-servicio',OrdenDeServicioController::class)->middleware('auth')->parameters(['ordenes-de-servicio' => 'orden',]);

/** ruta para Imagenes equipos */
Route::delete('equipos/{equipo}/delete-imagen-equipo/{id}',  [EquipoController::class, 'deleteImagen'] )->name('equipos.deleteimagen');

Route::get('/invitados/registrar/{invitadoId}', [InvitadoController::class, 'registrar'])->name('invitados.registrar');
Route::get('/invitados/validar-qr/{codigo}', [InvitadoController::class, 'validarQR'])->name('invitados.validar-qr');
Route::get('invitados/generar-pdf/{invitado}', [InvitadoController::class, 'generarPDF'])->name('invitados.generarPDF');





Route::get('/url', function () {
    return view('url');
});

require __DIR__.'/auth.php';
