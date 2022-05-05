<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CodigoPostalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PrecioProductoController;
use App\Http\Controllers\PromotorController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaDetailController;
use App\Http\Controllers\Dashboardcontroller;
use App\Http\Controllers\Auth\LoginController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [UsuarioController::class,'loginauth'])->name('loginauth');
Route::group(['middleware' => ['authapi']], function () {
  Route::prefix('almacen')->group(function(){
    Route::post('/get', [AlmacenController::class, 'getdataalmacen'])->name('getdataalamacen');
    Route::post('/get/one', [AlmacenController::class, 'getonealmacen'])->name('getdataalamacenbyid');
    Route::post('/get/all', [AlmacenController::class, 'getalmacenall'])->name('getdataalamacenall');
    Route::post('/update', [AlmacenController::class, 'updateApi'])->name('updateApi');
  });
  Route::prefix('categoria/producto')->group(function(){
    Route::post('/get',[CategoriaController::class, 'getdatacategoriaproducto'])->name('getcategorias');
    Route::post('/get/one',[CategoriaController::class, 'getdatacategoriaproductoone'])->name('getcategoriasone');
    Route::post('/get/all',[CategoriaController::class, 'getcategoriaApi'])->name('getcategoriasall');
    Route::post('/add',[CategoriaController::class, 'storeApi'])->name('storecategria');
    Route::post('/update',[CategoriaController::class, 'updateApi'])->name('updatecategoria');
    Route::post('/delete',[CategoriaController::class, 'destroyApi'])->name('destroycategoria');
  });
  Route::prefix('cliente')->group(function(){
    Route::post('/get',[ClienteController::class, 'getdatacliente'])->name('getclientes');
    Route::post('/get/one',[ClienteController::class, 'getdataclienteone'])->name('getclienteone');
    Route::post('/get/all',[ClienteController::class, 'getdataclienteall'])->name('getclienteall');
    Route::post('/add',[ClienteController::class, 'storeApi'])->name('storeAPiclient');
    Route::post('/update',[ClienteController::class, 'updateApi'])->name('updateApiclient');
    Route::post('/delete',[ClienteController::class, 'destroyApi'])->name('deleteApiclient');
  });
  Route::prefix('usuario')->group(function(){
    Route::post('/get',[UsuarioController::class, 'getdatauser'])->name('getusers');
    Route::post('/get/one',[UsuarioController::class, 'getdatauserone'])->name('getuserone');
    Route::post('/get/all',[UsuarioController::class, 'getdatauserall'])->name('getusersall');
    Route::post('/add',[UsuarioController::class, 'storeApi'])->name('storeApi');
    Route::post('/update',[UsuarioController::class, 'updateApi'])->name('updateApi');
    Route::post('/delete',[UsuarioController::class, 'destroyApi'])->name('destroyApi');
  });
  Route::prefix('producto')->group(function(){
    Route::post('/get',[ProductoController::class, 'getdataproduct'])->name('getproducts');
    Route::post('/get/one',[ProductoController::class, 'getdataproductone'])->name('getproductone');
    Route::post('/get/name',[ProductoController::class, 'getproductbyname'])->name('getproductbyname');
    Route::post('/get/all',[ProductoController::class, 'getdataall'])->name('getproductsall');
    Route::post('/add',[ProductoController::class, 'storeApi'])->name('addproduct');
    Route::post('/update',[ProductoController::class, 'updateApi'])->name('updateApi');
    Route::post('/delete',[ProductoController::class, 'deleteApi'])->name('deleteApi');
    Route::post('/inexistentes',[Dashboardcontroller::class, 'getproductinexistentes'])->name('getproductinexistentes');
  });
  Route::prefix('producto/precio')->group(function(){
    Route::post('/get',[PrecioProductoController::class, 'getprodutpreciotable'])->name('getprodutpreciotable');
    Route::post('/get/one',[PrecioProductoController::class, 'getprodutprecio'])->name('getproductprecio');
    Route::post('/get/all',[PrecioProductoController::class, 'getprodutprecioall'])->name('getproductprecioall');
    Route::post('missing', [PrecioProductoController::class, 'getProductPriceMissing'])->name('getProductPriceMissing');
    Route::post('/add', [PrecioProductoController::class, 'storeApi'])->name('storeApi');
    Route::post('/update', [PrecioProductoController::class, 'updateApi'])->name('updateApi');
    Route::post('/delete', [PrecioProductoController::class, 'destroyApi'])->name('destroyApi');
  });
  Route::prefix('promotor')->group(function(){
    Route::post('/get',[PromotorController::class, 'getdatapromotor'])->name('getpromotors');
    Route::post('/get/one',[PromotorController::class, 'getdatapromotorone'])->name('getpromotorone');
    Route::post('/get/all',[PromotorController::class, 'getdatapromotorall'])->name('getpromotorall');
    Route::post('/add',[PromotorController::class, 'storeApi'])->name('storePromotor');
    Route::post('/update',[PromotorController::class, 'updateApi'])->name('updateApi');
    Route::post('/delete',[PromotorController::class, 'destroyApi'])->name('destroyApi');
  });
  Route::prefix('compra')->group(function(){
    Route::post('/get',[CompraController::class, 'getdatacompra'])->name('getcompras');
    Route::post('/get/one',[CompraController::class, 'getdatacompraone'])->name('getcompraone');
    Route::post('/get/all',[CompraController::class, 'getdatacompraall'])->name('getcompraall');
    Route::post('/add',[CompraController::class, 'storeApi'])->name('storeApi');
    Route::post('/update',[CompraController::class, 'updateApi'])->name('updateApi');
    Route::post('/delete',[CompraController::class, 'destroyApi'])->name('destroyApi');
  });
  Route::prefix('venta')->group(function(){
    Route::post('/get',[VentaController::class, 'getdataventa'])->name('getventas');
    Route::post('/get/one',[VentaController::class, 'getdataventaone'])->name('getventasone');
    Route::post('/get/all',[VentaController::class, 'getdataventaall'])->name('getventasall');
    Route::post('/add',[VentaController::class, 'storeApi'])->name('storeApi');

    Route::post('/total/mes',[Dashboardcontroller::class, 'getventasbymestotalApi'])->name('getventasbymestotalApi');
    Route::post('/total/mes/{id}',[Dashboardcontroller::class, 'getventasbyusertotalApi'])->name('getventasbyusertotalApi');
  });

  Route::prefix('/venta/detalle')->group(function(){
    Route::post('/one', [VentaDetailController::class, 'detailventabyidApi'])->name('detailventabyidApi');
  });

});

// crear un middleware en para tomar el token
Route::post('/codigo/postal', [CodigoPostalController::class, 'getcodigopostal'])->name('cp');
Route::get('/get/pago', [CodigoPostalController::class, 'getPagos'])->name('pago');
