@extends('layouts.master')

@section('css')
  <link rel="stylesheet" href="src/css/venta.css">
@endsection

@section('content')
  <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
  <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
  <input type="hidden" name="token_user" id="token_user" value="{{Auth::user()->api_token}}">
  <!-- Button trigger modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalventa">
    Nueva Venta
  </button></br></br>
  <table id="venta-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
    <thead class="bg-primary text-white">
      <tr>
        <th>Factura</th>
        <th>Precio Total</th>
        <th>Pago</th>
        <th>Cliente</th>
        <th class='notexport'>Acciones</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

  <!-- Modal -->
  <div class="modal fade" id="modalventa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="staticBackdropLabel">Nueva Venta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col">
                <h4>Crear Venta</h4>
                <form id="form-venta-items" action="" method="post">
                  <div class="form-group">
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" name="clientitems" id="clientitems">
                    </select>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-11">
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" name="productitems" id="productitems">
                        </select>
                      </div>
                      <div class="col-1">
                        <button type="button" class="btn btn-light" id="addproductventa"><i class="fas fa-plus"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" name="typepay" id="typepay">
                    </select>
                  </div>
                </form>
              </div>
              <div class="col">
                  <h4>Detalle de lal venta</h4>
                  <table id="tabledetailventa" class="display" style="width:100%">
                    <thead>
                      <tr bgcolor="#6043ff">
                        <th>Cantidad</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Accion</th>
                      </tr>
                    </thead>
                    <tbody id="detailventabody">
                    </tbody>
                  </table>
                    <h5 id="precioTotaldetail" style="display:none; margin-left:60%;"></h5>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelventa">Cancelar</button>
          <button type="button" class="btn btn-primary" id="payventa">Pagar</button>
        </div>
        <div class="cho-container"></div>
      </div>
    </div>
  </div>

  <!-- Pagos -->
  <div class="modal fade" id="modalPagoRecibe" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Recibe: </label>
              <input type="number" class="form-control" name="idpago" id="idpago">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Total Venta</label>
              <input type="number" class="form-control" name="pagototalcobrar" id="pagototalcobrar" disabled>
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Cambio</label>
              <input type="number" class="form-control" name="cambioventabyefectivo" id="cambioventabyefectivo" disabled>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btncobrarventa">Cobrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal add product -->
  <div class="modal fade" id="addproductmodel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <p>¿Cuantos <span class="cantidad-items-product"></span> desea agregar al carrito? </p>
          <p>Cantidad máxima: <span id="token-items"></span> </p>
          <input type="number" class="form-control" name="items-product" id="items-product" min="1">
          <p style="color:red; display:none;" id="error-product-exists">No se encontraron productos de <span class="cantidad-items-product"></span> </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnaddproduct">Agregar Producto</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal detalle de venta -->
  <div class="modal fade" id="staticmodaldetalleventa" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title" id="staticBackdropLabel">Detalle de la venta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>Detalle de la venta <span id="facturaid"></span></h5>
          <h5>Cliente <span id="clienteventa"></span></h5>
          <h5>Atendido por <span id="userventa"></span></h5>
          <h5>Pago <span id="pagoventa"></span></h5>

          <div id="tablecontainer">
            <!-- tabla detalle de ventas-->
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal editar venta-->
<div class="modal fade" id="staticmodaleditventa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modificar Venta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col">
            <h4>Modificar Venta</h4>
            <form id="form-venta-items-edit" action="" method="post">
              <div class="form-group">
                <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" name="clientitems" id="clientitemsedit">
                </select>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-11">
                    <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" name="productitems" id="productitemsedit">
                    </select>
                  </div>
                  <div class="col-1">
                    <button type="button" class="btn btn-light" id="addproductventaedit"><i class="fas fa-plus"></i></button>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" name="typepayedit" id="typepayedit">
                </select>
              </div>
            </form>
          </div>
          <div class="col">
            <h4>Detalle de lal venta</h4>
            <table id="tabledetailventa" class="display" style="width:100%">
              <thead>
                <tr bgcolor="#6043ff">
                  <th>Cantidad</th>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody id="detailventabodyedit">
              </tbody>
            </table>
              <h5 id="precioTotaldetailedit" style="display:none; margin-left:60%;"></h5>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>



@endsection

@section('js')
  <script src="https://sdk.mercadopago.com/js/v2"></script>
  <script src="src/js/layout/venta.js"></script>
@endsection
