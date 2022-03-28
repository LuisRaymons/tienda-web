<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PrecioProductoModel;
use App\Models\ProductoModel;
use Illuminate\Support\Facades\Log;
use Response;

class PrecioProductoController extends Controller
{
    protected $columnas=['id','nombre','img'];
    /*---------------------------WEB----------------------------*/
    public function index(){
      return view('productoprecio.index');
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
    public function getProductoPriceData(Request $request){
        try {
          $productspricetotal = PrecioProductoModel::whereNull('deleted_at')->get();
          $productpriceColumn=$this->columnas;
  				$word = explode(" ",$request->search['value']);

          $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
          $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

          $productsprice = PrecioProductoModel::join('producto','productoprecio.id_product','=','producto.id')->select('productoprecio.id','producto.nombre','producto.img','productoprecio.precio')->whereNull('productoprecio.deleted_at')->where(function ($query) use ($productpriceColumn,$word) {
  				      foreach ($word as $word) {
  							         $query = $query->where(function ($query) use ($productpriceColumn,$word) {
  								                 foreach ($productpriceColumn as $column) {
  															      $query->orWhere("producto." . $column,'like',"%$word%");
  															   }
  												});
  							}
  				})->whereBetween('productoprecio.id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

          $draw = isset($request->draw) ? $request->draw : 0;

          if(!empty($productsprice)){
            $result['code'] = 200;
            $result['status'] = 'success';
            $result['draw'] = $draw;
            $result['recordsTotal']=count($productsprice);
            $result['recordsFiltered']=count($productspricetotal);
            $result['data'] = $productsprice;
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
          $result['orderby'] = $request->order[0]['column'];
          $result['order'] = $request->order[0]['dir'];
        }
        return Response::json($result);

      }
    public function getProductPriceMissing(){
      try {
        $ids = array();
        $priceproduct = PrecioProductoModel::whereNull('deleted_at')->select('id_product')->get();

        foreach ($priceproduct as $id) {
          array_push($ids,$id['id_product']);
        }

        $products = ProductoModel::whereNull('deleted_at')->whereNotIn('id',$ids)->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $products;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informcacion de los productos faltantes de precio';
      }
      return Response::json($result);
    }
    public function store(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'product' => 'required',
             'precioproduct' => 'required'
           ],
           [
             'product.required' => 'Seleccione un producto',
             'precioproduct.required' => 'Ingrese el precio del producto'
           ]
          );

         if(!$validator->fails()){
           $product = isset($request->product) ? $request->product : 0;
           $precio = isset($request->precioproduct) ? $request->precioproduct : 0.00;

           $productexist = ProductoModel::whereNull('deleted_at')->where('id','=',$product)->count();

           if($productexist > 0){
             $exist = PrecioProductoModel::whereNull('deleted_at')->where('id_product','=',$product)->count();

             if($exist == 0){
               $modelprecioproduct = new PrecioProductoModel();
               $modelprecioproduct->precio = $precio;
               $modelprecioproduct->id_product = $product;
               $modelprecioproduct->created_at = date('Y-m-d H:m:s');
               $modelprecioproduct->updated_at = date('Y-m-d H:m:s');
               $modelprecioproduct->save();

               $result['code'] = 200;
               $result['status'] = 'success';
               $result['msm'] = 'El producto se le asignado el precio correctamente';

             } else{
               $result['code'] = 202;
               $result['status'] = 'warning';
               $result['msm'] = 'El producto seleccionado ya se le asigno el precio';
             }
           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'El producto seleccionado no se encuentra en la base de datos';
           }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Erro al guardar el precio del producto seleccionado';
      }
      return Response::json($result);
    }
    public function update(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id' => 'required',
             'precioproduct' => 'required'
           ],
           [
             'id.required' => 'El iid del precio de producto es requerido',
             'precioproduct.required' => 'Ingrese el precio del producto'
           ]
         );

