<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\CompraModel;
use App\Models\AlmacenModel;
use App\Models\ProductoModel;
use App\Models\PromotorModel;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Response;
use DB;

class CompraController extends Controller
{
  protected $columnas=['id','folio','cantidad_stock','precio_total','id_almacen','id_promotor','id_producto'];

  /*-------------------------------WEB---------------------------------------*/
  public function index(){
    return view('compra.index');
  }
  public function getcompraData(Request $request){
      try {
        $compraColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'compra.id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $compra = CompraModel::leftjoin('almacen','compra.id_almacen','=','almacen.id')
                             ->leftjoin('promotor','compra.id_promotor','=','promotor.id')
                             ->leftjoin('producto','compra.id_producto','=','producto.id')
                             ->select('compra.id','compra.folio','compra.cantidad_stock','compra.precio_total','almacen.id as almacen','promotor.nombre as namepromo','producto.nombre as nameproduct')
                             ->whereNull('compra.deleted_at')->where(function ($query) use ($compraColumn,$word) {
                    				      foreach ($word as $word) {
                    							         $query = $query->where(function ($query) use ($compraColumn,$word) {
                    								                 foreach ($compraColumn as $column) {
                    															      $query->orWhere("compra." . $column,'like',"%$word%");
                    															   }
                    												});
                    							}
                    				 })->whereBetween('compra.id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($compra)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($compra);
          $result['recordsFiltered']=count($compra);
          $result['data'] = $compra;
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
        $result['data'] =$e->getMessage();  //array();
      }
      return Response::json($result);

  }
  public function store(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'stockcompra' => 'required',
             'preciocompra' => 'required',
             'productnewcompra' => 'required',
             'promotornewcompra' => 'required',
           ]
         );
         if(!$validator->fails()){
           if (!file_exists("storage/asset/compra/")) {
             mkdir("storage/asset/compra/", 0777, true);
           }

           if(isset($request->imgnewcompra)){
              $file = $request->file('imgnewcompra');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/compra/" . $namefull, \File::get($file));
            }
            $valoridalmacen = 0;
            $existproduct = AlmacenModel::whereNull('deleted_at')->where('id_producto','=', $request->productnewcompra)->count();
            if($existproduct < 1){
              $newAlmacen = new AlmacenModel();
              $newAlmacen->entrada = isset($request->stockcompra) ? $request->stockcompra : 0;
              $newAlmacen->salida = 0;
              $newAlmacen->stock = 0;
              $newAlmacen->id_user = $request->usercompra;
              $newAlmacen->id_producto = isset($request->productnewcompra) ? isset($request->productnewcompra) : 0;
              $newAlmacen->created_at = date('Y-m-d H:m:s');
              $newAlmacen->updated_at =date('Y-m-d H:m:s');
              $newAlmacen->save();
              $valoridalmacen = $newAlmacen->id;
            } else{
              $existsproducts = AlmacenModel::whereNull('deleted_at')->where('id_producto','=', $request->productnewcompra)->first();
              $valoridalmacen = $existsproducts->id;
            }

            $ventaultimoregister = CompraModel::orderBy('id', 'DESC')->first();
            $id = isset($ventaultimoregister->id ) ? intval($ventaultimoregister->id) + 1  : 1;

            $newcompra = new CompraModel();
            $newcompra->folio = $this->generarCodigo(10,$id);
            $newcompra->cantidad_stock = isset($request->stockcompra) ? $request->stockcompra : 0;
            $newcompra->precio_total = isset($request->preciocompra) ? $request->preciocompra : 0.00;
            $newcompra->img = isset($request->imgnewcompra) ? "storage/asset/compra/" . $namefull : '';
            $newcompra->id_almacen = $valoridalmacen;
            $newcompra->id_promotor = isset($request->promotornewcompra) ? isset($request->promotornewcompra) : 0;
            $newcompra->id_producto = isset($request->productnewcompra) ? isset($request->productnewcompra) : 0;
            $newcompra->created_at = date('Y-m-d H:m:s');
            $newcompra->updated_at = date('Y-m-d H:m:s');
            $newcompra->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = 'Compra registrada con exito';

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar la compra" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al insertar la compra';
        $result['info'] = $e->getMessage();
      }
      return Response::json($result);
    }
  public function update(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'stockcompraedit' => 'required',
           'preciocompraedit' => 'required'
         ]
       );
       if(!$validator->fails()){

         if(isset($request->imgeditcompra)){
           $modeldeleteimg = CompraModel::find($request->idupdatecompra);

           $fileimg = explode("/",$modeldeleteimg['img']);
           $carpeta = $fileimg[count($fileimg) -2];
           $fileexis = $fileimg[count($fileimg) - 1];
           \Storage::delete("asset/compra/" . $carpeta . "/" . $fileexis);


            $file = $request->file('imgeditcompra');
            $nombre = $file->getClientOriginalName();
            $namefull = str_replace(' ', '-', $nombre);
            \Storage::disk('local')->put("asset/compra/" . $namefull, \File::get($file));
          }

         $almacen = AlmacenModel::where('id','=',$request->idalmacenedit)
                                ->first();

        $compratemporal = CompraModel::where('id','=',$request->idupdatecompra)->first();
            $items = 0;
            if(intval($compratemporal->cantidad_stock) == intval($request->stockcompraedit)){
              $items = 0;
            } else if(intval($compratemporal->cantidad_stock) > intval($request->stockcompraedit)){
              $items = intval(intval($request->stockcompraedit) - intval($compratemporal->cantidad_stock));
              $almacen->salida = $almacen->salida + intval($items);
            } else if(intval($compratemporal->cantidad_stock) < intval($request->stockcompraedit)){
              $items = intval(intval($request->stockcompraedit) - intval($compratemporal->cantidad_stock));
              $almacen->entrada = $almacen->entrada + intval($items);
            }

          $almacen->stock = intval($almacen->entrada) - intval($almacen->salida);
          $almacen->id_user = isset($request->usercompraedit) ? $request->usercompraedit : 0;
          $almacen->updated_at = date('Y-m-d H:m:s');
          $almacen->save();

          $compra = CompraModel::find($request->idupdatecompra);
          $compra->cantidad_stock = isset($request->stockcompraedit) ? $request->stockcompraedit : $compra->cantidad_stock;
          $compra->precio_total = isset($request->preciocompraedit) ? $request->preciocompraedit : $compra->precio_total;
          $compra->img =  isset($request->$request->imgeditcompra) ? "storage/asset/compra/" . $namefull : $compra->img;
          $compra->id_promotor = isset($request->promotoreditcompra) ? $request->promotoreditcompra : $compra->id_promotor;
          $compra->id_producto = isset($request->producteditcompra) ? $request->producteditcompra : $compra->id_producto;
          $compra->updated_at = date('Y-m-d H:m:s');
          $compra->save();


         $result['code'] = 200;
         $result['status'] = 'success';
         $result['msm'] = 'Se actualizaron los datos de la compra junto con el almacen';


       }else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar compra, intente mas tarde o llame al administrador del sitio';
      $result['info'] = $e->getMessage();
    }
    return Response::json($result);

  }
  public function getcompraone($id){
    try {
      $compra = CompraModel::find($id);

      $result['code'] = 200;
      $result['status'] = 'success';
      $result['data'] = $compra;

    } catch (\Exception $e) {
      $result['code'] = 200;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de la compra';
    }
    return Response::json($result);
  }

  /*-------------------------------WEB---------------------------------------*/
  public function getdatacompra(Request $request){
    try {
      $exist = CompraModel::whereNull('deleted_at')->count();
      $pagina = isset($request->pag) ? $request->pag : 1;
      $registerpagina = isset($request->numpag) ? $request->numpag : 20;

      if($exist > 0){

        $start = $this->paginainicio($pagina,$registerpagina);
        $end = $this->paginaend($start,$request->numpag - 1);

        $promotores = CompraModel::join('promotor','compra.id_promotor','=','promotor.id')
                                 ->join('producto','compra.id_producto','=','producto.id')
                                 ->whereNull('compra.deleted_at')
                                 ->select('compra.id','compra.folio','compra.cantidad_stock','compra.precio_total','compra.img','compra.id_almacen','promotor.nombre as promotor','producto.nombre as producto')
                                 ->orderBy('compra.id','ASC')
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
        $result['status'] = 'warning';
        $result['total'] = $exist;
        $result['registerpag'] = $registerpagina;
        $result['pagina'] = $pagina;
        $result['data'] = array();
      }
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de las compras';

    }
    return Response::json($result);
  }
  public function getdatacompraone(Request $request){
    try {
      $id = isset($request->id) ? $request->id : 0;

      $exist = CompraModel::whereNull('deleted_at')->where('id','=',$id)->count();

      if($exist > 0){
        $promotor = CompraModel::whereNull('deleted_at')->where('id','=',$id)->select('id','folio','cantidad_stock','precio_total','img','id_almacen','id_promotor','id_producto')->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $promotor;
      } else{
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de la compra seleccionado';
    }
    return Response::json($result);
  }
  public function storeApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'stock' => 'required',
          'precio' => 'required',
          'product' => 'required',
          'promotor' => 'required',
          'userid' => 'required'
         ]
       );

       if(!$validator->fails()){
         if (!file_exists("storage/asset/compra/")) {
           mkdir("storage/asset/compra/", 0777, true);
         }

         if(isset($request->img)){
            $file = $request->file('img');
            $nombre = $file->getClientOriginalName();
            $namefull = str_replace(' ', '-', $nombre);
            \Storage::disk('local')->put("asset/compra/" . $namefull, \File::get($file));
          }

          $product = ProductoModel::whereNull('deleted_at')->where('nombre','=', trim($request->product))->count();

          if($product > 0){
            $promotorexist = PromotorModel::whereNull('deleted_at')->where('nombre','=',$request->promotor)->count();

            if($promotorexist > 0){
              $user = isset($request->userid) ? $request->userid : 0;
              $existuser = User::where('id','=',$user)->count();
                if($existuser > 0){
                  $product = ProductoModel::whereNull('deleted_at')->where('nombre','=', trim($request->product))->first();
                  $exists = AlmacenModel::whereNull('deleted_at')->where('id_producto','=', $product->id)->count();

                  if($exists < 1){

                    $modelcompra = new AlmacenModel();
                    $modelcompra->entrada = isset($request->stock) ? $request->stock : 0;
                    $modelcompra->salida = 0;
                    $modelcompra->stock = 0;
                    $modelcompra->id_user = isset($request->userid) ? $request->userid : 0;
                    $modelcompra->id_producto = $product->id;
                    $modelcompra->created_at = date('Y-m-d H:m:s');
                    $modelcompra->updated_at = date('Y-m-d H:m:s');
                    $modelcompra->save();

                    $valoridalmacen = $modelcompra->id;
                  } else{
                    $existsproducts = AlmacenModel::whereNull('deleted_at')->where('id_producto','=',$product->id)->first();
                    $valoridalmacen = $existsproducts->id;
                  }

                  $ventaultimoregister = CompraModel::orderBy('id', 'DESC')->first();
                  $id = isset($ventaultimoregister->id) ? intval($ventaultimoregister->id) + 1 : 1;

                  $promotorexist = PromotorModel::where('deleted_at')->where('nombre','=',trim($request->promotor))->count();

                  if($promotorexist > 0){
                    $promotors = PromotorModel::where('deleted_at')->where('nombre','=',trim($request->promotor))->first();

                    $newcompra = new CompraModel();
                    $newcompra->folio = $this->generarCodigo(10,$id);
                    $newcompra->cantidad_stock = isset($request->stock) ? $request->stock : 0;
                    $newcompra->precio_total = isset($request->precio) ? $request->precio : 0.00;
                    $newcompra->img = isset($request->img) ? "storage/asset/compra/" . $namefull : '';
                    $newcompra->id_almacen = $valoridalmacen;
                    $newcompra->id_promotor = isset($promotors->id) ? $promotors->id : 0;
                    $newcompra->id_producto = isset($product->id) ? $product->id : 0;
                    $newcompra->created_at = date('Y-m-d H:m:s');
                    $newcompra->updated_at = date('Y-m-d H:m:s');
                    $newcompra->save();

                    $result['code'] = 200;
                    $result['status'] = 'success';
                    $result['msm'] = 'Compra registrada con exito';

                  } else{
                    $result['code'] =  202;
                    $result['status'] = 'warning';
                    $result['msm'] = 'El promotor no existe en la base de datos';
                  }

                } else{
                  $resul['code'] = 202;
                  $result['status'] = 'warning';
                  $result['msm'] = 'El usuario no existe en la base de datos';
                }

            } else{
                $result['code'] = 202;
                $result['status'] = 'warning';
                $result['msm'] = 'El promotor no existe en la base de datos';
            }
          } else{
            $result['code'] = 202;
            $result['status'] = 'warning';
            $result['msm'] = 'El producto no existe en la base de datos';
          }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar la compra" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar la compra';
    }
    return Response::json($result);
  }
  public function updateApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'stock' => 'required',
           'precio' => 'required',
           'user'=> 'required'
         ]
       );
       if(!$validator->fails()){
          $compratemporal = CompraModel::where('id','=',$request->id)->first();

         if(isset($request->img)){
           $fileimg = explode("/",$compratemporal->img);
           $carpeta = $fileimg[count($fileimg) -2];
           $fileexis = $fileimg[count($fileimg) - 1];
           \Storage::delete("asset/compra/" . $carpeta . "/" . $fileexis);

            $file = $request->file('img');
            $nombre = $file->getClientOriginalName();
            $namefull = str_replace(' ', '-', $nombre);
            \Storage::disk('local')->put("asset/compra/" . $namefull, \File::get($file));
          }

          $almacen = AlmacenModel::where('id_producto','=',$compratemporal->id_producto)
                                ->first();

            $items = 0;
            if(intval($compratemporal->cantidad_stock) == intval($request->stock)){
              $items = 0;
            } else if(intval($compratemporal->cantidad_stock) > intval($request->stock)){
              $items = intval(intval($request->stock) - intval($compratemporal->cantidad_stock));
              $almacen->salida = $almacen->salida + intval($items);
            } else if(intval($compratemporal->cantidad_stock) < intval($request->stock)){
              $items = intval(intval($request->stock) - intval($compratemporal->cantidad_stock));
              $almacen->entrada = $almacen->entrada + intval($items);
            }

          $almacen->stock = intval($almacen->entrada) - intval($almacen->salida);
          $almacen->id_user = isset($request->user) ? $request->user : 0;
          $almacen->updated_at = date('Y-m-d H:m:s');
          $almacen->save();

          $promotorexist = ProductoModel::whereNull('deleted_at')->where('nombre','=',$request->producto)->first();
          $productexits = PromotorModel::whereNull('deleted_at')->where('nombre','=',$request->promotor)->first();

          $compra = CompraModel::find($request->id);
          $compra->cantidad_stock = isset($request->stock) ? $request->stock : $compra->cantidad_stock;
          $compra->precio_total = isset($request->precio) ? $request->precio : $compra->precio_total;
          $compra->id_promotor = isset($promotorexist) ? $promotorexist->id : 0;
          $compra->id_producto = isset($productexits) ? $productexits->id : 0;

          if(isset($request->$request->img)){
            $compra->img =  isset($request->$request->img) ? "storage/asset/compra/" . $namefull : $compra->img;
          }
          $compra->updated_at = date('Y-m-d H:m:s');
          $compra->save();

         $result['code'] = 200;
         $result['status'] = 'success';
         $result['msm'] = 'Se actualizaron los datos de la compra junto con el almacen';

       }else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }
    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar la compra" . "\n" . $e->getMessage() . "\n" . json_encode($compratemporal));
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = $e->getMessage(); //'Error al guardar la compra';
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
         $exist = CompraModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

         if($exist > 0){
           $modelcompra = CompraModel::whereNull('deleted_at')->where('id','=',$request->id)->first();
           $modelcompra->deleted_at = date('Y-m-d H:m:s');
           $modelcompra->save();

           $result['code'] = 200;
           $result['status'] = 'success';
           $result['msm'] = 'Se elimino la compra con exito';
         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'La compra seleccionado no se encuentra en la base de datos';
         }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al eliminar la compra" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al eliminar la compra seleccionada';
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
  private function generarCodigo($longitud, $codigoventa=0){
      $codigo = "";
      $caracteres="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $max=strlen($caracteres)-1;
      for($i=0;$i < $longitud;$i++)
      {
          $codigo.=$caracteres[rand(0,$max)];
      }
      return "Compra-" . $codigo . "" . $codigoventa . "" . date("Ymd");
    }

}
