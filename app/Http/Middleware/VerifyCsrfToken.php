<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'http://127.0.0.1/tienda/public/categoria/new', // nuevo cliente
        'http://127.0.0.1/tienda/public/cliente/new', // nuevo cliente
        'http://127.0.0.1/tienda/public/usuario/new', // nuevo usuario
        'http://127.0.0.1/tienda/public/producto/new', // nuevo producto
        'http://127.0.0.1/tienda/public/promotor/new', // nuevo promotor
        'http://127.0.0.1/tienda/public/compra/new', // nuevo compra
        'http://127.0.0.1/tienda/public/venta/new', // nuevo venta
        'http://127.0.0.1/tienda/public/producto/precios/new', // nuevo precio producto

          // Modificaciones
        'http://127.0.0.1/tienda/public/categoria/update', // update categoria producto
        'http://127.0.0.1/tienda/public/cliente/update', // update cliente
        'http://127.0.0.1/tienda/public/usuario/update', // update usuario
        'http://127.0.0.1/tienda/public/producto/update', // update producto
        'http://127.0.0.1/tienda/public/almacen/update', // update almacen
        'http://127.0.0.1/tienda/public/promotor/update', // update almacen
        'http://127.0.0.1/tienda/public/compra/update', // update compra
        'http://127.0.0.1/tienda/public/producto/precios/update', // update precio producto

        // Eliminar
        'http://127.0.0.1/tienda/public/categoria/delete/*', // delete categoria producto
        'http://127.0.0.1/tienda/public/cliente/delete/*', // delete cliente
        'http://127.0.0.1/tienda/public/usuario/delete/*', // delete usuario
        'http://127.0.0.1/tienda/public/producto/delete/*', // delete producto
        'http://127.0.0.1/tienda/public/almacen/delete/*', // delete almacen
        'http://127.0.0.1/tienda/public/promotor/delete/*', // delete almacen
        'http://127.0.0.1/tienda/public/compra/delete/*', // delete compra
        'http://127.0.0.1/tienda/public/producto/precios/delete/*', // delete compra

        // dashoboard
        'http://127.0.0.1/tienda/public/venta/total', // delete compra



        'http://127.0.0.1/tienda/public/usuario/disponibilidad/email', // consultar disponibilidad de correo
        'http://127.0.0.1/tienda/public/login', // login
        'http://127.0.0.1/tienda/public/logout', // login
        'http://127.0.0.1/tienda/public/register', // consultar disponibilidad de correo
    ];
}
