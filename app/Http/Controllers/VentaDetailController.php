<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ProductoModel;
use App\Models\VentaModel;
use App\Models\VentaDetailModel;
use Illuminate\Support\Facades\DB;

class VentaDetailController extends Controller
{

  /*------------------------------------WEB-----------------------------------*/
  public function detailventabyid($id){
    try {
      $exist = VentaModel::whereNull('deleted_at')->count();
      if($exist > 0){
        $dventa = VentaDetailModel::leftjoin('venta','d_venta.id_venta','=','venta.id')
                                 ->leftjoin('producto','d_venta.id_producto','=','producto.id')
                                 ->leftjoin('typepay','venta.id_pago','=','typepay.id')
                                 ->leftjoin('cliente','venta.id_cliente','=','cliente.id')
                                 ->leftjoin('users','venta.id_users','=','users.id')
                                 ->select('venta.id','venta.factura','producto.id as productoid','producto.nombre as producto','d_venta.cantidad','d_venta.precio','venta.precio_total','typepay.name as tipopago',DB::raw('CONCAT(cliente.nombre, " ", cliente.apellidos) AS cliente'),'users.name as usuario')
                                 ->whereNull('d_venta.deleted_at')
                                 ->where('id_venta','=',$id)
                                 ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $dventa;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = $e->getMessage(); //'Error al recuperar la informacion del detalle de venta';
    }
    return $result;
  }


  /*------------------------------------API-----------------------------------*/
  public function detailventabyidApi(Request $request){
    try {
      $exist = VentaModel::whereNull('deleted_at')->where('id','=',$request->id)->count();
      if($exist > 0){
        $dventa = VentaDetailModel::leftjoin('venta','d_venta.id_venta','=','venta.id')
                                 ->leftjoin('producto','d_venta.id_producto','=','producto.id')
                                 ->leftjoin('typepay','venta.id_pago','=','typepay.id')
                                 ->leftjoin('cliente','venta.id_cliente','=','cliente.id')
                                 ->leftjoin('users','venta.id_users','=','users.id')
                                 ->select('venta.id','venta.factura','producto.id as productoid','producto.nombre as producto','d_venta.cantidad','d_venta.precio','venta.precio_total','typepay.name as tipopago',DB::raw('CONCAT(cliente.nombre, " ", cliente.apellidos) AS cliente'),'users.name as usuario')
                                 ->whereNull('d_venta.deleted_at')
                                 ->where('id_venta','=',$request->id)
                                 ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $dventa;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = $e->getMessage(); //'Error al recuperar la informacion del detalle de venta';
    }
    return $result;
  }
}
