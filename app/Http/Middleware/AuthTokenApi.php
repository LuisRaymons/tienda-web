<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      //print_r($request->all());
      $token = isset($request->api_token) ? trim($request->api_token) : '';
      $userexist = User::where('api_token','=', $token)->count();

      if($userexist > 0){
        return $next($request);
      } else{
        return response()->json(['code' =>419, 'status' => 'warning', 'msg' => 'No autorizado']);
      }

    }
}
