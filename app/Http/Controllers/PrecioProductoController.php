<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrecioProductoModel;
use Illuminate\Support\Facades\Log;
use Response;


class PrecioProductoController extends Controller
{
    public function getpreciobyid($id){
      try {
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = PrecioProductoModel::where('id_product','=',$id)->first();

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar con el precio seleccionado';
      }
      return Response::json($result);
    }
    public function getprodutprecio(Request $request){
      try {
        $id = isset($request->id) ? $request->id : 0;
        $exist = PrecioProductoModel::whereNull('deleted_at')->where('id_product','=',$id)->count();
        if($exist > 0){
          $precioproduct = PrecioProductoModel::whereNull('deleted_at')
                                              ->where('id_product','=',$id)
                                              ->select('id','precio','id_product')
                                              ->first();
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $precioproduct;
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar el precio del producto seleccionado';
      }
      return Response::json($result);
    }
}
