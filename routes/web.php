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
use App\Http\Controllers\Dashboardcontroller;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('auth.login');
});

//Auth::routes();
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class,'authlogin'])->name('authlogin');
Route::post('/logout', [LoginController::class,'logout'])->name('logout');
Route::post('/register', [LoginController::class,'store'])->name('store');

Route::group(['middleware' => ['auth']], function () {
  Route::get('/home', [HomeController::class, 'index'])->name('home');

  // Categorias
  Route::get('/categoria', [CategoriaController::class, 'index'])->name('categoriaindex');
  Route::get('/categoria/get/table', [CategoriaController::class, 'getCategoriaData'])->name('categoriatable');
  Route::post('/categoria/new', [CategoriaController::class, 'store'])->name('categoriastore');
  Route::get('/categoria/get/{id}', [CategoriaController::class, 'getcategoriaone'])->name('getcategoriaone');
  Route::post('/categoria/update', [CategoriaController::class, 'update'])->name('categoriaupdate');
  Route::delete('/categoria/delete/{id}', [CategoriaController::class, 'destroy'])->name('destroycategoria');
  Route::get('/categoria/producto', [CategoriaController::class, 'getcategoria'])->name('categoriaget');

  // Producto
  Route::get('/producto', [ProductoController::class, 'index'])->name('productoindex');
  Route::get('/producto/get/table', [ProductoController::class, 'getProductoData'])->name('producttable');
  Route::post('/producto/new', [ProductoController::class, 'store'])->name('productstore');
  Route::get('producto/get/{id}', [ProductoController::class, 'getproductone'])->name('getproductone');
  Route::post('/producto/update', [ProductoController::class, 'update'])->name('productupdate');
  Route::delete('/producto/delete/{id}', [ProductoController::class, 'destroy'])->name('destroyproduct');
  Route::get('/productos', [ProductoController::class,'getproducts'])->name('getproducts');
  Route::get('product/existencia/{id}',[ProductoController::class, 'existproductalmacen'])->name('existproductalmacen');

  // Cliente
  Route::get('/cliente', [ClienteController::class, 'index'])->name('clienteindex');
  Route::get('/cliente/get/table', [ClienteController::class, 'getClienteData'])->name('clientetable');
  Route::post('/cliente/new', [ClienteController::class, 'store'])->name('clientstore');
  Route::get('cliente/get/{id}', [ClienteController::class, 'getclientone'])->name('getclientone');
  Route::post('/cliente/update',[ClienteController::class, 'update'])->name('clientupdate');
  Route::delete('/cliente/delete/{id}',[ClienteController::class,'destroy'])->name('destroyclient');
  Route::get('/clientes',[ClienteController::class,'getclients'])->name('getclient');

  // Promotor
  Route::get('/promotor', [PromotorController::class, 'index'])->name('promotorindex');
  Route::get('/promotor/get/table', [PromotorController::class, 'getPromotorData'])->name('promotortable');
  Route::post('/promotor/new', [PromotorController::class, 'store'])->name('promotorstore');
  Route::get('promotor/get/{id}', [PromotorController::class, 'getpromotorone'])->name('getpromotorone');
  Route::post('/promotor/update', [PromotorController::class, 'update'])->name('promotorupdate');
  Route::delete('/promotor/delete/{id}', [PromotorController::class, 'destroy'])->name('destroypromotor');
  Route::get('/promotores', [PromotorController::class, 'getpromotores'])->name('getpromotores');

  // Usuario
  Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuarioindex');
  Route::get('/usuario/get/table', [UsuarioController::class, 'getUsuarioData'])->name('clientetable');
  Route::post('/usuario/new', [UsuarioController::class, 'store'])->name('userstore');
  Route::get('usuario/get/{id}', [UsuarioController::class, 'getuserone'])->name('getuserone');
  Route::post('/usuario/update',[UsuarioController::class, 'update'])->name('userupdate');
  Route::delete('/usuario/delete/{id}',[UsuarioController::class,'destroy'])->name('destroyuser');
  Route::post('/usuario/disponibilidad/email', [UsuarioController::class, 'userdisponiblidad'])->name('userdisponiblidad');

  // Almacen
  Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacenindex');
  Route::get('/almacen/get/table', [AlmacenController::class, 'getAlmacenData'])->name('almacentable');
  Route::get('almacen/get/{id}', [AlmacenController::class, 'getalmacenone'])->name('getalmacenone');
  Route::post('/almacen/update', [AlmacenController::class, 'update'])->name('almacenupdate');
  Route::delete('/almacen/delete/{id}', [AlmacenController::class, 'destroy'])->name('destroyalmacen');

  // Compra
  Route::get('/compra', [CompraController::class, 'index'])->name('compraindex');
  Route::get('/compra/get/table', [CompraController::class, 'getcompraData'])->name('compratable');
  Route::post('/compra/new', [CompraController::class, 'store'])->name('comprastore');
  Route::get('compra/get/{id}', [CompraController::class, 'getcompraone'])->name('getcompraone');
  Route::post('/compra/update', [CompraController::class, 'update'])->name('compraupdate');
  Route::delete('/compra/delete/{id}', [CompraController::class, 'destroy'])->name('destroycompra');

  // Venta
  Route::get('/venta', [VentaController::class, 'index'])->name('ventaindex');
  Route::get('/venta/ticket', [VentaController::class, 'generarticket'])->name('generarticket');
  Route::get('/venta/get/table', [VentaController::class, 'getventasData'])->name('ventastable');
  Route::post('/venta/new', [VentaController::class, 'store'])->name('ventastore');

  // Precio Producto
  Route::get('/precios', [PrecioProductoController::class, 'index'])->name('ventaindex');
  Route::post('/producto/precios/new', [PrecioProductoController::class, 'store'])->name('precioproductstore');
  Route::post('/producto/precios/update', [PrecioProductoController::class, 'update'])->name('precioproductupdate');
  Route::delete('/producto/precios/delete/{id}', [PrecioProductoController::class, 'destroy'])->name('precioproductdestroy');
  Route::get('producto/precio/table', [PrecioProductoController::class, 'getProductoPriceData'])->name('getProductoPriceData');
  Route::get('producto/price/missing', [PrecioProductoController::class, 'getProductPriceMissing'])->name('getProductPriceMissing');
  Route::get('producto/precio/get/{id}',[PrecioProductoController::class, 'getprecioproductbyid'])->name('getprecioproductbyid');

  Route::get('producto/price/exists',[PrecioProductoController::class, 'getProductPriceExists'])->name('getProductPriceExists');

  // Dashoboard
  Route::get('producto/inexistentes',[Dashboardcontroller::class, 'getproductinexistentes'])->name('getproductinexistentes');
  Route::post('venta/total',[Dashboardcontroller::class, 'gettotalventasmes'])->name('gettotalventasmes');
  Route::get('venta/total/mes',[Dashboardcontroller::class, 'getventasbymestotal'])->name('getventasbymestotal');
  Route::get('venta/total/mes/{id}',[Dashboardcontroller::class, 'getventasbyusertotal'])->name('getventasbyusertotal');
});
