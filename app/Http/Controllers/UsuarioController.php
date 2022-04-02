<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    protected $columnas=['id','name','email', 'type'];
    /*-----------------------------WEB-----------------------------*/
    public function index(){
      return view('usuario.index');
    }
    public function getUsuarioData(Request $request){
       try {
         $usertotal = User::whereNull('deleted_at')->where('id','!=',1)->get();
         $userColumn=$this->columnas;
         $word = explode(" ",$request->search['value']);

         $users = User::whereNull('deleted_at')->where('id','!=',1)->where(function ($query) use ($userColumn,$word) {
              foreach ($word as $word) {
                       $query = $query->where(function ($query) use ($userColumn,$word) {
                                 foreach ($userColumn as $column) {
                                    $query->orWhere($column,'like',"%$word%");
                                 }
                        });
              }
        })->whereBetween('id', [$request->start + 1, $request->start + $request->length])->get();

         $draw = isset($request->draw) ? $request->draw : 0;

         if(!empty($users)){
           $result['code'] = 200;
           $result['status'] = 'success';
           $result['draw'] = $draw;
           $result['recordsTotal']=count($users);
           $result['recordsFiltered']=count($usertotal);
           $result['data'] = $users;
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
         $result['inf'] = $e->getMessage();
       }
       return Response::json($result);
     }
    public function store(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'nameuser'=> 'required',
             'emailuser'=> 'required',
             'passworduser'=> 'required'
           ]
         );

         if(!$validator->fails()){
           if (!file_exists("storage/asset/users/")) {
             mkdir("storage/asset/users/", 0777, true);
           }

           $userexist = User::whereNull('deleted_at')->where('email','=', $request->emailuser)->count();
           if($userexist < 1){
             if(isset($request->imgnewuser)){
                $file = $request->file('imgnewuser');
                $nombre = $file->getClientOriginalName();
                $namefull = str_replace(' ', '-', $nombre);
                \Storage::disk('local')->put("asset/users/" . $namefull, \File::get($file));
              }

             $newUser = new User();
             $newUser->name = $request->nameuser;
             $newUser->email = $request->emailuser;
             $newUser->password = Hash::make($request->passworduser);
             $newUser->img = isset($request->imgnewuser) ? "storage/asset/users/" . $namefull : '';
             $newUser->type = $request->typeusernew;
             $newUser->created_at = date('Y-m-d H:m:s');
             $newUser->updated_at = date('Y-m-d H:m:s');
             $newUser->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = "Se creo el usuario con exito";
           } else{
             $result['code'] = 400;
             $result['status'] = 'warning';
             $result['msm'] = "El usuario a registrar, ya se registro anteriormente";
           }

         }else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar un usuario" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = utf8_encode($e->getMessage());
      }
      return Response::json($result);

    }
    public function getuserone($id){
      try {
        $user = User::whereNull('deleted_at')->find($id);

        $result['code'] = 200;
        $result['status'] = 'success';
        $result['data'] = $user;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion del usuario seleccionando';
      }
      return Response::json($result);

    }
    public function userdisponiblidad(Request $request){
      try {
        $correo = isset($request->email) ? trim($request->email) : '';
        $correoexist = User::whereNull('deleted_at')->where('email','=',$correo)->count();

        $result['code'] = 200;
        $resuult['status'] = 'success';
        $result['data'] = $correoexist;

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al consultar existencia del correo, pongase en contacto con el administrador';
      }
      return Response::json($result);

    }
    public function update(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'nameuseredit'=> 'required'
           ]
         );

         if(!$validator->fails()){

           if(isset($request->imguseredit)){

             $user = User::whereNull('deleted_at')->find($request->idupdateuser);

             $fileimg = explode("/",$user->img);
             $carpeta = $fileimg[count($fileimg) -2];
             $file = $fileimg[count($fileimg) - 1];

             \Storage::delete("asset/" . $carpeta . "/" . $file);

              $file = $request->file('imguseredit');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/users/" . $namefull, \File::get($file));
            }

           $userexist = User::whereNull('deleted_at')->where('email','=',$request->emailuseredit)->count();
           if($userexist > 0){
             $updateUser = User::whereNull('deleted_at')->find($request->idupdateuser);
             $updateUser->name = isset($request->nameuseredit) ? $request->nameuseredit : $updateUser->name;
             $updateUser->email = isset($request->emailuseredit) ? $request->emailuseredit : $updateUser->email;
             $updateUser->password = isset($request->passworduseredit) ? Hash::make($request->passworduseredit) : $updateUser->password;
             $updateUser->img = isset($request->imguseredit) ? "storage/asset/users/" . $namefull : $updateUser->img;
             $updateUser->type = isset($request->typeuseredit) ? $request->typeuseredit:  $updateUser->type;
             $updateUser->updated_at = date('Y-m-d H:m:s');
             $updateUser->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = "Se modifico el usuario con exito";
           } else{
             $result['code'] = 400;
             $result['status'] = 'error';
             $result['msm'] = "Usuario ya existe en la base de datos";
           }

         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        Log::error("Ocurrio un error al modificar un usuario" . "\n" . $e->getMessage());

        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al modificar el registro de usuario, intente mas tarde o pongase en contacto con el administrador';
      }
      return Response::json($result);

    }
    public function destroy($id){
      try {
        $exist = User::whereNull('deleted_at')->where('id','!=',1)->where('id','=',$id)->count();

        if($exist > 0){
          $model = User::whereNull('deleted_at')->find($id);
          $model->delete();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['msm'] = 'Registro de usuario fue eliminado con exito';
        } else{
          $result['code'] = 400;
          $result['status'] = 'error';
          $result['msm'] = 'No se encontro el usuario seleccionado';
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar el registro de usuario intente mas tarde o llame al admnistrador del sitio';
      }
      return Response::json($result);
    }
    public function loginauth(Request $request){
      try {

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
          $result['code'] = 401;
          $result['status'] = 'warning';
          $result['msm'] = 'Credenciales no validas';
        }else if(Auth::attempt($credentials)){
          $user = $request->user();

          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $user;
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al consultar usuario';
      }
      return Response::json($result);
    }

    /*-----------------------------API-----------------------------*/
    public function getdatauser(Request $request){
      try {
        $exist = User::whereNull('deleted_at')->where('id','!=',1)->count();
        $pagina = isset($request->pag) ? $request->pag : 1;
        $registerpagina = isset($request->numpag) ? $request->numpag : 20;

        if($exist > 0){

          $start = $this->paginainicio($pagina,$registerpagina);
          $end = $this->paginaend($start,$request->numpag - 1);

          $usuarios = User::whereNull('deleted_at')->where('id','!=',1)->select('id','name','email','type','img','api_token')
                          ->get();

          $arrayregistros = array();
          $arraydatosfinal = array();

          for ($i=$start; $i <= $end; $i++) {
            array_push($arrayregistros,$i);
          }

          foreach ($usuarios as $key => $u) {
            if(in_array($key,$arrayregistros)){
              array_push($arraydatosfinal,$u);
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
        $result['msm'] = 'Error al recuperar la informacion de los usuario';
      }
      return Response::json($result);
    }
    public function getdatauserone(Request $request){
      try {
        $id = isset($request->id) ? $request->id : 0;

        $exist = User::whereNull('deleted_at')->where('id','=',$id)->count();
        if($exist > 0){
          $user = User::whereNull('deleted_at')->where('id','=',$id)->select('id','name','email','img','img','api_token')->get();
          $result['code'] = 200;
          $result['status'] = 'success';
          $result['data'] = $user;
        }else{
          $result['code'] = 202;
          $result['status'] = 'warning';
          $result['data'] = array();
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al recuperar la informacion del usuario seleccionado';
      }
      return Response::json($result);
    }
    public function storeApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'name'=> 'required',
             'email'=> 'required|email',
             'password'=> 'required|same:confirmpassword',
             'confirmpassword'=> 'required',
             'typeuser'=> 'required|in: "Administrador","Vendedor","Cliente"'
           ],
           [
             'name.required'=> 'El nombre del usuario es requerido',
             'email.required'=> 'El correo del usuario es requerido',
             'email.email'=> 'El formato del correo es incorrecto',
             'password.required'=> 'La contraseña es requerido',
             'password.same'=> 'Las contraseñas no cohiciden',
             'confirmpassword.required' => 'La confirmacion de la contraseña es requerido',
             'typeuser.required' => 'El tipo de usuario es requerido',
             'typeuser.in' => 'El tipo de usuario solo permite valores de "Administrador","Vendedor","Cliente"'
           ]
         );

        if(!$validator->fails()){
           $name = isset($request->name) ? trim($request->name) : '';
           $email = isset($request->email) ? trim($request->email): '';
           $password = isset($request->password) ? trim($request->password) : '';
           $confirmpassword = isset($request->confirmpassword) ? trim($request->confirmpassword): '';
           $typeuser = isset($request->type) ? trim($request->type) : 'Cliente';

           $userexist = User::whereNull('deleted_at')->where('email','=',$email)->count();

           if($userexist > 0){
             $result['code'] = 400;
             $result['status'] = 'error';
             $result['msm'] = 'El correo del usuario ya existe en la base de datos, comunicate con uno de los administradores para recuperar tu contraseña';
           } else{

             if(isset($request->img)){
                $file = $request->file('img');
                $nombre = $file->getClientOriginalName();
                $namefull = str_replace(' ', '-', $nombre);
                \Storage::disk('local')->put("asset/users/" . $namefull, \File::get($file));
              }

             $modeluser = new User();
             $modeluser->name = isset($request->name) ? $request->name : '';
             $modeluser->email = isset($request->email) ? $request->email : '';
             $modeluser->password = isset($request->password) ?  Hash::make(trim($request->password)) : '';
             $modeluser->img = isset($request->img) ? "storage/asset/users/" . $namefull : '';
             $modeluser->type = isset($request->typeuser) ? $request->typeuser : 'Cliente';
             $modeluser->api_token = Str::random(30);
             $modeluser->created_at = date('Y-m-d H:m:s');
             $modeluser->updated_at = date('Y-m-d H:m:s');
             $modeluser->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El usuario fue registrado con exito';
             $result['data'] = User::find($modeluser->id);
           }
         }else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar un usuario" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar usuario, intente mas tarde';
      }
      return Response::json($result);
    }
    public function updateApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id'=> 'required',
             'name'=> 'required',
             'email'=> 'required|email',
             'typeuser'=> 'required|in: "Administrador","Vendedor","Cliente"'
           ],
           [
             'id.required' => 'Se requiere un id para modificar al usuario',
             'name.required'=> 'El nombre del usuario es requerido',
             'email.required'=> 'El correo del usuario es requerido',
             'email.email'=> 'El formato del correo es incorrecto',
             'typeuser.required' => 'El tipo de usuario es requerido',
             'typeuser.in' => 'El tipo de usuario solo permite valores de "Administrador","Vendedor","Cliente"'
           ]
         );

        if(!$validator->fails()){
           $name = isset($request->name) ? trim($request->name) : '';
           $email = isset($request->email) ? trim($request->email): '';
           $password = isset($request->password) ? trim($request->password) : '';
           $confirmpassword = isset($request->confirmpassword) ? trim($request->confirmpassword): '';
           $typeuser = isset($request->type) ? trim($request->type) : 'Cliente';

           if(isset($request->img)){
              $file = $request->file('img');
              $nombre = $file->getClientOriginalName();
              $namefull = str_replace(' ', '-', $nombre);
              \Storage::disk('local')->put("asset/users/" . $namefull, \File::get($file));
            }

            $userexists = User::whereNull('deleted_at')->where('id','=',$request->id)->count();

            if($userexists > 0){
              $modeluser = User::find($request->id);
              $modeluser->name = isset($request->name) ? $request->name : '';
              $modeluser->email = isset($request->email) ? $request->email : '';
              if($request->password != ''){
                $modeluser->password = isset($request->password) ?  Hash::make(trim($request->password)) : '';
              }
              if(isset($request->img)){
                $modeluser->img = isset($request->img) ? "storage/asset/users/" . $namefull : '';
              }
              $modeluser->type = isset($request->typeuser) ? $request->typeuser : 'Cliente';
              $modeluser->updated_at = date('Y-m-d H:m:s');
              $modeluser->save();

              $result['code'] = 200;
              $result['status'] = 'success';
              $result['msm'] = 'El usuario fue modificado con exito';

            } else{
              $result['code'] = 202;
              $result['status'] = 'warning';
              $result['msm'] = 'El usuario no se encontro en la base de datos';
            }
         }else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }
      } catch (\Exception $e) {
        Log::error("Ocurrio un error al insertar un usuario" . "\n" . $e->getMessage());
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al guardar usuario, intente mas tarde';
      }
      return Response::json($result);
    }
    public function destroyApi(Request $request){
      try {
        $validator = Validator::make($request->all(),
           [
             'id'=> 'required'
           ]
         );

         if(!$validator->fails()){
           $exist = User::whereNull('deleted_at')->where('id','!=',1)->where('id','=',$request->id)->count();

           if($exist > 0){
             $modeluser = User::whereNull('deleted_at')->where('id','!=',1)->where('id','=',$request->id)->first();
             $modeluser->deleted_at = date('Y-m-d H:m:s');
             $modeluser->save();

             $result['code'] = 200;
             $result['status'] = 'success';
             $result['msm'] = 'El usuario fue eliminado con exito';
           } else{
             $result['code'] = 202;
             $result['status']= 'warning';
             $result['msm'] = 'No se encontro el usuario a eliminar';
           }
         } else{
           $result['code'] = 400;
           $result['status'] = 'warning';
           $result['msm'] = $validator->errors();
         }

      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al eliminar el usuario seleccionado';
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
