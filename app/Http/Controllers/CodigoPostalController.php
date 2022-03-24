<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoPostalModel;
use Illuminate\Support\Facades\Log;
use Response;
use DB;

class CodigoPostalController extends Controller
{
    public function getcodigopostal(Request $request){
      try {
        $codigoscp = CodigoPostalModel::where('d_codigo','=',$request->CP)->get();  //204

        if(count($codigoscp) > 0){
          $datoscp = [];

          foreach ($codigoscp as $index => $oodigoscp) {
            $datos = array(
              "codigo postal" => $codigoscp[$index]->d_codigo,
              "colonia" => $codigoscp[$index]->d_asenta,
              "codigo de municipio" => $codigoscp[$index]->c_mnpio,
              "municipio" => $codigoscp[$index]->d_mnpio,
              "codigo de estado" => $codigoscp[$index]->c_estado,
              "estado" => $codigoscp[$index]->d_estado
            );
            array_push($datoscp, $datos);
          }

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $datoscp;
        } else{
          $result['code'] = 204;
          $result['status'] = 'warning';
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['data'] = array();
      }
      return Response::json($result);
    }
    public function getPagos(){
      try {
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = DB::table('typepay')->get();

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
      }
      return Response::json($result);

    }

}
