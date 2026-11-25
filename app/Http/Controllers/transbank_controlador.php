<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;
use App\Models\compra_transbank;
use App\Models\carrito_compras;
use Illuminate\Support\Facades\Auth;

class transbank_controlador extends Controller
{
    public function __construct(){
        if( app()->environment('production')){
            WebpayPLus::configureForProduction(
                env('webpay_plus_cc'),
                env('webpay_plus_api_key')
            );
        } else {
            WebpayPlus::configureForIntegration(
                env('webpay_plus_cc_i'),
                env('webpay_plus_api_key_i')
            );
        }
    }

    public function index(Request $request){
        
        $nueva_compra = new compra_transbank;
        $nueva_compra->session_id = rand(1,1000);
        $nueva_compra->total = $request->total;
        $nueva_compra->status = 1;
        $nueva_compra->usuario_id = 20;
        $nueva_compra->numero_carrito = $request->numero_carrito;
        $nueva_compra->token_ws = '---';
        $nueva_compra->save();


        $url_to_pay = self::start_web_pay_plus_transaction($nueva_compra);
        return $url_to_pay;
    }

    public function start_web_pay_plus_transaction($nueva_compra){
    
        try {
            $transaccion = (new Transaction)->create(
                $nueva_compra->id,
                $nueva_compra->session_id,
                $nueva_compra->total,
                route('confirmar_pago')
            );
            $url = $transaccion->getUrl().'?token_ws='.$transaccion->getToken();
            return $url;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function confirmar_pago(Request $req){
        try {
            if($req->get('TBK_TOKEN')){

                $buyOrder = $req->get('TBK_ORDEN_COMPRA');
                $compra_transbank = compra_transbank::where('id',$buyOrder)->first();
                $amount = $compra_transbank->total; 
                
                //$response = (new Transaction)->refund($req->get('TBK_TOKEN'), (INT)$amount);
                
                $compra_transbank->status = 1;
                $compra_transbank->token_ws = $req->get('TBK_TOKEN');
                $compra_transbank->update();
                return redirect('confirmacion_rechazo'."?compra_id={$compra_transbank->id}");
                
            }else{
                $confirmacion = (new Transaction)->commit($req->get('token_ws'));
            
                $compra = compra_transbank::where('id',$confirmacion->buyOrder)->first();
                if($confirmacion->isApproved()){
                    $compra->status = 2; // Aprobada
                    $compra->token_ws = $req->get('token_ws');
                    $compra->update();
    
                    return redirect('confirmacion_pago'."?compra_id={$compra->numero_carrito}");
                }else{
                    //fallida o rechazada
                    $compra->status = 1;
                    $compra->token_ws = $req->get('token_ws');
                    $compra->update();
                    return redirect('confirmacion_rechazo'."?compra_id={$compra->numero_carrito}");
                }
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    
}
