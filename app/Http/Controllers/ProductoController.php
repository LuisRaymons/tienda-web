<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use App\Models\CategoriaModel;
use App\Models\ProductoModel;
use App\Models\AlmacenModel;
use Illuminate\Support\Facades\Log;
use Response;
use DB;

class ProductoController extends Controller
{
  protected $columnas=['id','nombre','descripcion','precioPorKilo','img','id_categoria'];

  /*-----------------------------WEB-----------------------------*/
  public function index(){
    return view('producto.index');
  }
  public function getProductoData(Request $request){
      try {
        $productstotal = ProductoModel::whereNull('deleted_at')->get();
        $productColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $products = ProductoModel::join('categoria_producto','producto.id_categoria','=','categoria_producto.id')->select('producto.id','producto.nombre','producto.descripcion','producto.precioPorKilo','producto.img','categoria_producto.nombre as categoria')->whereNull('producto.deleted_at')->where(function ($query) use ($productColumn,$word) {
				      foreach ($word as $word) {
							         $query = $query->where(function ($query) use ($productColumn,$word) {
								                 foreach ($productColumn as $column) {
															      $query->orWhere("producto." . $column,'like',"%$word%");
															   }
												});
							}
				})->whereBetween('producto.id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($products)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($products);
          $result['recordsFiltered']=count($productstotal);
          $result['data'] = $products;
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
  public function store(Request $request){
    try {
      $validator = Validator::make($request->all(),
         [
           'nameproduct' => 'required',
           'descriptionproduct' => 'required',
           'categoriaProduct' => 'required'
         ]
       );
       if(!$validator->fails()){
         if (!file_exists("storage/asset/productos/")) {
           mkdir("storage/asset/productos/", 0777, true);
         }

         if (!file_exists("storage/asset/productos/QR")) {
           mkdir("storage/asset/productos/QR", 0777, true);
         }

         $productexist = ProductoModel::where('nombre', '=', trim($request->nameproduct))->count();
         if($productexist < 1){
           if(isset($request->imgnewproduct)){
              $file = $request->file('imgnewproduct');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/productos/" . $namefull, \File::get($file));
            }

            $newProducto = new ProductoModel();
            $newProducto->nombre = trim($request->nameproduct);
            $newProducto->descripcion = trim($request->descriptionproduct);
            $newProducto->precioPorKilo = trim($request->preciocatekilo);
            $newProducto->img = isset($request->imgnewproduct) ? "storage/asset/productos/" . $namefull : '';
            $newProducto->id_categoria = $request->categoriaProduct;
            $newProducto->created_at = date('Y-m-d H:m:s');
            $newProducto->updated_at = date('Y-m-d H:m:s');
            $newProducto->save();

            #Generar QR de producto
            QrCode::size(600)->generate(trim($request->nameproduct), '../public/storage/asset/productos/QR/'.$newProducto->id . '-'. trim($request->nameproduct) .'.svg');

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = 'El registro de producto fue guardado con exito';

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = "Este producto ya existe en la base de datos";
         }
       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar el producto" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar la informacion del nuevo producto';
    }
    return Response::json($result);
  }
  public function getproducts(){
    try {
      $productos = ProductoModel::whereNull('deleted_at')->select('id','nombre')->distinct()->get();

      if(count($productos)){
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $productos;
      } else{
        $result['code'] = 400;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar datos de productos';
    }
    return Response::json($result);
  }
  public function getproductone($id){
    try {
      $product = ProductoModel::find($id);

      $result['code'] = 200;
      $result['status'] = 'success';
      $result['data'] = $product;

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion del producto, pongase en contacto con el administrador del sitio';
    }
    return Response::json($result);

  }
  public function update(Request $request){
    try {

      $validator = Validator::make($request->all(),
         [
           'nameproductedit' => 'required',
           'descriptionproductedit' => 'required',
           'categoriaProductedit' => 'required'
         ]
       );
       if(!$validator->fails()){

          $existclient = ProductoModel::where('id','=',$request->idproductedit)->count();

          if($existclient > 0){
            if(isset($request->imgproductedit)){

              $product = ProductoModel::find($request->idproductedit);

              $fileimg = explode("/",$product->img);
              $carpeta = $fileimg[count($fileimg) -2];
              $file = $fileimg[count($fileimg) - 1];

              \Storage::delete("asset/" . $carpeta . "/" . $file);

               $file = $request->file('imgproductedit');
               $nombre = $file->getClientOriginalName();
               $namefull = str_replace(' ', '-', $nombre);
               \Storage::disk('local')->put("asset/productos/" . $namefull, \File::get($file));
             }

            $modelproductedit = ProductoModel:: find($request->idproductedit);
            $modelproductedit->nombre = isset($request->nameproductedit) ?  trim($request->nameproductedit): '';
            $modelproductedit->descripcion = isset($request->descriptionproductedit) ?  trim($request->descriptionproductedit) : '';
            $modelproductedit->precioPorKilo = ($request->pricekiloproductedit == 'on') ?  'true' : 'false';
            $modelproductedit->img = isset($request->imgproductedit) ? "storage/asset/productos/" . $namefull : $modelproductedit->img;
            $modelproductedit->id_categoria = isset($request->categoriaProductedit) ?  $request->categoriaProductedit: '';
            $modelproductedit->updated_at  = date('Y-m-d H:m:s');
            $modelproductedit->save();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['msm'] = "Producto modificado con exito";
          } else{
            $result['code'] = 400;
            $result['status'] = 'error';
            $result['msm'] = "El producto no existe";
          }


       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al modificar el producto';
      $result['info'] = $e->getMessage();
    }
    return Response::json($result);

  }
  public function destroy($id){
    try {
      $model = ProductoModel::find($id);
      $model->deleted_at = date('Y-m-d H:m:s');
      $model->save();

      $result['code'] = 200;
      $result['status'] = 'success';
      $result['msm'] = 'Registro de producto fue eliminado con exito';
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al eliminar el registro de usuario intente mas tarde o llame al admnistrador del sitio';
    }
    return Response::json($result);

  }
  public function existproductalmacen($id){
    try {
      $product = AlmacenModel::where('id_producto','=',$id)
                             ->whereNull('deleted_at')
                             ->sum('stock');
      $result['code'] = 200;
      $result['status'] = 'success';
      $result['cantidaditems'] = intval($product);

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar informacion del producto seleccionado';
    }
    return Response::json($result);

  }

  /*-----------------------------API-----------------------------*/
  public function getdataproduct(Request $request){
    try {
      $exist = ProductoModel::whereNull('deleted_at')->count();
      $pagina = isset($request->pag) ? $request->pag : 1;
      $registerpagina = isset($request->numpag) ? $request->numpag : 20;

      if($exist > 0){

        $start = $this->paginainicio($pagina,$registerpagina);
        $end = $this->paginaend($start,$request->numpag - 1);

        $productos = ProductoModel::join('categoria_producto','producto.id_categoria','=','categoria_producto.id')
                                  ->whereNull('producto.deleted_at')
                                  ->select('producto.id','producto.nombre as nombrepoducto','producto.descripcion','producto.precioPorKilo','producto.img','categoria_producto.nombre as nombrecategoria')
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
  public function getdataproductone(Request $request){
    try {
      $id = isset($request->id) ? $request->id : 0;
      $exist = ProductoModel::whereNull('deleted_at')->where('id','=',$id)->count();
      if($exist > 0){
        $producto = ProductoModel::whereNull('deleted_at')->where('id','=',$id)->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $producto;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar el producto seleccionado';
    }
    return Response::json($result);
  }
  public function storeApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'name' => 'required',
          'description' => 'required',
          'categoria' => 'required',
          'pricekilo' => 'required',
        ]
      );
       if(!$validator->fails()){
         $existsproduct = ProductoModel::where('nombre','=',trim($request->name))->count();

         if($existsproduct < 1){

           $existscategoria = CategoriaModel::whereNull('deleted_at')->where('nombre','=',trim($request->categoria))->count();

           if($existscategoria > 0){
             if (!file_exists("storage/asset/productos/")) {
               mkdir("storage/asset/productos/", 0777, true);
             }

             if(isset($request->img)){
                $file = $request->file('img');
                $nombre = $file->getClientOriginalName();
                $namefull = str_replace(' ', '-', $nombre);
                \Storage::disk('local')->put("asset/productos/" . $namefull, \File::get($file));
              }

             $categoria = CategoriaModel::whereNull('deleted_at')->where('nombre','=',trim($request->categoria))->first();

             $modelproduct = new ProductoModel();
             $modelproduct->nombre =  isset($request->name) ? $request->name : '';
             $modelproduct->descripcion =  isset($request->description) ? $request->description : '';
             $modelproduct->precioPorKilo = isset($request->pricekilo) ? $request->pricekilo : 'false';
             $modelproduct->img = isset($request->img) ? "storage/asset/productos/" . $namefull : '';
             $modelproduct->id_categoria = isset($categoria->id) ? $categoria->id : 0;
             $modelproduct->created_at = date('Y-m-d H:m:s');
             $modelproduct->updated_at = date('Y-m-d H:m:s');
             $modelproduct->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El registro de producto fue almacenado con exito';
           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'La categoria seleccionada no existe, intente con otra';
           }
         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'Este producto ya existe en almacen';
        }
       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }
    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar el producto" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar el nuevo producto';
    }
    return Response::json($result);
  }
  public function updateApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'name' => 'required',
          'description' => 'required',
          'categoria' => 'required',
          'pricekilo' => 'required',
        ]
      );
       if(!$validator->fails()){
         $existsproduct = ProductoModel::where('id','=',$request->id)->count();

         if($existsproduct > 0){

           $existscategoria = CategoriaModel::whereNull('deleted_at')->where('nombre','=',trim($request->categoria))->count();

           if($existscategoria > 0){
             if (!file_exists("storage/asset/productos/")) {
               mkdir("storage/asset/productos/", 0777, true);
             }

             if(isset($request->img)){
                $file = $request->file('img');
                $nombre = $file->getClientOriginalName();
                $namefull = str_replace(' ', '-', $nombre);
                \Storage::disk('local')->put("asset/productos/" . $namefull, \File::get($file));
              }

             $categoria = CategoriaModel::whereNull('deleted_at')->where('nombre','=',trim($request->categoria))->first();

             $modelproduct = ProductoModel::find($request->id);
             $modelproduct->nombre =  isset($request->name) ? $request->name : '';
             $modelproduct->descripcion =  isset($request->description) ? $request->description : '';
             $modelproduct->precioPorKilo = isset($request->pricekilo) ? $request->pricekilo : 'false';

             if(isset($request->img)){
               $modelproduct->img = isset($request->img) ? "storage/asset/users/" . $namefull : '';
             }
             $modelproduct->id_categoria = isset($categoria->id) ? $categoria->id : 0;
             $modelproduct->updated_at = date('Y-m-d H:m:s');
             $modelproduct->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El registro de producto fue modificado con exito';
           } else{
             $result['code'] = 202;
             $result['status'] = 'warning';
             $result['msm'] = 'La categoria seleccionada no existe, intente con otra';
           }
         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'Este producto no existe en almacen';
        }

       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al modificar el producto" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al modificar el producto';
    }
    return Response::json($result);
  }
  public function getdataall(Request $request){
    try {
      $exist = ProductoModel::whereNull('deleted_at')->count();
      if($exist > 0){
        $products = ProductoModel::join('categoria_producto','producto.id_categoria','=','categoria_producto.id')
                                 ->whereNull('producto.deleted_at')
                                 ->select('producto.id','producto.nombre','producto.descripcion','producto.precioPorKilo','producto.img','categoria_producto.nombre as categoria')
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
      $result['msm'] = 'Error al recuperar los productos';
    }
    return Response::json($result);
  }
  public function getproductbyname(Request $request){
    try {
      $exits = ProductoModel::whereNull('deleted_at')->count();
      if($exits > 0){
        $nombre = isset($request->name) ? $request->name : '';
        $producto = ProductoModel::whereNull('deleted_at')
                                  ->where('nombre','=',trim($nombre))
                                  ->select('id','nombre','img')
                                  ->first();
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $producto;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Erro al recuperar informacion del producto';
    }
    return Response::json($result);
  }
  public function deleteApi(Request $request){
    try {
      $validator = Validator::make($request->all(),
        [
          'id' => 'required'
        ]
       );
       if(!$validator->fails()){
         $exist = ProductoModel::whereNull('deleted_at')->where('id','=',$request->id)->count();
         if($exist > 0){
           $modelproduct = ProductoModel::whereNull('deleted_at')->where('id','=',$request->id)->first();
           $modelproduct->deleted_at = date('Y-m-d H:m:s');
           $modelproduct->save();

           $result['code'] = 200;
           $result['status'] = 'success';
           $result['msm'] = 'El producto fue eliminado con exito';
         } else{
           $result['code'] = 202;
           $result['status'] = 'warning';
           $result['msm'] = 'El producto a eliminar no se encuentra en la base de datos';
         }
       } else{
         $result['code'] = 400;
         $result['status'] = 'warning';
         $result['msm'] = $validator->errors();
       }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al eliminar el producto" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al eliminar el producto seleccionado';
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
