<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AlmacenModel;
use App\Models\CategoriaModel;
use App\Models\ClienteModel;
use App\Models\CompraModel;
use App\Models\ProductoModel;
use App\Models\PrecioProductoModel;
use App\Models\PromotorModel;
use App\Models\VentaModel;
use App\Models\VentaDetailModel;
use DB;

class AllSeeder extends Seeder
{
    public function run()
    {
        DB::table('typePay')->insert(['name'=> 'Efectivo','created_at'=> date('Y-m-d H:m:s'),'updated_at'=> date('Y-m-d H:m:s')]);
        DB::table('typePay')->insert(['name'=> 'Tarjeta de credito o debito','created_at'=> date('Y-m-d H:m:s'),'updated_at'=> date('Y-m-d H:m:s')]);
        DB::table('typePay')->insert(['name'=> 'Mercado Pago','created_at'=> date('Y-m-d H:m:s'),'updated_at'=> date('Y-m-d H:m:s')]);
        ClienteModel::insert(['nombre'=> 'Cliente', 'apellidos' => 'Generico', 'telefono' => '0000000000','img'=>'src/img/user-icon-6.png', 'direccion'=>'-----', 'cp'=> '000000', 'colonia' => '-------','created_at'=> date('Y-m-d H:m:s'),'updated_at'=> date('Y-m-d H:m:s')]);
        User::insert(['name'=>'Admin Admin','email'=>'superadmin@gmail.com','password'=>Hash::make('Admin123'),'img'=>'src/img/user-icon-6.png','type'=>'Administrador','api_token'=>'MqN7lCKFy0lRfXxnhjYLnVf5Pkg83K','created_at'=> date('Y-m-d H:m:s'),'updated_at'=> date('Y-m-d H:m:s')]);
        CategoriaModel::factory()->count(5)->create()->each(function($categoria){
          $user = User::factory()->count(4)->create();
          $cliente = ClienteModel::factory()->count(20)->create();
          $promotor = PromotorModel::factory()->count(15)->create();

          $producto = ProductoModel::factory()->count(6)->create([
            'id_categoria' => $categoria->id
          ])->each(function($producto) use ($categoria, $user, $cliente, $promotor){
            PrecioProductoModel::factory()->create([
              'id_product' => $producto->id
            ]);
            AlmacenModel::factory()->count(1)->create([
              'id_user' => $user[0]->id,
              'id_producto' => $producto->id
            ])->each(function($almacen) use ($categoria, $user, $cliente, $promotor,$producto){
              CompraModel::factory()->count(5)->create([
                'id_almacen' => $almacen->id,
                'id_promotor' => $promotor[0]->id,
                'id_producto' => $producto->id
              ])->each(function($compra) use ($categoria, $user, $cliente, $promotor,$producto){
                VentaModel::factory()->count(2)->create([
                  'id_pago' =>  rand(1, 3),
                  'id_cliente' => $cliente[0]->id,
                  'id_users' => $user[0]->id
                ])->each(function($venta) use($categoria, $user, $cliente, $promotor,$producto){
                  VentaDetailModel::factory()->count(2)->create([
                    'id_venta' => $venta->id,
                    'id_producto' => $producto->id
                  ]);
                });
              });
            });

          });
        });
    }
}
