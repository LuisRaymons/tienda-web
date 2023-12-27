<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VentaModel;
use App\Models\VentaDetailModel;
use App\Models\PrecioProductoModel;
use App\Models\AlmacenModel;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Response;

class VentaController extends Controller
{
  protected $columnas=['factura','impuesto','precio_total','id_pago','id_cliente','id_users'];
  /*------------------------------------WEB-----------------------------------*/
  public function index(){
    return view('venta.index');
  }
  public function getventasData(Request $request){
      try {
        $ventaColumn=$this->columnas;
				$word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'venta.id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $venta = VentaModel::whereNull('venta.deleted_at')->where(function ($query) use ($ventaColumn,$word) {
                    				      foreach ($word as $word) {
                    							         $query = $query->where(function ($query) use ($ventaColumn,$word) {
                    								                 foreach ($ventaColumn as $column) {
                    															      $query->orWhere("venta." . $column,'like',"%$word%");
                    															   }
                    												});
                    							}
                    				 })
                             ->where('id_users','=',$request->iduser)
                             /*->whereBetween('id', [$request->start + 1, $request->start + $request->length])*/
                             ->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        $arraydatos = array();
        $registrosget  = array();

        $start = $request->start; //$request->start;
        $end = $request->start + $request->length; //$request->length;

        // registros a obtener
        for ($i=$start; $i < $end; $i++) {
          array_push($registrosget,intval($i));
        }

        foreach ($venta as $key => $v) {
          if(in_array($key,$registrosget)){
            array_push($arraydatos,$v);
          }
        }

