<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\CategoriaModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CategoriaController extends Controller
{
    protected $columnas=['id','nombre'];

    /*-----------------------------------WEB------------------------------------*/
    public function index(){
      return view('categoria.index');
    }
    public function getCategoriaData(Request $request){
      try {
        $categoriatotal = CategoriaModel::whereNull('deleted_at')->get();
        $categoriaColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $almacen = CategoriaModel::whereNull('deleted_at')->where(function ($query) use ($categoriaColumn,$word) {
				      foreach ($word as $word) {
							         $query = $query->where(function ($query) use ($categoriaColumn,$word) {
								                 foreach ($categoriaColumn as $column) {
															      $query->orWhere($column,'like',"%$word%");
															   }
												});
							}
				})->whereBetween('id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($almacen)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($almacen);
          $result['recordsFiltered']=count($categoriatotal);
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
    public function getcategoria(){
      try {
        $categoria = CategoriaModel::whereNull('deleted_at')->get();

        if(count($categoria) > 0){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $categoria;
        } else{
          $result['code'] = 400;
          $result['status'] = 'success';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar las categoria';
      }
      return Response::json($result);
    }
    public function getcategoriaone($id){
      try {
        $modelcategoria = CategoriaModel::find($id);

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $modelcategoria;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion de la categoria del producto';
      }
      return Response::json($result);
    }
    public function update(Request $request){
      try {
        $existproduct = CategoriaModel::where('id','=',$request->idupdatecategoria)->count();

        if($existproduct > 0){
          $modelcategoria = CategoriaModel::where('id','=',$request->idupdatecategoria)->update([
            "nombre" => isset($request->categorianameupdate) ? $request->categorianameupdate : ''
          ]);

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'Se modifico la categoria del producto con exito';

        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['msm'] = 'No se encontro el registro a modificar';
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al modoificar la informacion de la compra';
      }
      return Response::json($result);

    }
    public function destroy($id){
      try {
        $existproduct = CategoriaModel::where('id','=',$id)->count();

        if($existproduct > 0){
          $modelcategoria = CategoriaModel::where('id','=',$id)->update([
            "deleted_at" => date("Y-m-d H:m:s")
          ]);
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'El registro seleccionado de categoria de producto se elimino con exito';
        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['msm'] = 'NO se encontro la categoria de producto seleccionado';
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar el registro de categoria de producto';
      }
      return Response::json($result);
    }
    public function store(Request $request){
      try {
        $namecategoria = trim($request->namenewcategoria);
        $existcategoria = CategoriaModel::where('nombre','=', $namecategoria)->count();

        if($existcategoria < 1){
          $modelcategoria = new CategoriaModel();
          $modelcategoria->nombre = isset($request->namenewcategoria) ? $namecategoria : '';
          $modelcategoria->created_at = date("Y-m-d H:m:s");
          $modelcategoria->updated_at = date("Y-m-d H:m:s");
          $modelcategoria->save();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'Se registro la categoria con exito';

        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['msm'] = 'Ya categoria ya existe en la base de datos';
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al crear la nueva categoria';
      }
      return Response::json($result);

    }

    /*-----------------------------------API------------------------------------*/
    public function getdatacategoriaproducto(Request $request){
      try {
        $exist = CategoriaModel::whereNull('deleted_at')->count();
        $pagina = isset($request->pag) ? $request->pag : 1;
        $registerpagina = isset($request->numpag) ? $request->numpag : 20;

        if($exist > 0){

          $start = $this->paginainicio($pagina,$registerpagina);  // 0
          $end = $this->paginaend($start,$request->numpag - 1); // 19

          $categorias = CategoriaModel::whereNull('deleted_at')->select('id','nombre')->get();

          $arrayregistros = array();
          $arraydatosfinal = array();

          for ($i=$start; $i <= $end; $i++) {
            array_push($arrayregistros,$i);
          }

          foreach ($categorias as $key => $c) {
            if(in_array($key,$arrayregistros)){
              array_push($arraydatosfinal,$c);
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
          $result['status'] = 'warning';
          $result['total'] = $exist;
          $result['registerpag'] = $registerpagina;
          $result['pagina'] = $pagina;
          $result['msm'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion de la categoria de productos';
      }
      return Response::json($result);

    }
    public function getdatacategoriaproductoone(Request $request){
      try {

        $validator = Validator::make($request->all(),
           [
             'id' => 'required'
           ],
           [
             'id.required' => 'El id es requerido'
           ]
         );
        if(!$validator->fails()){
          $id = isset($request->id) ? $request->id : 0;

          $exist = CategoriaModel::whereNull('deleted_at')->where('id','=',$id)->count();
          if($exist > 0){

            $modelcategoria = CategoriaModel::whereNull('deleted_at')->where('id','=',$id)->select('id','nombre')->first();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['data'] = $modelcategoria;
          } else{
            $result['code'] = 202;
            $result['status'] = 'warning';
            $result['data'] = array();
          }
        } else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = $validator->errors();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la categoria seleccionada';
      }
      return Response::json($result);
    }
    public function getcategoriaApi(Request $request){
      try {
        $countcategorias = CategoriaModel::whereNull('deleted_at')->count();

        if($countcategorias > 0){
          $categorias = CategoriaModel::whereNull('deleted_at')->select('id','nombre')->get();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $categorias;
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar las categorias';
      }
      return Response::json($result);
    }
    public function storeApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'nombre' => 'required'
           ],
           [
             'nombre.required' => 'El nombre de la categoria es requerida'
           ]
         );
        if(!$validator->fails()){
              $exist = CategoriaModel::whereNull('deleted_at')->where('nombre','=',trim($request->nombre))->count();

              if($exist < 1){
                $model = new CategoriaModel();
                $model->nombre = isset($request->nombre) ? trim($request->nombre) : '';
                $model->created_at  = date('Y-m-d H:m:s');
                $model->updated_at = date('Y-m-d H:m:s');
                $model->save();

                $result['code'] = 200;
                $result['status'] = 'success';
                $result['data'] = 'El regitro de categoria de producto se guardo con exito';

              } else{
                $result['code'] = 402;
                $result['status'] = 'warning';
                $result['msm'] = 'La categoria ya existe en la base de datos';
              }
        } else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = $validator->errors();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al tratar de insertar el registro de categoria de producto';
      }
      return Response::json($result);
    }
    public function updateApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id' => 'required',
             'nombre' => 'required'
           ],
           [
             'id.required' => 'Se requiere el id del producto',
             'nombre.required' => 'El nombre de la categoria es requerida'
           ]
         );
        if(!$validator->fails()){

          $existproduct = CategoriaModel::whereNull('deleted_at')->where('id','=',trim($request->id))->count();

          if($existproduct > 0){
            $modelcategoria = CategoriaModel::find($request->id);

            $modelcategoria->nombre = isset($request->nombre) ? $request->nombre : '';
            $modelcategoria->updated_at = date('Y-m-d H:m:s');
            $modelcategoria->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['data'] = CategoriaModel::where('id','=',$request->id)->select('id','nombre')->first();
            $result['msm'] = 'El registro de categoria de producto se modifico con exito';

          } else{
            $result['code'] = 402;
            $result['status'] = 'warning';
            $result['msm'] = 'No se encontro la categoria a modificar';
          }

        } else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = $validator->errors();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al modificar el registro de categoria de producto';
      }
      return Response::json($result);
    }
    public function destroyApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id' => 'required',
           ],
           [
             'id.required' => 'Se requiere el id de la categoria de producto',
           ]
         );
        if(!$validator->fails()){
          $existcategoria = CategoriaModel::where('id','=',$request->id)->count();

          if($existcategoria > 0){
            $model = CategoriaModel::find($request->id);
            $model->deleted_at = date('Y-m-d H:m:s');
            $model->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = 'El registro de categoria de producto fue eliminado con exito';
          } else{
            $result['code'] = 402;
            $result['status'] = 'warning';
            $result['msm'] = 'La categoria de producto no se encontro';
          }

        } else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = $validator->errors();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar la categoria de producto';
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
