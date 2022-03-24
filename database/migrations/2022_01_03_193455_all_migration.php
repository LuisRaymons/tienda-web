<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AllMigration extends Migration
{
    public function up()
    {

       Schema::create('promotor', function (Blueprint $table) {
           $table->id();
           $table->string('nombre',200);
           $table->string('direccion',200);
           $table->string('telefono',100);
           $table->string('sitioWeb',200)->nullable();
           $table->string('img',200)->nullable();
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('categoria_producto', function (Blueprint $table) {
           $table->id();
           $table->string('nombre', 200)->collation('utf8mb4_general_ci');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);

       });
       Schema::create('producto', function (Blueprint $table) {
           $table->id();
           $table->string('nombre',200);
           $table->string('descripcion',400);
           $table->enum('precioPorKilo',['true','false']);
           $table->string('img',200)->nullable();
           $table->unsignedBigInteger('id_categoria');
           $table->foreign('id_categoria')->references('id')->on('categoria_producto');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('productoPrecio', function (Blueprint $table) {
           $table->id();
           $table->decimal('precio', 10,2);
           $table->unsignedBigInteger('id_product');
           $table->foreign('id_product')->references('id')->on('producto');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('almacen', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('entrada')->default(1);
          $table->bigInteger('salida')->default(0);
          $table->bigInteger('stock');
          $table->unsignedBigInteger('id_user');
          $table->unsignedBigInteger('id_producto');
          $table->foreign('id_user')->references('id')->on('users');
          $table->foreign('id_producto')->references('id')->on('producto');
          $table->timestamps();
          $table->softDeletes('deleted_at', 0);
       });
       Schema::create('compra', function (Blueprint $table) {
           $table->id();
           $table->string('folio',200);
           $table->bigInteger('cantidad_stock');
           $table->decimal('precio_total', 10,4);
           $table->string('img',200)->nullable();
           $table->unsignedBigInteger('id_almacen');
           $table->unsignedBigInteger('id_promotor');
           $table->unsignedBigInteger('id_producto');
           $table->foreign('id_almacen')->references('id')->on('almacen');
           $table->foreign('id_promotor')->references('id')->on('promotor');
           $table->foreign('id_producto')->references('id')->on('producto');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('cliente', function (Blueprint $table) {
           $table->id();
           $table->string('nombre', 100);
           $table->string('apellidos', 100);
           $table->string('telefono', 30);
           $table->string('img',200)->nullable();
           $table->string('direccion', 200);
           $table->integer('cp');
           $table->string('colonia', 200);
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('typePay',  function (Blueprint $table){
         $table->id();
         $table->string('name',150);
         $table->timestamps();
       });
       Schema::create('venta', function (Blueprint $table) {
           $table->id();
           $table->string('factura', 100);
           $table->decimal('impuesto', 10,2);
           $table->decimal('precio_total', 10,2);
           $table->unsignedBigInteger('id_pago');
           $table->unsignedBigInteger('id_cliente');
           $table->unsignedBigInteger('id_users');
           $table->foreign('id_pago')->references('id')->on('typePay');
           $table->foreign('id_cliente')->references('id')->on('cliente');
           $table->foreign('id_users')->references('id')->on('users');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('d_venta', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('id_venta');
           $table->unsignedBigInteger('id_producto');
           $table->bigInteger('cantidad');
           $table->decimal('precio', 10, 4);
           $table->foreign('id_venta')->references('id')->on('venta');
           $table->foreign('id_producto')->references('id')->on('producto');
           $table->timestamps();
           $table->softDeletes('deleted_at', 0);
       });
       Schema::create('cuentaporcobrar', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_cliente');
            $table->decimal('precioTotal', 10, 4);
            $table->bigInteger('pagos');
            $table->foreign('id_venta')->references('id')->on('venta');
            $table->foreign('id_cliente')->references('id')->on('cliente');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
       });
       Schema::create('d_cuentaporcobrar', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('id_cuenta');
            $table->decimal('precioabono', 10, 4);
            $table->foreign('id_cuenta')->references('id')->on('cuentaporcobrar');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
       });
       Schema::create('arqueocaja', function (Blueprint $table){
         $table->id();
         $table->datetime('apertura');
         $table->decimal('apertura_precio', 10,4);
         $table->datetime('cierre')->nullable();
         $table->decimal('cierre_precio', 10,4);
         $table->unsignedBigInteger('id_user');
         $table->foreign('id_user')->references('id')->on('users');
         $table->timestamps();
         $table->softDeletes('deleted_at', 0);
       });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('almacen');
        Schema::dropIfExists('compra');
        Schema::dropIfExists('categoria_producto');
        Schema::dropIfExists('producto');
        Schema::dropIfExists('productoPrecio');
        Schema::dropIfExists('cliente');
        Schema::dropIfExists('promotor');
        Schema::dropIfExists('typePay');
        Schema::dropIfExists('venta');
        Schema::dropIfExists('d_venta');
        Schema::dropIfExists('cuentaporcobrar');
        Schema::dropIfExists('d_cuentaporcobrar');
        Schema::dropIfExists('arqueocaja');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

}
