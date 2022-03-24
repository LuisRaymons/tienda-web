<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\ClienteModel;
use Response;
use DB;
use Auth;

class ClienteController extends Controller
{
    protected $columnas=['id','nombre','apellidos','telefono','direccion','cp','colonia'];

    /*-----------------------------------WEB------------------------------------*/
    public function index(){
      return view('cliente.index');
    }
    public function getClienteData(Request $request){
      try {
        $clientetotal = ClienteModel::whereNull('deleted_at')->get();
        $clienteColumn=$this->columnas;
        $word = explode(" ",$request->search['value']);

        $orderBy = isset($request->order[0]['column'])==0 ? $this->columnas[$request->order[0]['column'] - 1] : 'id';
        $oder = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC';

        $cliente = ClienteModel::select('cliente.id',DB::raw("CONCAT(cliente.nombre,' ',cliente.apellidos) as nombrefull"),'cliente.telefono','cliente.direccion','cliente.cp','cliente.colonia','cliente.img')->whereNull('deleted_at')->where(function ($query) use ($clienteColumn,$word) {
             foreach ($word as $word) {
                      $query = $query->where(function ($query) use ($clienteColumn,$word) {
                                foreach ($clienteColumn as $column) {
                                   $query->orWhere($column,'like',"%$word%");
                                }
                       });
             }
       })->whereBetween('id', [$request->start + 1, $request->start + $request->length])->orderBy($orderBy, $oder)->get();

        $draw = isset($request->draw) ? $request->draw : 0;

        if(!empty($cliente)){
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['draw'] = $draw;
          $result['recordsTotal']=count($cliente);
          $result['recordsFiltered']=count($clientetotal);
          $result['data'] = $cliente;
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
        $result['data'] = $e->getMessage(); //array();
      }
      return Response::json($result);

     }
    public function store(Request $request){
      try {
          $validator = Validator::make($request->all(),
             [
               'nameclient'=> 'required',
               'lastnameclient'=> 'required',
               'phoneclient'=> 'required',
               'addressclient'=> 'required',
               'cpclient'=> 'required',
               'coloniaclient' => 'required'
             ]
           );
           if(!$validator->fails()){

             if (!file_exists("storage/asset/clientes/")) {
               mkdir("storage/asset/clientes/", 0777, true);
             }

             $existsclient = ClienteModel::where('nombre', '=', trim($request->nameclient))->where('apellidos','=',trim($request->lastnameclient))->count();

             if($existsclient < 1){
               if(isset($request->imgnewclient)){
                  $file = $request->file('imgnewclient');
                  $nombre = $file->getClientOriginalName();
                  $namefull = str_replace(' ', '-', $nombre);
                  \Storage::disk('local')->put("asset/clientes/" . $namefull, \File::get($file));
                }

                $newcliente = new ClienteModel();
                $newcliente->nombre = trim($request->nameclient);
                $newcliente->apellidos = trim($request->lastnameclient);
                $newcliente->telefono = trim($request->phoneclient);
                $newcliente->img = isset($request->imgnewclient) ? "storage/asset/clientes/" . $namefull : '';
                $newcliente->direccion = trim($request->addressclient);
                $newcliente->cp = trim($request->cpclient);
                $newcliente->colonia = trim($request->coloniacp);
                $newcliente->created_at = date('Y-m-d H:m:s');
                $newcliente->updated_at = date('Y-m-d H:m:s');
                $newcliente->save();

                $result['code'] = 200;
                $result['status'] = 'success';
                $result['msm'] = "Se agrego correctamente al cliente";
             } else{
               $result['code'] = 202;
               $result['status'] = 'warning';
               $result['msm'] = "El cliente agregado ya existe en la base de datos, buscalo con el nombre" . trim($request->nameclient) . " " . trim($request->lastnameclient);
             }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar un cliente" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = "Error al guardar el nuevo clientess";
      }
      return Response::json($result);
    }
    public function getclientone($id){
      try {
        $client = ClienteModel::where('cliente.id','=',$id)->join('codigos_postales', function($join){
          $join->on('cliente.cp', '=', 'codigos_postales.d_codigo');
          $join->on('cliente.colonia', '=', 'codigos_postales.d_asenta');
        })->select('cliente.id','cliente.nombre','cliente.apellidos','cliente.telefono','cliente.img','cliente.direccion','cliente.cp','cliente.colonia')->first();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $client;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = $e->getMessage(); //'Error al recuperar la informacion del cliente, contacte al administrador del citio';
      }
      return Response::json($result);

    }
    public function update(Request $request){
      try {

        $validator = Validator::make($request->all(),
           [
             'nameclientedit'=> 'required',
             'lastnameclientedit'=> 'required',
             'phoneclientedit'=> 'required',
             'addressclientedit'=> 'required',
             'cpclientedit'=> 'required',
             'coloniacp' => 'required'
           ]
         );
         if(!$validator->fails()){
           if(isset($request->imgeditclient)){
              $imgexist =  ClienteModel::find($request->idupdateclient);

              $fileimg = explode("/",$imgexist->img);
              $carpeta = $fileimg[count($fileimg) -2];
              $file = $fileimg[count($fileimg) - 1];

              \Storage::delete("asset/" . $carpeta . "/" . $file);

              $file = $request->file('imgeditclient');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/clientes/" . $namefull, \File::get($file));
            }

           $modelupdate = ClienteModel::find($request->idupdateclient);
           $modelupdate->nombre = isset($request->nameclientedit) ? $request->nameclientedit : '';
           $modelupdate->apellidos = isset($request->lastnameclientedit) ? $request->lastnameclientedit : '';
           $modelupdate->telefono = isset($request->phoneclientedit) ? $request->phoneclientedit : '';
           $modelupdate->img = isset($request->imgeditclient) ? "storage/asset/clientes/" . $namefull : $modelupdate->img;
           $modelupdate->direccion = isset($request->addressclientedit) ? $request->addressclientedit : '';
           $modelupdate->cp = isset($request->cpclientedit) ? $request->cpclientedit : '';
           $modelupdate->colonia = isset($request->coloniacp) ? $request->coloniacp : '';
           $modelupdate->updated_at = date('Y-m-d H:m:s');
           $modelupdate->save();

           $result['code'] = 200;
           $result['status'] = 'success';
           $result['msm'] = 'Registro de cliente actualizado con exito';
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar los cambios de cliente, intente mas tarde o pongase en contacto con el administrador del sitio';
        $result['info'] = $e->getMessage();
      }
      return Response::json($result);

    }
    public function destroy($id){
      try {
        $model = ClienteModel::where('id','=',$id)->first();
        $model->deleted_at = date('Y-m-d H:m:s');
        $model->save();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['msm'] = 'Se elimino el cliente con exito';

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar registro de cliente, intente mas tarde o llame al administrador del sitio';
      }
      return Response::json($result);

    }
    public function getclients(){
      try {
        $cliente = ClienteModel::whereNull('deleted_at')->where('id','!=',1)->select('id','nombre')->distinct()->get();

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $cliente;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al generar clientes';
      }
      return Response::json($result);

    }

    /*-----------------------------------API------------------------------------*/
    public function getdatacliente(Request $request){
      try {
        $exisit = ClienteModel::whereNull('deleted_at')->count();
        $pagina = isset($request->pag) ? $request->pag : 1;
        $registerpagina = isset($request->numpag) ? $request->numpag : 20;

        if($exisit > 0){

          $start = $this->paginainicio($pagina,$registerpagina);
          $end = $this->paginaend($start,$request->numpag - 1);

          $clientes = ClienteModel::whereNull('deleted_at')
                                  ->where('id','!=',1)
                                  ->select('id','nombre','apellidos','apellidos','telefono','img','direccion','cp','colonia')
                                  ->get();

          $arrayregistros = array();
          $arraydatosfinal = array();

          for ($i=$start; $i <= $end; $i++) {
            array_push($arrayregistros,$i);
          }
          foreach ($clientes as $key => $c) {
            if(in_array($key,$arrayregistros)){
                array_push($arraydatosfinal,$c);
            }
          }

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['total'] = $exisit;
          $result['registerpag'] = $registerpagina;
          $result['pagina'] = $pagina;
          $result['data'] = $arraydatosfinal;
        } else{
          $result['code'] = 402;
          $result['status'] = 'warning';
          $result['total'] = $exisit;
          $result['registerpag'] = $registerpagina;
          $result['pagina'] = $pagina;
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al obtener la informacion de los clientes';
      }
      return Response::json($result);

    }
    public function getdataclienteone(Request $request){
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
          $exist = ClienteModel::where('id','=',$id)->count();

          if($exist > 0){
            $model = ClienteModel::whereNull('deleted_at')->where('id','=',$id)->select('id','nombre','apellidos','telefono','apellidos','img','direccion','cp','colonia')->first();

            $result['code'] = 200;
            $result['status'] = 'success';
            $result['data'] = $model;
          } else{
            $result['code'] = 402;
            $result['status'] = 'warning';
            $result['msm'] = 'No se encontro el cliente selecionado';
          }
        } else{
          $result['code'] = 400;
          $result['status'] = 'warning';
          $result['msm'] = $validator->errors();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion del cliente seleccionado';
      }
      return Response::json($result);
    }
    public function getdataclienteall(Request $request){
      try {
        $exist = ClienteModel::whereNull('deleted_at')->count();
        if($exist > 0){
          $clientes = ClienteModel::whereNull('deleted_at')
                                  ->select('id','nombre','apellidos','telefono','img','direccion','cp','colonia')
                                  ->where('id','!=',1)
                                  ->get();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $clientes;
        } else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar los clientes';
      }
      return Response::json($result);

    }
    public function storeApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
          [
            'name'=> 'required',
            'lastname'=> 'required',
            'phone'=> 'required',
            'address'=> 'required',
            'cp'=> 'required',
            'colonia' => 'required'
          ]
        );
        if(!$validator->fails()){

           $exists = ClienteModel::where('nombre','=',trim($request->name))->where('apellidos','=',trim($request->lastname))->count();


           if($exists < 1){
             // crear la carpeta de almacen
            if (!file_exists("storage/asset/cliente/")) {
                mkdir("storage/asset/cliente/", 0777, true);
            }
            if(isset($request->img)){
              $file = $request->file('img');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/cliente/" . $namefull,  \File::get($file));
            }

             $modelcliente = new ClienteModel();
             $modelcliente->nombre = isset($request->name) ? $request->name : '';
             $modelcliente->apellidos = isset($request->lastname) ? $request->lastname : '';
             $modelcliente->telefono = isset($request->phone) ? $request->phone : '';
             $modelcliente->img = isset($request->img) ? "storage/asset/almacen/" . $namefull : '';
             $modelcliente->direccion = isset($request->address) ? $request->address : '';
             $modelcliente->cp = isset($request->cp) ? $request->cp : '';
             $modelcliente->colonia = isset($request->colonia) ? $request->colonia : '';
             $modelcliente->created_at = date('Y-m-d H:m:s');
             $modelcliente->updated_at = date('Y-m-d H:m:s');
             $modelcliente->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El cliente fue guardado con exito';
           } else{
             $result['code'] = 400;
             $result['status'] = 'warning';
             $result['msm'] = '¡El cliente ya exisite!';
           }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar un cliente" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al insertar el cliente';
      }
      return Response::json($result);
    }
    public function updateApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
             [
               'name'=> 'required',
               'lastname'=> 'required',
               'phone'=> 'required',
               'address'=> 'required',
               'cp'=> 'required',
               'colonia' => 'required'
             ]
           );
         if(!$validator->fails()){
           $exists = ClienteModel::where('id','=',trim($request->id))->count();

           if($exists > 0){
             // crear la carpeta de almacen
            if (!file_exists("storage/asset/cliente/")) {
                mkdir("storage/asset/cliente/", 0777, true);
            }
            if(isset($request->img)){
              $file = $request->file('img');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/cliente/" . $namefull,  \File::get($file));
            }

             $modelcliente = ClienteModel::find($request->id);
             $modelcliente->nombre = isset($request->name) ? $request->name : '';
             $modelcliente->apellidos = isset($request->lastname) ? $request->lastname : '';
             $modelcliente->telefono = isset($request->phone) ? $request->phone : '';

             if(isset($request->img)){
               $modelcliente->img = isset($request->img) ? "storage/asset/almacen/" . $namefull : '';
             }

             $modelcliente->direccion = isset($request->address) ? $request->address : '';
             $modelcliente->cp = isset($request->cp) ? $request->cp : '';
             $modelcliente->colonia = isset($request->colonia) ? $request->colonia : '';
             $modelcliente->updated_at = date('Y-m-d H:m:s');
             $modelcliente->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El cliente fue guardado con exito';
           } else{
             $result['code'] = 400;
             $result['status'] = 'warning';
             $result['msm'] = '¡El cliente no exisite!';
           }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al modificar un cliente" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al modiificar el cliente';
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

              $exist = ClienteModel::whereNull('deleted_at')->where('id','=',$request->id)->where('id','!=',1)->count();

              if($exist > 0){
                $modelcliente = ClienteModel::whereNull('deleted_at')->where('id','=',$request->id)->where('id','!=',1)->first();
                $modelcliente->deleted_at = date('Y-m-d');
                $modelcliente->save();

                $result['code'] = 200;
                $result['status'] = 'success';
                $result['msm'] = 'El cliente fue eliminado con exito';
              } else{
                $result['code'] = 202;
                $result['status'] = 'warning';
                $result['msm'] = 'No se encontro el cliente seleccionado';
              }
            } else{
              $result['code'] = 400;
              $result['status'] = 'warning';
              $result['msm'] = $validator->errors();
            }

        } catch (\Exception $e) {
          Log::error("Ocurrio un error al eliminar el cliente" . "\n" . $e->getMessage());
          $result['code'] = 500;
          $result['status'] = 'error';
          $result['msm'] = 'Error al intentar eliminar el cliente seleccionado';
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