        if(!empty($arraydatos)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']= count($venta);
          $result['recordsFiltered']= count($venta);//count($arraydatos);
          $result['data'] = $arraydatos;
        } else{
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = 0;
          $result['recordsTotal'] = 0;
          $result['recordsFiltered'] = 0;
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
      $productos = isset($request->datosventa) ? json_decode($request->datosventa, true) : '';

      if($productos != ''){

        $ventaultimoregister = VentaModel::orderBy('id', 'DESC')->first();
        $id = isset($ventaultimoregister->id ) ? $ventaultimoregister->id  : 1;
        $factura = $this->generarCodigo(10,$id,date('dmY'));

        // generar la venta  $request->datosventa
        $ventamodel = new VentaModel();
        $ventamodel->factura = $factura;
        $ventamodel->impuesto = 0.16;
        $ventamodel->precio_total = isset($request->pagototal) ? $request->pagototal : 0.00;
        $ventamodel->id_pago = isset($request->idpago) ? $request->idpago : 1;
        $ventamodel->id_cliente = isset($request->idclient) ? $request->idclient : 1;
        $ventamodel->id_users = isset($request->iduser) ? $request->iduser : 1;
        $ventamodel->created_at = date('Y-m-d H:m:s');
        $ventamodel->updated_at = date('Y-m-d H:m:s');
        $ventamodel->save();

        if(isset($ventamodel)){
          $dventafor = json_decode($request->datosventa,false);

          foreach ($dventafor as $dventa) {
            $priceproductone = PrecioProductoModel::where('id_product','=',$dventa->id)->first();

            $dventamodel = new VentaDetailModel();
            $dventamodel->id_venta = $ventamodel->id;
            $dventamodel->id_producto = isset($dventa->id) ? $dventa->id : 1;
            $dventamodel->cantidad = isset($dventa->acount) ? $dventa->acount : 1;
            $dventamodel->precio = isset($priceproductone->precio) ? $priceproductone->precio : 0.00;
            $dventamodel->created_at = date('Y-m-d H:m:s');
            $dventamodel->updated_at = date('Y-m-d H:m:s');
            $dventamodel->save();

            // quitar productos de almacen
            $almacenproduct = AlmacenModel::where('id_producto','=',$dventa->id)->first();

            $cantidadstock = intval($almacenproduct->stock) - intval($dventa->acount);
            $cantidadsalida = intval($almacenproduct->salida) + intval($dventa->acount);

            AlmacenModel::where('id_producto','=',$dventa->id)->update([
              'stock' => $cantidadstock,
              'salida' => $cantidadsalida,
              'updated_at' => date('Y-m-d H:m:s')
            ]);
          }
        }

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = 'La compra fue todo un exito';
      } else{
        $result['code'] = 400;
        $result['status'] = 'error';
        $result['msm'] = 'No se encontraron productos en el carrito';
      }

    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar una venta" . "\n" . $e->getMessage());
      $result['code'] = 500;
      $resul['status'] = 'error';
      $result['msm'] = 'Error al generar la venta, intente mas tarde o llame al administrador del sitio web';
    }
    return Response::json($result);
  }
  public function generarticket(){
    $connector = new FilePrintConnector("php://stdout");
    $printer = new Printer($connector);
    try {
        $tux = EscposImage::load("https://cdn.goconqr.com/uploads/media/image/10292625/desktop_03d49627-c715-4fd6-92ac-f70192d1fa7d.png", false);
        $printer->text("These example images are printed with the older\nbit image print command. You should only use\n\$p -> bitImage() if \$p -> graphics() does not\nwork on your printer.\n\n");
        $printer->bitImage($tux);
        $printer->text("Regular Tux (bit image).\n");
        $printer->feed();
        $printer->bitImage($tux, Printer::IMG_DOUBLE_WIDTH);
        $printer->text("Wide Tux (bit image).\n");
        $printer->feed();
        $printer->bitImage($tux, Printer::IMG_DOUBLE_HEIGHT);
        $printer->text("Tall Tux (bit image).\n");
        $printer->feed();
        $printer->bitImage($tux, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
        $printer->text("Large Tux in correct proportion (bit image).\n");
    } catch (Exception $e) {
        /* Images not supported on your PHP, or image file not found */
        $printer->text($e->getMessage() . "\n");
    }
    $printer->cut();
    $printer->close();


  }

  /*------------------------------------API-----------------------------------*/
  public function getdataventa(Request $request){
    try {
      $exist = VentaModel::whereNull('deleted_at')->count();
      $pagina = isset($request->pag) ? $request->pag : 1;
      $registerpagina = isset($request->numpag) ? $request->numpag : 20;

      if($exist > 0){

        $start = $this->paginainicio($pagina,$registerpagina);
        $end = $this->paginaend($start,$request->numpag - 1);

        $ventas = VentaModel::leftjoin('typepay','venta.id_pago','=','typepay.id')
                            ->leftjoin('cliente','venta.id_cliente','=','cliente.id')
                            ->leftjoin('users','venta.id_users','=','users.id')
                            ->whereNull('venta.deleted_at')
                            ->select('venta.id','venta.factura','venta.precio_total','typepay.name as tipopago','cliente.nombre as cliente','users.name as usuario')
                            ->get();

        $arrayregistros = array();
        $arraydatosfinal = array();

        for ($i=$start; $i <= $end; $i++) {
          array_push($arrayregistros,$i);
        }

        foreach ($ventas as $key => $v) {
          if(in_array($key,$arrayregistros)){
            array_push($arraydatosfinal,$v);
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
      $result['msm'] = 'Error al recuperar informacion de las ventas';
    }
    return Response::json($result);
  }
  public function getdataventaone(Request $request){
    try {
      $id = isset($request->id) ? $request->id : 0;
      $exists = VentaModel::whereNull('deleted_at')->where('id','=',$id)->count();

      if($exists > 0){
        $venta = VentaModel::whereNull('deleted_at')
                           ->where('id','=',$id)
                           ->select('id','factura','impuesto','precio_total','id_pago','id_cliente','id_users')
                           ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $venta;
      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }
    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar informacion de la venta seleccionada';
    }
    return Response::json($result);
  }
  public function storeApi(Request $request){
    try {
      $productos = isset($request->datosventa) ? json_decode($request->datosventa, false) : array();

      if(count($productos) > 0){
        $ventaultimoregister = VentaModel::orderBy('id', 'DESC')->first();
        $id = isset($ventaultimoregister->id ) ? $ventaultimoregister->id  : 1;
        $factura = $this->generarCodigo(10,$id,date('dmY'));

        // generar la venta  $request->datosventa
        $ventamodel = new VentaModel();
        $ventamodel->factura = $factura;
        $ventamodel->impuesto = 0.16;
        $ventamodel->precio_total = isset($request->totalventa) ? $request->totalventa : 0.00;
        $ventamodel->id_pago = isset($request->pago) ? $request->pago : 1;
        $ventamodel->id_cliente = isset($request->cliente) ? $request->cliente : 1;
        $ventamodel->id_users = isset($request->user) ? $request->user : 1;
        $ventamodel->created_at = date('Y-m-d H:m:s');
        $ventamodel->updated_at = date('Y-m-d H:m:s');
        $ventamodel->save();

        if(isset($ventamodel)){
          foreach ($productos as $producto) {
            $dventamodel = new VentaDetailModel();
            $dventamodel->id_venta = $ventamodel->id;
            $dventamodel->id_producto = isset($producto->id) ? $producto->id : 1;
            $dventamodel->cantidad = isset($producto->cantidad) ? $producto->cantidad : 1;
            $dventamodel->precio = isset($producto->precio) ? $producto->precio : 0.00;
            $dventamodel->created_at = date('Y-m-d H:m:s');
            $dventamodel->updated_at = date('Y-m-d H:m:s');
            $dventamodel->save();

            // quitar producuto de almacen
            $almacenproduct = AlmacenModel::where('id_producto','=',$producto->id)->first();

            $cantidadstock = intval($almacenproduct->stock) - intval($producto->cantidad);
            $cantidadsalida = intval($almacenproduct->salida) + intval($producto->cantidad);

            AlmacenModel::where('id_producto','=',$producto->id)->update([
              'stock' => $cantidadstock,
              'salida' => $cantidadsalida,
              'updated_at' => date('Y-m-d H:m:s')
            ]);
          }
        }
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['msm'] = 'La venta fue todo un exito';
        //$result['request'] = $request->all();
      } else{
        $result['code'] = 400;
        $result['status'] = 'error';
        $result['msm'] = 'No se encontraron productos en el carrito';
      }
    } catch (\Exception $e) {
      Log::error("Ocurrio un error al insertar una venta" . "\n" . $e->getMessage() . "\n" . json_encode($request->datosventa));
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al guardar la venta';
    }
    return Response::json($result);
  }
  public function getdataventaall(Request $request){
    try {
      $exists = VentaModel::whereNull('deleted_at')->count();

      if($exists > 0){
        $ventas = VentaModel::leftjoin('typepay','venta.id_pago','=','typepay.id')
                            ->leftjoin('cliente','venta.id_cliente','=','cliente.id')
                            ->leftjoin('users','venta.id_users','=','users.id')
                            ->select('venta.id','venta.factura','venta.precio_total','typepay.name as pago','cliente.nombre as cliente','users.name as usuario')
                            ->whereNull('venta.deleted_at')
                            ->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $ventas;

      } else{
        $result['code'] = 202;
        $result['status'] = 'warning';
        $result['data'] = array();
      }

    } catch (\Exception $e) {
      $result['code'] = 500;
      $result['status'] = 'error';
      $result['msm'] = 'Error al recuperar la informacion de las ventas';
    }
    return $result;
  }

  public function generarCodigo($longitud, $codigoventa=0, $hoy){
      $codigo = "";
      $caracteres="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $max=strlen($caracteres)-1;
      for($i=0;$i < $longitud;$i++)
      {
          $codigo.=$caracteres[rand(0,$max)];
      }
      return "Factura-". $hoy . $codigo . "" . $codigoventa;
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