         if(!$validator->fails()){
           $precio = isset($request->precioproduct) ? $request->precioproduct : 0.00;

           $exist = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

           if($exist > 0){
             $modelprecioproduct = PrecioProductoModel::find($request->id);
             $modelprecioproduct->precio = $precio;
             $modelprecioproduct->updated_at = date('Y-m-d H:m:s');
             $modelprecioproduct->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El producto se le asignado el precio correctamente';

           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'El producto seleccionado no se encontro en la base de datos';
           }

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al modificar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $resultl['msm'] = 'Error al modificar el precio del producto seleccionado';
      }
      return Response::json($result);
    }
    public function destroy($id){
      try {
        $exist = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$id)->count();

        if($exist > 0){
          $exist = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$id)->update([
            'deleted_at' => date('Y-m-d H:m:s')
          ]);

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'El producto fue eliminado con exito';

        } else {
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['msm'] = 'No se encontro el producto para eliminar su precio';
        }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al eliminar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar el precio del producto';
      }
      return Response::json($result);
    }
    public function getprecioproductbyid($id){
      try {
        $exits = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$id)->count();

        if($exits > 0){
          $seleccionado = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$id)->first();

          $seleccionado = PrecioProductoModel::join('producto','productoprecio.id_product','=','producto.id')
                                         ->select('productoprecio.id as idproductprecio','productoprecio.precio','productoprecio.id_product','producto.id','producto.nombre')
                                         ->whereNull('productoprecio.deleted_at')
                                         ->where('productoprecio.id','=',$id)
                                         ->first();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $seleccionado;
        } else{
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'El producto de precio seleccionado no se encuentra en la base de datos';
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al obtener el precio del producto seleccionado';
      }
      return Response::json($result);
    }
    public function getProductPriceExists(){
      try {
        $exist = PrecioProductoModel::whereNull('deleted_at')->count();
        if($exist > 0){

          $ids = array();

          $products = PrecioProductoModel::join('producto','productoprecio.id_product','=','producto.id')
                                         ->select('productoprecio.id as idproductprecio','producto.id','producto.nombre')
                                         ->whereNull('productoprecio.deleted_at')
                                         ->get();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $products;
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar los productos existentes con precio';
      }
      return Response::json($result);
    }

    /*---------------------------API----------------------------*/
    public function getprodutpreciotable(Request $request){
      try {
        $exist = PrecioProductoModel::whereNull('deleted_at')->count();
        $pagina = isset($request->pag) ? $request->pag : 1;
        $registerpagina = isset($request->numpag) ? $request->numpag : 20;

        if($exist > 0){

          $start = $this->paginainicio($pagina,$registerpagina);
          $end = $this->paginaend($start,$request->numpag - 1);

          $productos = PrecioProductoModel::join('producto','productoprecio.id_product','=','producto.id')
                                    ->whereNull('productoprecio.deleted_at')
                                    ->select('productoprecio.id','producto.nombre','productoprecio.precio')
                                    ->get();
          $arrayregistros = array();
          $arraydatosfinal = array();

          for ($i=$start; $i <= $end; $i++) {
            array_push($arrayregistros,$i);
          }

          foreach ($productos as $key => $p) {
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
        $result['msm']  = 'Error al recuperar la informacion de los productos';
      }
      return Response::json($result);
    }
    public function storeApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'product' => 'required',
             'price' => 'required'
           ],
           [
             'product.required' => 'Seleccione un producto',
             'price.required' => 'Ingrese el precio del producto'
           ]
          );

         if(!$validator->fails()){
           $existproduct = ProductoModel::whereNull('deleted_at')->where('nombre','=', trim($request->product))->count();

           if($existproduct > 0){
             $product = ProductoModel::whereNull('deleted_at')->where('nombre','=', trim($request->product))->first();

             $existprecio = PrecioProductoModel::whereNull('deleted_at')->where('id_product','=',$product->id)->count();
             if($existprecio < 1){
               $modelprecioproduct = new PrecioProductoModel();
               $modelprecioproduct->precio  = $request->price;
               $modelprecioproduct->id_product  = $product->id;
               $modelprecioproduct->created_at = date('Y-m-d H:m:s');
               $modelprecioproduct->updated_at = date('Y-m-d H:m:s');
               $modelprecioproduct->save();

               $result['code'] = 200;
               $result['status'] = 'success';
               $result['msm'] = 'El producto se guardo con correctamente';
             } else{
               $result['code'] = 202;
               $result['status'] = 'warning';
               $result['msm'] = 'El producto ya tiene precio, busque el producto, intente cambiar su precio';
             }

           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'El producto no se encuentra en la base de datos';
           }

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar el precio del producto';
      }
      return Response::json($result);
    }
    public function updateApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id' => 'required',
             'price' => 'required'
           ],
           [
             'id.required' => 'El id del precio de producto es requerido',
             'price.required' => 'Ingrese el precio del producto'
           ]
          );

         if(!$validator->fails()){
           $existprecio = PrecioProductoModel::whereNull('deleted_at')->where('id_product','=',$request->id)->count();
           if($existprecio > 0){
             $modelprecioproduct = PrecioProductoModel::find($request->id);
             $modelprecioproduct->precio  = $request->price;
             $modelprecioproduct->updated_at = date('Y-m-d H:m:s');
             $modelprecioproduct->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El producto se modifico con correctamente';
           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'No se encontro el producto y su precio';
           }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al modificar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar el precio del producto';
      }
      return Response::json($result);
    }
    public function destroyApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id' => 'required'
           ],
           [
             'id.required' => 'id del precio del producto es requerido'
           ]
          );

         if(!$validator->fails()){
           $exist = PrecioProductoModel::whereNull('deleted_at')->where('id','=',$request->id)->count();

           if($exist > 0){

             $modelprecioproduct =  PrecioProductoModel::whereNull('deleted_at')->where('id','=',$request->id)->first();
             $modelprecioproduct->deleted_at = date('Y-m-d H:m:s');
             $modelprecioproduct->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El registro del precio de producto fue eliminado con exito';

           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'El precio de producto no se encontro, intente mas tarde';
           }

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al eliminar el precio de producto" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar el precio del producto';
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
