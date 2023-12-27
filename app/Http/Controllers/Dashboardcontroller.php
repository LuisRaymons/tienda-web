<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaModel;
use App\Models\ProductoModel;
use App\Models\AlmacenModel;
use App\Models\VentaModel;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use DateTime;

class Dashboardcontroller extends Controller
{

    public function getproductinexistentes(){
      try {
        $productininexistentes = AlmacenModel::join('producto','almacen.id_producto','=','producto.id')
                                             ->where('stock','<',5)
                                             ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $productininexistentes;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion de los productos por expirarse';
      }
      return Response::json($result);
    }
    public function gettotalventasmes(Request $request){
      try {
        $ventabyuser = VentaModel::where('id_users','=',$request->id)
                                 ->whereBetween('created_at',[$request->from,$request->to])
                                 ->sum('precio_total');
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $ventabyuser;
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar el total de la ventas del usuario seleccionado';
      }
      return Response::json($result);

    }
    public function getventasbymestotal(){
      try {
        $detalleventa = array();
        $vendedores = User::where('type','=','Vendedor')->get();

        foreach ($vendedores as $v) {
          $ventas = array();
          $meses = array();

          for($i=1;$i<=12;$i++){
            $fechames = new DateTime(date("Y-m-d",mktime(0,0,0,date("m")-$i,date("d"),date("Y"))));
            $inicio = $fechames->format('Y-m-01');
            $fin = $fechames->format( 'Y-m-t');

            $mes = explode("-",$inicio);
            $messstring = $this->getmeses($mes[1],1);


            $venta = VentaModel::where('id_users','=',$v->id)
                                ->whereBetween('created_at',[$inicio,$fin])
                                ->sum('precio_total');
            array_push($ventas,$venta);
            array_push($meses,$messstring);


            $arrayventasone = array(
              "id" => $v->id,
              "name" => $v->name,
              "backgroundColor" => $this->gerarcodigocolor(),
              "borderColor" => $this->gerarcodigocolor(),
              "ventas" => $ventas,
              "meses" => $meses
            );


          }
          array_push($detalleventa,$arrayventasone);
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $detalleventa;
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = $e->getMessage();// 'Error al recuperar la informacion de los vendedores';
      }
      return Response::json($result);

    }
    public function getventasbyusertotal($id){
      try {

        $arraymeses = array();
        $arrayventatotales = array();
        for($i=1;$i<=12;$i++){
          $fechames = new DateTime(date("Y-m-d",mktime(0,0,0,date("m")-$i,date("d"),date("Y"))));
          $inicio = $fechames->format('Y-m-01');
          $fin = $fechames->format( 'Y-m-t');

          $ventas = VentaModel::where('id_users','=',$id)
                              ->whereBetween('created_at',[$inicio,$fin])
                              ->sum('precio_total');

          $mes = explode("-",$inicio);
          $messstring = $this->getmeses($mes[1],1);
          array_push($arraymeses,$messstring);
          array_push($arrayventatotales,floatval($ventas));
        }

        $detailventas = array(
          "meses" => $arraymeses,
          "ventas" => $arrayventatotales
        );

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $detailventas;
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al obtener las ventas por mes';
      }
      return Response::json($result);

    }


    /*-----------------------------------API------------------------------------*/
    public function getventasbymestotalApi(){
      try {
        $detalleventa = array();
        $vendedores = User::where('type','=','Vendedor')->get();

        foreach ($vendedores as $v) {
          $ventas = array();
          $meses = array();

          for($i=1;$i<=12;$i++){
            $fechames = new DateTime(date("Y-m-d",mktime(0,0,0,date("m")-$i,date("d"),date("Y"))));
            $inicio = $fechames->format('Y-m-01');
            $fin = $fechames->format( 'Y-m-t');

            $mes = explode("-",$inicio);
            $messstring = $this->getmeses($mes[1],2);


            $venta = VentaModel::where('id_users','=',$v->id)
                                ->whereBetween('created_at',[$inicio,$fin])
                                ->sum('precio_total');
            array_push($ventas,round($venta));
            array_push($meses,$messstring);

            $arrayventasone = array(
              "id" => $v->id,
              "name" => $v->name,
              "backgroundColor" => $this->gerarcodigocolor(),
              "borderColor" => $this->gerarcodigocolor(),
              "ventas" => $ventas,
              "meses" => $meses
            );

          }
          
          array_push($detalleventa,$arrayventasone);
        }

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $detalleventa;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = $e->getMessage(); //'Error al recuperar la informacion de los vendedores';
      }
      return Response::json($result);

    }
    public function getventasbyusertotalApi($id){
      try {

        $arraymeses = array();
        $arrayventatotales = array();
        for($i=1;$i<=12;$i++){
          $fechames = new DateTime(date("Y-m-d",mktime(0,0,0,date("m")-$i,date("d"),date("Y"))));
          $inicio = $fechames->format('Y-m-01');
          $fin = $fechames->format( 'Y-m-t');

          $ventas = VentaModel::where('id_users','=',$id)
                              ->whereBetween('created_at',[$inicio,$fin])
                              ->sum('precio_total');

          $mes = explode("-",$inicio);
          $messstring = $this->getmeses($mes[1],2);
          array_push($arraymeses,$messstring);
          array_push($arrayventatotales,floatval($ventas));
        }

        $detailventas = array(
          "meses" => $arraymeses,
          "ventas" => $arrayventatotales
        );

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $detailventas;
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al obtener las ventas por mes';
      }
      return Response::json($result);

    }


    public function getmeses($mes,$type){

      switch ($mes) {
        case 1:
          $mes = ($type == 1) ? "Enero" : 1;
        break;
        case 2:
          $mes = ($type == 1) ?  "Febrero": 2;
        break;
        case 3:
          $mes = ($type == 1) ?  "Marzo": 3;
        break;
        case 4:
          $mes = ($type == 1) ?  "Abril": 4;
        break;
        case 5:
          $mes = ($type == 1) ?  "Mayo": 5;
        break;
        case 6:
          $mes = ($type == 1) ?  "Junio": 6;
        break;
        case 7:
          $mes = ($type == 1) ?  "Julio": 7;
        break;
        case 8:
          $mes = ($type == 1) ?  "Agosto": 8;
        break;
        case 9:
          $mes = ($type == 1) ?  "Septiembre": 9;
        break;
        case 10:
          $mes = ($type == 1) ?  "Octubre" : 10;
        break;
        case 11:
          $mes = ($type == 1) ?  "Noviembre": 11;
        break;
        case 12:
          $mes = ($type == 1) ?  "Diciembre": 12;
        break;
      }
      return $mes;

    }
    public function gerarcodigocolor(){
      $red = rand(0,255);
      $green = rand(0,255);
      $blue = rand(0,255);
      //$alfa = rand(0,255);
      return "rgba(". $red ."," . $green ."," . $blue . ", 1)";
    }
}
