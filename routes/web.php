<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/token/exchange', [App\Http\Controllers\ExternalAuthController::class, 'exchange'])
    ->middleware('throttle:20,1')
    ->name('external-auth.exchange');
Route::post('/auth/token/logout', [App\Http\Controllers\ExternalAuthController::class, 'destroy'])
    ->name('external-auth.logout');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('prueba');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('totem',[App\Http\Controllers\prueba_controlador::class,'totem'])->name('totem');
// Route::get('/', [App\Http\Controllers\prueba_controlador::class, 'index'])->name('prueba');
Route::get('/repuestos', [App\Http\Controllers\prueba_controlador::class, 'repuestos'])->name('repuestos');
Route::get('/repuesto/{id}', [App\Http\Controllers\prueba_controlador::class, 'repuesto'])->name('repuesto');
Route::get('/contacto', [App\Http\Controllers\prueba_controlador::class, 'contacto'])->name('contacto');
Route::get('/busqueda_por_modelo/{modelo}/{familia}', [App\Http\Controllers\prueba_controlador::class, 'busqueda_por_modelo'])->name('busqueda_por_modelo');
Route::get('/busqueda_por_oem/{oem}', [App\Http\Controllers\prueba_controlador::class, 'busqueda_por_oem']);
Route::get('/marcas-repuestos', [App\Http\Controllers\prueba_controlador::class, 'damemarcas'])->name('marcas');
Route::get('/carrito',[App\Http\Controllers\prueba_controlador::class, 'carrito'])->middleware('external.auth');
Route::post('/agregar_carrito',[App\Http\Controllers\prueba_controlador::class, 'agregar_carrito'])->middleware('external.auth');
Route::get('/revisar_carrito',[App\Http\Controllers\prueba_controlador::class, 'revisar_carrito'])->middleware('external.auth');
Route::post('/eliminar_item_carrito',[App\Http\Controllers\prueba_controlador::class, 'eliminar_item_carrito'])->middleware('external.auth');
Route::get('/modelos_marca/{id}',[App\Http\Controllers\prueba_controlador::class, 'modelos_marca']);
Route::get('/repuestos_modelo/{id}',[App\Http\Controllers\prueba_controlador::class, 'repuestos_modelo']);
Route::get('/quienes-somos',[App\Http\Controllers\prueba_controlador::class, 'quienes_somos']);
Route::get('/aprende-con-nosotros',[App\Http\Controllers\prueba_controlador::class, 'aprende_con_nosotros']);
Route::get('/listar-medidas',[App\Http\Controllers\prueba_controlador::class, 'listar_medidas']);
Route::get('{tag}/buscador',[App\Http\Controllers\prueba_controlador::class, 'buscador']);
Route::get('/solicitudes',[App\Http\Controllers\prueba_controlador::class, 'solicitudes'])->middleware('external.auth');
Route::get('/mis_datos',[App\Http\Controllers\prueba_controlador::class, 'mis_datos'])->middleware('external.auth');
Route::post('/retiro_tienda_guardar',[App\Http\Controllers\prueba_controlador::class, 'retiro_tienda_guardar'])->middleware('external.auth');
Route::post('/despacho_domicilio_guardar',[App\Http\Controllers\prueba_controlador::class, 'despacho_domicilio_guardar'])->middleware('external.auth');

Route::post('/iniciar_pago',[App\Http\Controllers\getnet_controlador::class, 'index'])->middleware('external.auth');
Route::post('/recibir_pago',[App\Http\Controllers\getnet_controlador::class, 'recibir_pago']);
Route::post('/notificacion',[App\Http\Controllers\getnet_controlador::class, 'notificacion']);
//Route::get('/confirmar_pago',[App\Http\Controllers\transbank_controlador::class, 'confirmar_pago'])->name('confirmar_pago');
Route::get('/confirmacion_pago',[App\Http\Controllers\getnet_controlador::class, 'confirmacion_pago'])->name('confirmacion_pago');
Route::get('/confirmacion_rechazo',[App\Http\Controllers\getnet_controlador::class, 'confirmacion_rechazo'])->name('confirmacion_rechazo');

Route::get('/busqueda_por_medida/{medida}', [App\Http\Controllers\prueba_controlador::class, 'busqueda_por_medida']);
Route::get('/password/reset_/{token}', [App\Http\Controllers\prueba_controlador::class, 'password_reset'])->name('password.reset_');
Route::get('/confirm-register/{token}', [App\Http\Controllers\prueba_controlador::class, 'confirm_register'])->name('confirm.register');
