@extends('layouts.master')

@section('css')
@endsection

@section('content')

  <nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-item nav-link active" id="nav-table-target-tab" data-toggle="tab" href="#nav-table-target" role="tab" aria-controls="nav-table-target" aria-selected="true">Datos</a>
      <a class="nav-item nav-link {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor') ? '' : 'showcontentuser'}}" id="nav-register-target-tab" data-toggle="tab" href="#nav-register-target" role="tab" aria-controls="nav-register-target" aria-selected="false">Registro</a>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
    <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
    <div class="tab-pane fade show active" id="nav-table-target" role="tabpanel" aria-labelledby="nav-table-target-tab">
      <table id="compra-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
        <thead class="bg-primary text-white">
          <tr>
            <th>Id</th>
            <th>Folio</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>Almacen</th>
            <th>Promotor</th>
            <th>Producto</th>
            <th class='notexport'>Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register-target-tab">
      <form action="index.html" method="post" id="form-new-compra">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Cantidad de productos</label>
          <input type="number" class="form-control" name="stockcompra" id="stockcompra" required/>
          <input type="hidden" name="usercompra" value="{{Auth::user()->id}}">
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Precio Total de la compra</label>
          <input type="number" class="form-control" name="preciocompra" id="preciocompra" step="0.25" required/>
        </div>
        <div class="input-group mb-3">
          <input type="file" class="form-control" name="imgnewcompra" id="imgnewcompra">
          <label class="input-group-text" for="inputGroupFile02">Folio</label>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Producto</label>
          <select class="form-control" name="productnewcompra" id="productnewcompra" required>
          </select>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Promotor</label>
          <select class="form-control" name="promotornewcompra" id="promotornewcompra" required>
          </select>
        </div>
        <div class="d-grid gap-2">
          <button class="btn btn-primary" type="button" id="btnsavecompra">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal editar compra-->
  <div class="modal fade" id="modaleditcompra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Modificar Compra</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form  method="post" id="form-edit-compra">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Cantidad de productos</label>
                  <input type="number" class="form-control" name="stockcompraedit" id="stockcompraedit" required/>
                  <input type="hidden" name="usercompraedit" value="{{Auth::user()->id}}">
                  <input type="hidden" name="idupdatecompra" id="idupdatecompra">
                  <input type="hidden" name="idalmacenedit" id="idalmacenedit">
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Precio Total de la compra</label>
                  <input type="number" class="form-control" name="preciocompraedit" id="preciocompraedit"  required/>
                </div>
                <div class="input-group mb-3">
                  <input type="file" class="form-control" name="imgeditcompra" id="imgeditcompra">
                  <img src="" alt="" id="imgsrccompraedit" width="50px" height="40px">
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Producto</label>
                  <select class="form-control" name="producteditcompra" id="producteditcompra">
                  </select>
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Promotor</label>
                  <select class="form-control" name="promotoreditcompra" id="promotoreditcompra">
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="btnsavecompraedit">Guardar</button>
            </div>
          </div>
        </div>
      </div>

@endsection

@section('js')
  <script src="src/js/layout/compra.js"></script>
@endsection
