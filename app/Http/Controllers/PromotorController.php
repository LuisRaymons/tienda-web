<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\PromotorModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File AS Filelaravel;
use Illuminate\Support\Facades\DB;
use Exception;

class PromotorController extends Controller
{
  protected $columnas=['id','nombre','direccion','telefono','sitioWeb'];

  /*-------------------------------WEB---------------------------------------*/
  public function index(){
    return view('promotor.index');
  }
  public function getPromotorData(Request $request){
      try {
        $promotortotal = PromotorModel::whereNull('deleted_at')->get();

        $promotorColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $promotor = PromotorModel::whereNull('deleted_at')->where(function ($query) use ($promotorColumn,$word) {
				      foreach ($word as $word) {
							         $query = $query->where(function ($query) use ($promotorColumn,$word) {
								                 foreach ($promotorColumn as $column) {
															      $query->orWhere($column,'like',"%$word%");
															   }
												});
							}
				})->whereBetween('id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($promotor)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($promotor);
          $result['recordsFiltered']=count($promotortotal);
          $result['data'] = $promotor;
        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['draw'] = 0;
          $result['recordsTotal']=0;
          $result['recordsFiltered']=0;
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['draw'] = 0;
        $result['recordsTotal']=0;
        $result['recordsFiltered']=0;
        $result['data'] = array();
      }
      return Response::json($result);

    }
  public function store(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'namepromotor' => 'required',
           'addresspromotor' => 'required',
           'phonepromotor' => 'required',
         ]
       );
       if(!$validator->fails()){
         if (!file_exists("storage/asset/promotor/")) {
           mkdir("storage/asset/promotor/", 0777, true);
         }

         $promotorexist = PromotorModel::where('nombre','=',trim($request->namepromotor))->count();

         if($promotorexist < 1){
           if(isset($request->imgnewpromotor)){
              $file = $request->file('imgnewpromotor');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              Storage::disk('local')->put("asset/promotor/" . $namefull, Filelaravel::get($file));
            }
          $newPromotor = new PromotorModel();
          $newPromotor->nombre = $request->namepromotor;
          $newPromotor->direccion = $request->addresspromotor;
          $newPromotor->telefono = $request->phonepromotor;
          $newPromotor->sitioWeb = isset($request->webpromotor) ? $request->webpromotor : '';
          $newPromotor->img = isset($request->imgnewpromotor) ? "storage/asset/promotor/" . $namefull : '';
          $newPromotor->created_at = date('Y-m-d H:m:s');
          $newPromotor->updated_at = date('Y-m-d H:m:s');
          $newPromotor->save();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = "El proveedor fue registrado con exito";

         }else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = 'El promotor ya existen en la base de datos';
         }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar un promotor" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = "Error al guardar el nuevo promotor";
    }
    return Response::json($result);
  }
  public function getpromotores(){
    try {
      $promotores = PromotorModel::whereNull('deleted_at')->select('id','nombre','img')->distinct()->get();

      if(count($promotores)){
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $promotores;
      } else{
        $result['code'] = 400;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar datos de promotores';
    }
    return Response::json($result);
  }
  public function getpromotorone($id){
    try {
      $promotor = PromotorModel::find($id);

      $result['code'] = 200;
      $result['status'] = 'success';
      $result['data'] = $promotor;

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar informacion del promotor seleccionado';
    }
    return Response::json($result);

  }
  public function update(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'namepromotoredit' => 'required',
           'addresspromotoredit' => 'required',
           'phonepromotoredit' => 'required',
         ]
       );
       if(!$validator->fails()){

         $promotorexist = PromotorModel::where('nombre','=',trim($request->namepromotor))->count();

         if($promotorexist < 1){
           if(isset($request->imgnewpromotoredit)){
             $promotor = PromotorModel::find($request->idpromotor);

             $fileimg = explode("/",$promotor->img);
             $carpeta = $fileimg[count($fileimg) -2];
             $file = $fileimg[count($fileimg) - 1];

             Storage::delete("asset/" . $carpeta . "/" . $file);

              $file = $request->file('imgnewpromotoredit');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              Storage::disk('local')->put("asset/promotor/" . $namefull, Filelaravel::get($file));
            }
          $editPromotor = PromotorModel::find($request->idpromotor);
          $editPromotor->nombre = $request->namepromotoredit;
          $editPromotor->direccion = $request->addresspromotoredit;
          $editPromotor->telefono = $request->phonepromotoredit;
          $editPromotor->sitioWeb = isset($request->webpromotoredit) ? $request->webpromotoredit : '';
          $editPromotor->img = isset($request->imgnewpromotoredit) ? "storage/asset/promotor/" . $namefull : $editPromotor->img;
          $editPromotor->updated_at = date('Y-m-d H:m:s');
          $editPromotor->save();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = "El proveedor fue modificado con exito";

         }else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = 'El promotor ya existen en la base de datos';
         }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al guardar los datos del promotor" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar los datos del promotor';
    }
    return Response::json($result);

  }
  public function destroy($id){
    try {
      $model = PromotorModel::find($id);
      $model->deleted_at = date('Y-m-d H:m:s');
      $model->save();

      $result['code'] = 200;
      $result['status'] = 'success';
      $result['msm'] = 'Registro de promotor fue eliminado con exito';
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al eliminar el registro de usuario intente mas tarde o llame al admnistrador del sitio';
    }
    return Response::json($result);
  }

  /*-------------------------------API---------------------------------------*/
  public function getdatapromotor(Request $request){
    try {
      $exist = PromotorModel::whereNull('deleted_at')->count();
      $pagina = isset($request->pag) ? $request->pag : 1;
      $registerpagina = isset($request->numpag) ? $request->numpag : 20;

      if($exist > 0){

        $start = $this->paginainicio($pagina,$registerpagina);
        $end = $this->paginaend($start,$request->numpag - 1);

        $promotores = PromotorModel::whereNull('deleted_at')
                                   ->select('id','nombre','direccion','telefono','sitioWeb','img')
                                   ->get();

        $arrayregistros = array();
        $arraydatosfinal = array();

        for ($i=$start; $i <= $end; $i++) {
          array_push($arrayregistros,$i);
        }
       foreach ($promotores as $key => $p) {
         if(in_array($key,$arrayregistros)){
           array_push($arraydatosfinal,$p);
         }
       }

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['total'] = $exist;
        $result['registerpag'] = $registerpagina;
        $result['pagina'] = $pagina;
        $result['data'] = $arraydatosfinal;
      } else{
        $result['code'] = 202;
        $result['status']  = 'warning';
        $result['total'] = $exist;
        $result['registerpag'] = $registerpagina;
        $result['pagina'] = $pagina;
        $result['data'] = array();
      }
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de los clientes';
    }
    return Response::json($result);
  }
  public function getdatapromotorone(Request $request){
    try {
      $id = isset($request->id) ? $request->id : 0;

      $exist = PromotorModel::whereNull('deleted_at')->where('id','=', $id)->count();

      if($exist > 0){
        $promotor = PromotorModel::whereNull('deleted_at')->where('id','=', $id)->select('id','nombre','direccion','telefono','sitioWeb','img')->get();

        $resul['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $promotor;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar el promotor seleccionado';
    }
    return Response::json($result);
  }
  public function storeApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'name' => 'required',
          'address' => 'required',
          'phone' => 'required'
        ]
      );

       if(!$validator->fails()){
         $name = isset($request->name) ? trim($request->name) : '';

         $existproveedor = PromotorModel::whereNull('deleted_at')
                                        ->where('nombre','=',$name)
                                        ->count();
         if($existproveedor < 1){
           if (!file_exists("storage/asset/promotor/")) {
             mkdir("storage/asset/promotor/", 0777, true);
           }

           if(isset($request->img)){
              $file = $request->file('img');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              Storage::disk('local')->put("asset/promotor/" . $namefull, Filelaravel::get($file));
            }

            $modelproveedor = new PromotorModel();
            $modelproveedor->nombre = isset($request->name) ? $request->name : '';
            $modelproveedor->direccion = isset($request->address) ? $request->address : '';
            $modelproveedor->telefono = isset($request->phone) ? $request->phone : '';
            $modelproveedor->sitioWeb = isset($request->website) ? $request->website : '';
            $modelproveedor->img = isset($request->img) ? "storage/asset/promotor/" . $namefull : '';
            $modelproveedor->created_at = date('Y-m-d H:m:s');
            $modelproveedor->updated_at = date('Y-m-d H:m:s');
            $modelproveedor->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = 'El registro de proveedor fue guardado con exito';
         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'El proveedor ya existe en la base de datos';
         }
       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar un promotor" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar el nuevo proveedor';
    }
    return Response::json($result);
  }
  public function updateApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'name' => 'required',
          'address' => 'required',
          'phone' => 'required'
        ]
      );

       if(!$validator->fails()){

         $existproveedor = PromotorModel::whereNull('deleted_at')
                                        ->where('id','=',$request->id)
                                        ->count();
         if($existproveedor > 0){
           if (!file_exists("storage/asset/promotor/")) {
             mkdir("storage/asset/promotor/", 0777, true);
           }

           if(isset($request->img)){
             $file = $request->file('img');
             $nombre = $file->getClientOriginalName();
             $namefull = str_replace(' ', '-', $nombre);
             Storage::disk('local')->put("asset/promotor/" . $namefull, Filelaravel::get($file));
            }

            $modelproveedor = PromotorModel::find($request->id);
            $modelproveedor->nombre = isset($request->name) ? $request->name : '';
            $modelproveedor->direccion = isset($request->address) ? $request->address : '';
            $modelproveedor->telefono = isset($request->phone) ? $request->phone : '';
            $modelproveedor->sitioWeb = isset($request->website) ? $request->website : '';
            if(isset($request->img)){
              $modelproveedor->img = isset($request->img) ? "storage/asset/promotor/" . $namefull : '';
            }
            $modelproveedor->updated_at = date('Y-m-d H:m:s');
            $modelproveedor->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = 'El registro de proveedor fue modificado con exito';
        }else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = 'El proveedor seleccionado no existe en la base de datos';
        }
       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al modificar el promotor" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al modificar el registro de promotor';
    }
    return Response::json($result);
  }
  public function getdatapromotorall(Request $request){
    try {
      $exist = PromotorModel::whereNull('deleted_at')->count();
      if($exist > 0) {
        $promotores = PromotorModel::whereNull('deleted_at')
                                   ->select('id','nombre','direccion','telefono','sitioWeb','img')
                                   ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $promotores;
      }else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de los proveedores';
    }
    return Response::json($result);
  }
  public function destroyApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'id' => 'required'
         ]
       );
       if(!$validator->fails()){

         $exist = PromotorModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

         if($exist > 0){
           $modelpromotor = PromotorModel::whereNull('deleted_at')->where('id','=',$request->id)->first();
           $modelpromotor->deleted_at = date('Y-m-d H:m:s');
           $modelpromotor->save();

           $result['code'] = 200;
           $result['status'] = 'success';
           $result['msm'] = 'El promotor se elimino con exito';

         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'No se encontro el proveedor a eliminar';
         }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al eliminar un promotor" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al eliminar el promotor seleccionado';
    }
    return Response::json($result);
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
