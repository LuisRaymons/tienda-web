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
  // vistas generales
  Route::get('/categoria', [CategoriaController::class, 'index'])->name('categoriaindex');
  Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacenindex');
  Route::get('/cliente', [ClienteController::class, 'index'])->name('clienteindex');
  Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuarioindex');
  Route::get('/producto', [ProductoController::class, 'index'])->name('productoindex');
  Route::get('/promotor', [PromotorController::class, 'index'])->name('promotorindex');
  Route::get('/compra', [CompraController::class, 'index'])->name('compraindex');
  Route::get('/venta', [VentaController::class, 'index'])->name('ventaindex');

  Route::get('/venta/ticket', [VentaController::class, 'generarticket'])->name('generarticket');

  // Tablas
  Route::get('/categoria/get/table', [CategoriaController::class, 'getCategoriaData'])->name('categoriatable');
  Route::get('/almacen/get/table', [AlmacenController::class, 'getAlmacenData'])->name('almacentable');
  Route::get('/cliente/get/table', [ClienteController::class, 'getClienteData'])->name('clientetable');
  Route::get('/usuario/get/table', [UsuarioController::class, 'getUsuarioData'])->name('clientetable');
  Route::get('/producto/get/table', [ProductoController::class, 'getProductoData'])->name('producttable');
  Route::get('/promotor/get/table', [PromotorController::class, 'getPromotorData'])->name('promotortable');
  Route::get('/compra/get/table', [CompraController::class, 'getcompraData'])->name('compratable');
  Route::get('/venta/get/table', [VentaController::class, 'getventasData'])->name('ventastable');

  // creacion de registros
  Route::post('/categoria/new', [CategoriaController::class, 'store'])->name('categoriastore');
  Route::post('/cliente/new', [ClienteController::class, 'store'])->name('clientstore');
  Route::post('/usuario/new', [UsuarioController::class, 'store'])->name('userstore');
  Route::post('/producto/new', [ProductoController::class, 'store'])->name('productstore');
  Route::post('/promotor/new', [PromotorController::class, 'store'])->name('promotorstore');
  Route::post('/compra/new', [CompraController::class, 'store'])->name('comprastore');
  Route::post('/venta/new', [VentaController::class, 'store'])->name('ventastore');

  // obtener registro
  Route::get('/categoria/get/{id}', [CategoriaController::class, 'getcategoriaone'])->name('getcategoriaone');
  Route::get('cliente/get/{id}', [ClienteController::class, 'getclientone'])->name('getclientone');
  Route::get('usuario/get/{id}', [UsuarioController::class, 'getuserone'])->name('getuserone');
  Route::get('producto/get/{id}', [ProductoController::class, 'getproductone'])->name('getproductone');
  Route::get('almacen/get/{id}', [AlmacenController::class, 'getalmacenone'])->name('getalmacenone');
  Route::get('promotor/get/{id}', [PromotorController::class, 'getpromotorone'])->name('getpromotorone');
  Route::get('compra/get/{id}', [CompraController::class, 'getcompraone'])->name('getcompraone');

  //Modificar registro
  Route::post('/categoria/update', [CategoriaController::class, 'update'])->name('categoriaupdate');
  Route::post('/cliente/update',[ClienteController::class, 'update'])->name('clientupdate');
  Route::post('/usuario/update',[UsuarioController::class, 'update'])->name('userupdate');
  Route::post('/producto/update', [ProductoController::class, 'update'])->name('productupdate');
  Route::post('/almacen/update', [AlmacenController::class, 'update'])->name('almacenupdate');
  Route::post('/promotor/update', [PromotorController::class, 'update'])->name('promotorupdate');
  Route::post('/compra/update', [CompraController::class, 'update'])->name('compraupdate');

  //Eliminar registro
  Route::delete('/categoria/delete/{id}', [CategoriaController::class, 'destroy'])->name('destroycategoria');
  Route::delete('/cliente/delete/{id}',[ClienteController::class,'destroy'])->name('destroyclient');
  Route::delete('/usuario/delete/{id}',[UsuarioController::class,'destroy'])->name('destroyuser');
  Route::delete('/producto/delete/{id}', [ProductoController::class, 'destroy'])->name('destroyproduct');
  Route::delete('/almacen/delete/{id}', [AlmacenController::class, 'destroy'])->name('destroyalmacen');
  Route::delete('/promotor/delete/{id}', [PromotorController::class, 'destroy'])->name('destroypromotor');
  Route::delete('/compra/delete/{id}', [CompraController::class, 'destroy'])->name('destroycompra');

  // rutas de apoyo
  Route::get('/categoria/producto', [CategoriaController::class, 'getcategoria'])->name('categoriaget');
  Route::get('/productos', [ProductoController::class,'getproducts'])->name('getproducts');
  Route::get('/promotores', [PromotorController::class, 'getpromotores'])->name('getpromotores');
  Route::get('/clientes',[ClienteController::class,'getclients'])->name('getclient');
  Route::post('/usuario/disponibilidad/email', [UsuarioController::class, 'userdisponiblidad'])->name('userdisponiblidad');
  Route::get('producto/precio/{id}',[PrecioProductoController::class, 'getpreciobyid'])->name('getpreciobyid');
  Route::get('product/existencia/{id}',[ProductoController::class, 'existproductalmacen'])->name('existproductalmacen');


  // contenido para Dashboardcontroller
  Route::get('product/inexistentes',[Dashboardcontroller::class, 'getproductinexistentes'])->name('getproductinexistentes');
  Route::post('ventas/total',[Dashboardcontroller::class, 'gettotalventasmes'])->name('gettotalventasmes');
  Route::get('ventas/total/mes',[Dashboardcontroller::class, 'getventasbymestotal'])->name('getventasbymestotal');
  Route::get('ventas/total/mes/{id}',[Dashboardcontroller::class, 'getventasbyusertotal'])->name('getventasbyusertotal');
});
