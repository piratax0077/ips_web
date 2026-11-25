<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\carrito_compras;
use App\Models\carrito_compras_detalle;
use App\Models\compra_transbank;
use App\Models\correlativo;
use App\Models\retiro_tienda;
use App\Models\despacho_domicilio;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // para tratamiento de fechas

class prueba_controlador extends Controller
{
    public function index()
    {
        return view('prueba');
    }

    public function totem(){
        return view('totem');
    }

    public function repuestos(){
        return view('repuestos');
    }

    public function contacto(){
        return view('contacto');
    }

    public function repuesto($id){
        return view('repuesto',['repuesto_id' => $id]);
    }

    public function busqueda_por_modelo($modelo,$familia){
        return view('busqueda_por_modelo',['idmodelo' => $modelo,'familia' => $familia]);
    }

    public function busqueda_por_oem($oem){
        return view('busqueda_por_oem',['oem' => $oem]);
    }

    public function busqueda_por_medida($medida){
        return view('busqueda_por_medida',['medida' => $medida]);
    }

    public function damemarcas(){
        return view('marcas');
    }

    public function agregar_carrito(Request $req){
     
        try {
            $usuario = Auth::user();
            $correlativo = correlativo::find(1);
           
            $existe = carrito_compras::where('usuario_id',$usuario->id)->where('activo',1)->first();
            if(!$existe){
                $carrito_compras = new carrito_compras;
                $carrito_compras->fecha_emision = Carbon::today()->toDateString();
                $carrito_compras->numero_carrito = intval($correlativo->correlativo) + 1;
                $carrito_compras->activo = 1;
                $carrito_compras->usuario_id = $usuario->id;
                $carrito_compras->save();
                $correlativo->correlativo = $carrito_compras->numero_carrito;
                $correlativo->save();
                
                $num_carrito = intval($correlativo->correlativo);
                $existe_repuesto = $this->existe($num_carrito, $req->id_repuesto);
            }else{
            	$num_carrito = $existe->numero_carrito;
                $existe_repuesto = $this->existe($num_carrito, $req->id_repuesto);
            }
            
                

                if(!$existe_repuesto){
                    $carrito_existente = $this->damecarritoexistente($usuario->id, $num_carrito);
                
                    $detalle = new carrito_compras_detalle;
    
                    $detalle->repuesto_id = $req->id_repuesto;
                    $detalle->cantidad = $req->cantidad;
                    $detalle->carrito_numero = $carrito_existente->numero_carrito;
                    $detalle->save();
                    
                    return 'OK';
                }else{
                    return 'existe';
                }
            
                
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function damecarritoexistente($usuario_id, $numero_carrito){
        $carro = carrito_compras::where('usuario_id',$usuario_id)->where('activo',1)->where('numero_carrito',$numero_carrito)->first();
        return $carro;
    }



    public function carrito(){
        $items = $this->dame_carrito_actual();
        return view('carrito',['items' => $items]);
    }

    public function revisar_carrito(){
        try {
            if(Auth::user()){
                $carrito = $this->dame_carrito_actual();
                if($carrito){
                    return $carrito->count();
                }else{
                    return 0;
                }
                
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
        
    }

    private function dame_carrito_actual(){
        try {
            $carrito = carrito_compras::where('usuario_id',Auth::user()->id)->where('activo',1)->first();
            if($carrito){
                $items = carrito_compras_detalle::where('carrito_numero',$carrito->numero_carrito)->get();
                return $items;
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function existe($numero_carrito, $repuesto_id){
        $carrito = carrito_compras_detalle::where('carrito_numero',$numero_carrito)->where('repuesto_id',$repuesto_id)->first();
        return $carrito;
    }

    public function eliminar_item_carrito(Request $req){
       
        try {
            $item = carrito_compras_detalle::where('repuesto_id',$req->idrep)->where('carrito_numero',$req->numero_carrito)->first();
            
            $item->delete();
           
            $items = $this->dame_carrito_actual();
            return $items->count();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function modelos_marca($idmarca){
        return view('modelos_marca',['idmarca' => $idmarca]);
    }

    public function repuestos_modelo($idmodelo){
        return view('repuestos_modelo',['idmodelo' => $idmodelo]);
    }

    public function quienes_somos(){
        return view('quienes-somos');
    }

    public function aprende_con_nosotros(){
        return view('aprende-con-nosotros');
    }

    public function buscador($tag){
        return view('buscador');
    }

    public function solicitudes(){
        try {
            return view('solicitudes');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function retiro_tienda_guardar(Request $req){
        try {
            $retiro_tienda = new retiro_tienda;
            $retiro_tienda->numero_carrito = $req->numero_carrito;
            $retiro_tienda->nombre = $req->nombre;

            $retiro_tienda->save();

            $carrito = carrito_compras::where('numero_carrito',$req->numero_carrito)->first();

            $carrito->activo = 0;

            $carrito->save();
            return 'OK';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function despacho_domicilio_guardar(Request $req){
       
        try {
            $region = $req->region;
            $comuna = $req->comuna;
            $numero_carrito = $req->numero_carrito;
            $direccion_despacho = $req->direccion_despacho;
            $telefono = $req->telefono_despacho;
            $persona = $req->persona_quien_retira;

            $despacho_domicilio = new despacho_domicilio;
            $despacho_domicilio->region = $region;
            $despacho_domicilio->comuna = $comuna;
            $despacho_domicilio->numero_carrito = $numero_carrito;
            $despacho_domicilio->direccion_despacho = $direccion_despacho;
            $despacho_domicilio->telefono_despacho = $telefono;
            $despacho_domicilio->persona = $persona;

            $despacho_domicilio->save();

            $carrito = carrito_compras::where('numero_carrito',$req->numero_carrito)->first();

            $carrito->activo = 0;

            $carrito->save();

            return 'OK';

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function password_reset($token){
        
        try {
            return view('auth.passwords.reset_',['token' => $token]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function confirm_password($token)
    {
        // $user = User::where('confirmation_token', $token)->firstOrFail();
        // $user->confirmation_token = null;
        // $user->email_verified_at = now();
        // $user->save();

        // Auth::login($user); // Inicia sesión automáticamente después de confirmar el correo electrónico

        // return redirect('/home');
    }

    public function mis_datos(){
        return view('mis_datos');
    }

    public function listar_medidas(){
        return view('listar_medidas');
    }

}
