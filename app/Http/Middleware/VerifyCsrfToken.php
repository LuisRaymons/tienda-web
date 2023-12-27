<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'http://127.0.0.1:8000/categoria/new', // nuevo cliente
        'http://127.0.0.1:8000/cliente/new', // nuevo cliente
        'http://127.0.0.1:8000/usuario/new', // nuevo usuario
        'http://127.0.0.1:8000/producto/new', // nuevo producto
        'http://127.0.0.1:8000/promotor/new', // nuevo promotor
        'http://127.0.0.1:8000/compra/new', // nuevo compra
        'http://127.0.0.1:8000/venta/new', // nuevo venta
        'http://127.0.0.1:8000/productoprecios/new', // nuevo precio producto

          // Modificaciones
        'http://127.0.0.1:8000/categoria/update', // update categoria producto
        'http://127.0.0.1:8000/cliente/update', // update cliente
        'http://127.0.0.1:8000/usuario/update', // update usuario
        'http://127.0.0.1:8000/producto/update', // update producto
        'http://127.0.0.1:8000/almacen/update', // update almacen
        'http://127.0.0.1:8000/promotor/update', // update almacen
        'http://127.0.0.1:8000/compra/update', // update compra
        'http://127.0.0.1:8000/productoprecios/update', // update precio producto

        // Eliminar
        'http://127.0.0.1:8000/categoria/delete/*', // delete categoria producto
        'http://127.0.0.1:8000/cliente/delete/*', // delete cliente
        'http://127.0.0.1:8000/usuario/delete/*', // delete usuario
        'http://127.0.0.1:8000/producto/delete/*', // delete producto
        'http://127.0.0.1:8000/almacen/delete/*', // delete almacen
        'http://127.0.0.1:8000/promotor/delete/*', // delete almacen
        'http://127.0.0.1:8000/compra/delete/*', // delete compra
        'http://127.0.0.1:8000/productoprecios/delete/*', // delete compra

        // dashoboard
        'http://127.0.0.1:8000/venta/total', // delete compra
        'http://127.0.0.1:8000/productoprecios/get/one', // obtener el prrecio de un producto



        'http://127.0.0.1:8000/usuario/disponibilidad/email', // consultar disponibilidad de correo
        'http://127.0.0.1:8000/login', // login
        'http://127.0.0.1:8000/logout', // login
        'http://127.0.0.1:8000/register', // consultar disponibilidad de correo
    ];
}
