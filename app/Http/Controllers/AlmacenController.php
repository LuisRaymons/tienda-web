<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\CategoriaModel;
use App\Models\ProductoModel;
use App\Models\AlmacenModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    protected $columnas=['id','entrada','salida','stock','id_user','id_producto'];

    /*----------------------------------------------WEB------------------------------------------------*/

    public function index(){
      return view('almacen.index');
    }
    public function getAlmacenData(Request $request){
      try {
        $almacentotal = AlmacenModel::whereNull('deleted_at')->get();
        $almacenColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $almacen = DB::table('almacen')->join('users','almacen.id_user','=','users.id')->join('producto','almacen.id_producto','=','producto.id')
                               ->select('almacen.id','almacen.entrada','almacen.salida','almacen.stock','users.name as username','producto.nombre as productname')
                               ->whereNull('almacen.deleted_at')->where(function ($query) use ($almacenColumn,$word) {
				      foreach ($word as $word) {
							         $query = $query->where(function ($query) use ($almacenColumn,$word) {
								                 foreach ($almacenColumn as $column) {
															      $query->orWhere("almacen." . $column,'like',"%$word%");
															   }
												});
							}
				})->whereBetween('almacen.id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($almacen)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($almacen);
          $result['recordsFiltered']=count($almacentotal);
          $result['data'] = $almacen;
        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['draw'] = 0;
          $result['recordsTotal']=0;
          $result['recordsFiltered']=0;
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 502;
        $result['status'] = 'error';
        $result['draw'] = 0;
        $result['recordsTotal']=0;
        $result['recordsFiltered']=0;
        $result['data'] =array();
        $result['info'] = $e->getMessage();
      }
      return Response::json($result);
    }
    public function getalmacenone($id){
      try {
        $almacen = AlmacenModel::find($id);

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $almacen;

      } catch (\Exception $e) {
        $result['code'] = 200;
        $result['status'] = 'error';
        $result['msm'] = 'Error al obtener informacion del almacen, intente mas tarde o llame a su administrador';
      }
      return Response::json($result);

    }
    public function update(Request $request){
      try {
        if($request->id && $request->id){
          $almacenupdate =  AlmacenModel::find($request->id);
          $almacenupdate->entrada = isset($request->entryalmacen) ? $request->entryalmacen : 0;
          $almacenupdate->salida = isset($request->exitalmacen) ? $request->exitalmacen : 0;
          $almacenupdate->stock = isset($request->stockalmacen) ? $request->stockalmacen : 0;
          $almacenupdate->id_user = isset($request->iduseredit) ? $request->iduseredit : 0;
          $almacenupdate->updated_at = date('Y-m-d H:m:s');
          $almacenupdate->save();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'Se actualizaron los datos de almacen';

        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['msm'] = 'No se reconoce el usuario o el codigo del almacen, intente mas tarde o inicie session';
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar los cambio del almacen, intente mas tarde o llame a su administrador';
      }
      return Response::json($result);

    }

    /*----------------------------------------------API------------------------------------------------*/
    public function getdataalmacen(Request $request){
      try {
        $countexist = AlmacenModel::count();
        $pagina = isset($request->pag) ? $request->pag : 1;
        $registerpagina = isset($request->numpag) ? $request->numpag : 20;

        if($countexist > 0){

          $start = $this->paginainicio($pagina,$registerpagina);
          $end = $this->paginaend($start,$request->numpag - 1);

          $almacenes = AlmacenModel::join('users','almacen.id_user','=','users.id')
                                   ->join('producto','almacen.id_producto','=','producto.id')
                                   ->select('almacen.id','almacen.entrada','almacen.salida','almacen.stock','users.name as usuario','producto.nombre as producto')
                                   ->whereNull('almacen.deleted_at')
                                   ->get();
          $arrayregistros = array();
          $arraydatosfinal = array();

          for ($i=$start; $i <= $end; $i++) {
            array_push($arrayregistros,$i);
          }

          foreach ($almacenes as $key => $a) {
            if(in_array($key,$arrayregistros)){
              array_push($arraydatosfinal,$a);
            }
          }

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['total'] = $countexist;
          $result['registerpag'] = $registerpagina;
          $result['pagina'] = $pagina;
          $result['data'] = $arraydatosfinal;

        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['total'] = $countexist;
          $result['registerpag'] = $registerpagina;
          $result['pagina'] = $pagina;
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion de almacen';
      }
      return Response::json($result);

    }
    public function getonealmacen(Request $request){
      try {
        $id = isset($request->id) ? $request->id : 0;
        $exist = AlmacenModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

        if($exist > 0){
            $almacenselect = AlmacenModel::select('id','entrada','salida','stock','id_user','id_producto')->whereNull('deleted_at')->where('id','=',$request->id)->first();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['data'] = $almacenselect;
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $resul['status'] = 'error';
        $result['msm'] = 'Error al recuperar informacion del almacen selecciondo';
      }
      return Response::json($result);

    }
    public function updateApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id'=> 'required',
             'entry'=> 'required',
             'exit'=> 'required',
             'stock'=> 'required',
           ]
         );
         if(!$validator->fails()){
           $exist = AlmacenModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

           if($exist > 0){
             $modelalmacen = AlmacenModel::whereNull('deleted_at')->where('id','=',$request->id)->first();
             $modelalmacen->entrada = isset($request->entry) ? $request->entry : 0;
             $modelalmacen->salida = isset($request->exit) ? $request->exit : 0;
             $modelalmacen->stock = isset($request->stock) ? $request->stock : 0;
             $modelalmacen->id_user = isset($request->idUser) ? $request->idUser : 0;
             $modelalmacen->updated_at = date('Y-m-d H:m:s');
             $modelalmacen->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El registro de almacen se modifico con exito';

           } else{
             $result['code'] = 400;
             $result['status'] = 'warning';
             $result['msm'] = 'No se encontro el producto en almacen';
           }

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al modificar el almacen" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al modificar los datos de almacen';
      }
      return Response::json($result);

    }
    public function getalmacenall(request $request){
      try {
        $exists = AlmacenModel::whereNull('deleted_at')->count();
        if($exists > 0){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = AlmacenModel::join('users','almacen.id_user','=','users.id')
                                        ->join('producto','almacen.id_producto','=','producto.id')
                                        ->select('almacen.id','almacen.entrada','almacen.salida','almacen.stock','users.name as usuario','producto.nombre as producto')
                                        ->whereNull('almacen.deleted_at')
                                        ->get();
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion de almacen';
      }
      return $result;

    }
    /*---------------------------------Paginado---------------------------------*/
    private function paginainicio($pag,$paginasize){
      if ($pag <= 0) {
          $pag = 1;
      }
      $startRowsInPage = ($pag * $paginasize) - $paginasize;
      return $startRowsInPage;
    }
    private function paginaend($startRowsInPage,$pagesize){
      if ($startRowsInPage <= 1) {
          $startRowsInPage = 0;
      }
      $endRowsInPage = $startRowsInPage + $pagesize;
      return $endRowsInPage;
    }
}
