@extends('layouts.master')

@section('css')

@endsection

@section('content')
<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-table" data-toggle="tab" href="#nav-table-target" role="tab" aria-controls="nav-table-target" aria-selected="true">Datos</a>
    <a class="nav-item nav-link" id="nav-register" data-toggle="tab" href="#nav-register-target" role="tab" aria-controls="nav-register-target" aria-selected="false">Registro</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
  <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
  <input type="hidden" name="token_user" id="token_user" value="{{Auth::user()->api_token}}">
  <input type="hidden" name="emailuser" id="emailuser" value="{{Auth::user()->email}}">
  <div class="tab-pane fade show active" id="nav-table-target" role="tabpanel" aria-labelledby="nav-table">
    <table id="producto-precio-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
      <thead class="bg-primary text-white">
        <tr>
          <th>Id</th>
          <th>producto</th>
          <th>Precio</th>
          <th>Imagen</th>
          <th class="notexport">Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
    <form action="" method="post" id="form-new-product-price"></br>
      <div class="form-group">
        <label>Producto</label>
        <select class="form-control" name="product" id="product" required>
        </select>
        <label id="product-error" class="error" for="product" style="display:none">Seleccione un producto</label>
      </div>
      <div class="form-group">
        <label>Precio</label>
        <input type="number" class="form-control" name="precioproduct" id="precioproduct" placeholder="0.00" required/>

      </div>
      <div class="d-grid gap-2">
        <button class="btn btn-primary" type="button" id="btnsavepriceproduct">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal editar -->
<div class="modal fade" id="staticmodaleditprecioproduct" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Modificar precio del producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="form-edit-product-precio">
          <div class="form-group">
            <label>Producto:</label>
            <input type="text" class="form-control" name="product" id="productedit" required disabled>
          </div>
          <div class="form-group">
            <label>Precio:</label>
            <input type="number" class="form-control" name="precio" id="precioedit" required>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btneditsaveproductprecio">Guardar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
  <script src="src/js/layout/producto-price.js"></script>
@endsection
