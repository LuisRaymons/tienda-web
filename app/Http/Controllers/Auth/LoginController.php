<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;
use Response;

class LoginController extends Controller{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function authlogin(Request $request){
      try {
        $userexist = User::where('email','=',$request->email)->whereIn('type',['Administrador','Vendedor'])->count();

        if($userexist > 0){
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
        } else{
          $result['code'] = 401;
          $result['status'] = 'warning';
          $result['msm'] = 'Credenciales no validas';
        }
      } catch (\Exception $e) {
        $result['code'] = 500;
        $result['status'] = 'error';
        $result['msm'] = 'Error al login';
        $result['info'] = $e->getMessage();
      }
      return Response::json($result);
    }
    public function logout(Request $request){

      try {
        Auth::logout();
        $result['code'] = 200;
        $result['status'] = 'success';
        $result['msm'] = 'Cerraste la session con exito';
        //return redirect('login');

      } catch (\Exception $e) {
        $resul['code'] = 500;
        $result['status'] = 'error';
        $resul['msm'] = 'Error al cerrar session intente mas tarde';
      }
      return Response::json($result);
    }
    public function store(Request $request){
       try {
         $existuser = User::where('email','=',isset($request->emailregister) ? $request->emailregister : '')->count();

         if($existuser < 1){

           $usermodel = new User();
           $usermodel->name = isset($request->nameregister) ? $request->nameregister : '';
           $usermodel->email = isset($request->emailregister) ? $request->emailregister : '';
           $usermodel->password = isset($request->passwordregister) ? Hash::make($request->passwordregister) : '';
           $usermodel->img = 'https://www.pinclipart.com/picdir/middle/165-1653686_female-user-icon-png-download-user-colorful-icon.png';
           $usermodel->type = 'Cliente';
           $usermodel->created_at = date('Y-m-d H:m:s');
           $usermodel->updated_at = date('Y-m-d H:m:s');
           $usermodel->save();

           $result['code'] = 200;
           $result['status'] = 'success';
           $result['msm'] = 'El registro de usuario, se registro con exito';

         } else{
           $result['code'] = 400;
           $result['status'] = 'error';
           $result['msm'] = 'El usuario ya esta registrado, recupera tu contraseña con el administrador del sitio';
         }

       } catch (\Exception $e) {
         $result['code'] = 500;
         $result['status'] = 'error';
         $result['msm'] = 'Error al registrar el usuario, intente mas tarde o llame a su administrador';
       }
       return Response::json($result);

     }
}
