<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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

Route::get('/', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

//Auth::routes();

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class,'authlogin'])->name('authlogin');
Route::get('/logout', [LoginController::class,'logout'])->name('logout');
Route::post('/register', [LoginController::class,'store'])->name('store');

Route::group(['middleware' => ['auth']], function () {
  Route::get('/home', [HomeController::class, 'index'])->name('home');

  // Categorias
  Route::prefix('categoria')->group(function(){
    Route::get('/', [CategoriaController::class, 'index'])->name('categoriaindex');
    Route::get('/get/table', [CategoriaController::class, 'getCategoriaData'])->name('categoriatable');
    Route::post('/new', [CategoriaController::class, 'store'])->name('categoriastore');
    Route::get('/get/{id}', [CategoriaController::class, 'getcategoriaone'])->name('getcategoriaone');
    Route::post('/update', [CategoriaController::class, 'update'])->name('categoriaupdate');
    Route::delete('/delete/{id}', [CategoriaController::class, 'destroy'])->name('destroycategoria');
    Route::get('/producto', [CategoriaController::class, 'getcategoria'])->name('categoriaget');
  });
  // Producto
  Route::prefix('producto')->group(function(){
    Route::get('/', [ProductoController::class, 'index'])->name('productoindex');
    Route::get('/get/table', [ProductoController::class, 'getProductoData'])->name('producttable');
    Route::post('/new', [ProductoController::class, 'store'])->name('productstore');
    Route::get('/get/{id}', [ProductoController::class, 'getproductone'])->name('getproductone');
    Route::post('/update', [ProductoController::class, 'update'])->name('productupdate');
    Route::delete('/delete/{id}', [ProductoController::class, 'destroy'])->name('destroyproduct');
    Route::get('/all', [ProductoController::class,'getproducts'])->name('getproducts');
    Route::post('/existencia',[ProductoController::class, 'existproductalmacen'])->name('existproductalmacen');
  });
  // Cliente
  Route::prefix('cliente')->group(function(){
    Route::get('/', [ClienteController::class, 'index'])->name('clienteindex');
    Route::get('/get/table', [ClienteController::class, 'getClienteData'])->name('clientetable');
    Route::post('/new', [ClienteController::class, 'store'])->name('clientstore');
    Route::get('/get/{id}', [ClienteController::class, 'getclientone'])->name('getclientone');
    Route::post('/update',[ClienteController::class, 'update'])->name('clientupdate');
    Route::delete('/delete/{id}',[ClienteController::class,'destroy'])->name('destroyclient');
    Route::get('/all',[ClienteController::class,'getclients'])->name('getclient');
  });
  // Promotor
  Route::prefix('promotor')->group(function(){
    Route::get('/', [PromotorController::class, 'index'])->name('promotorindex');
    Route::get('/get/table', [PromotorController::class, 'getPromotorData'])->name('promotortable');
    Route::post('/new', [PromotorController::class, 'store'])->name('promotorstore');
    Route::get('/get/{id}', [PromotorController::class, 'getpromotorone'])->name('getpromotorone');
    Route::post('/update', [PromotorController::class, 'update'])->name('promotorupdate');
    Route::delete('/delete/{id}', [PromotorController::class, 'destroy'])->name('destroypromotor');
    Route::get('/all', [PromotorController::class, 'getpromotores'])->name('getpromotores');
  });
  // Usuario
  Route::prefix('usuario')->group(function(){
    Route::get('/', [UsuarioController::class, 'index'])->name('usuarioindex');
    Route::get('/get/table', [UsuarioController::class, 'getUsuarioData'])->name('clientetable');
    Route::post('/new', [UsuarioController::class, 'store'])->name('userstore');
    Route::get('/get/{id}', [UsuarioController::class, 'getuserone'])->name('getuserone');
    Route::post('/update',[UsuarioController::class, 'update'])->name('userupdate');
    Route::delete('/delete/{id}',[UsuarioController::class,'destroy'])->name('destroyuser');
    Route::post('/disponibilidad/email', [UsuarioController::class, 'userdisponiblidad'])->name('userdisponiblidad');
  });
  // Almacen
  Route::prefix('almacen')->group(function(){
    Route::get('/', [AlmacenController::class, 'index'])->name('almacenindex');
    Route::get('/get/table', [AlmacenController::class, 'getAlmacenData'])->name('almacentable');
    Route::get('/get/{id}', [AlmacenController::class, 'getalmacenone'])->name('getalmacenone');
    Route::post('/update', [AlmacenController::class, 'update'])->name('almacenupdate');
    Route::delete('/delete/{id}', [AlmacenController::class, 'destroy'])->name('destroyalmacen');
  });
  // Compra
  Route::prefix('compra')->group(function(){
    Route::get('/', [CompraController::class, 'index'])->name('compraindex');
    Route::get('/get/table', [CompraController::class, 'getcompraData'])->name('compratable');
    Route::post('/new', [CompraController::class, 'store'])->name('comprastore');
    Route::get('/get/{id}', [CompraController::class, 'getcompraone'])->name('getcompraone');
    Route::post('/update', [CompraController::class, 'update'])->name('compraupdate');
    Route::delete('/delete/{id}', [CompraController::class, 'destroy'])->name('destroycompra');
  });
  // Venta
  Route::prefix('venta')->group(function(){
    Route::get('/', [VentaController::class, 'index'])->name('ventaindex');
    Route::get('/ticket', [VentaController::class, 'generarticket'])->name('generarticket');
    Route::get('/get/table', [VentaController::class, 'getventasData'])->name('ventastable');
    Route::post('/new', [VentaController::class, 'store'])->name('ventastore');

    // Detalle de venta
    Route::get('/detalle/{id}', [VentaDetailController::class, 'detailventabyid'])->name('detailventabyid');

    // Dashoboard
    Route::post('/total',[Dashboardcontroller::class, 'gettotalventasmes'])->name('gettotalventasmes');
    Route::get('/total/mes',[Dashboardcontroller::class, 'getventasbymestotal'])->name('getventasbymestotal');
    Route::get('/total/mes/{id}',[Dashboardcontroller::class, 'getventasbyusertotal'])->name('getventasbyusertotal');
  });
  // Precio Producto
  Route::prefix('productoprecios')->group(function(){
    Route::get('/', [PrecioProductoController::class, 'index'])->name('ventaindex');

    Route::post('/new', [PrecioProductoController::class, 'store'])->name('precioproductstore');
    Route::post('/update', [PrecioProductoController::class, 'update'])->name('precioproductupdate');
    Route::delete('/delete/{id}', [PrecioProductoController::class, 'destroy'])->name('precioproductdestroy');
    Route::get('/table', [PrecioProductoController::class, 'getProductoPriceData'])->name('getProductoPriceData');
    Route::get('/missing', [PrecioProductoController::class, 'getProductPriceMissing'])->name('getProductPriceMissing');
    Route::post('/get/one',[PrecioProductoController::class, 'getprecioproductbyid'])->name('getprecioproductbyid');
    Route::get('/exists',[PrecioProductoController::class, 'getProductPriceExists'])->name('getProductPriceExists');


  });
  // Dashoboard
  Route::get('producto/inexistentes',[Dashboardcontroller::class, 'getproductinexistentes'])->name('getproductinexistentes');

});
