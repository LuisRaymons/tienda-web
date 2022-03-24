@extends('layouts.master')

@section('css')
@endsection

@section('content')
<input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
<input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
<table id="almacen-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
  <thead class="bg-primary text-white">
    <tr>
      <th>Producto</th>
      <th>Entradas</th>
      <th>Salidas</th>
      <th>Stock</th>
      <th>usuario</th>
      <th class="notexport">Acciones</th>
    </tr>
  </thead>
</table>

  <!-- Modal editar almacen -->
  <div class="modal fade" id="modaleditaralmacen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Modificar Almacen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" method="post" id="form-edit-almacen">
            <input type="hidden" name="idalmacenedit" id="idalmacenedit">
            <input type="hidden" name="iduseredit" id="iduseredit" value="{{Auth::user()->id}}">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Entradas</label>
              <input type="number" class="form-control" id="entryalmacen" name="entryalmacen" required>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Salidas</label>
              <input type="number" class="form-control" id="exitalmacen" name="exitalmacen" required>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Stock</label>
              <input type="number" class="form-control" id="stockalmacen" name="stockalmacen" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnsaveupdatealmacen">Guardar</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="src/js/layout/almacen.js"></script>
@endsection
