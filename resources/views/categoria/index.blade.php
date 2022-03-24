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
  <div class="tab-pane fade show active" id="nav-table-target" role="tabpanel" aria-labelledby="nav-table">
    <table id="categoria-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
      <thead class="bg-primary text-white">
        <tr>
          <th>Id</th>
          <th>Nombre</th>
          <th class='notexport'>Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
    <form action="" method="post" id="form-new-categoria">
      <div class="form-group">
        <label for="exampleInputEmail1">Nombre</label>
        <input type="text" class="form-control" id="namenewcategoria" name="namenewcategoria" placeholder="Nombre" required>
      </div>
      <div class="form-grop">
        <button type="button" class="btn btn-primary btn-lg btn-block" id="btnsavecategorianew">Guardar</button>
      </div>
    </form>
  </div>
</div>


<!-- Modal editar compra-->
<div class="modal fade" id="modaleditcategoria" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Modificar Categoria</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form  method="post" id="form-edit-categoria">
              <div class="mb-3">
                <input type="hidden" name="usercompraedit" value="{{Auth::user()->id}}">
                <input type="hidden" name="idupdatecategoria" id="idupdatecategoria">
                <label for="exampleInputEmail1" class="form-label">Categoria nombre</label>
                <input type="text" class="form-control" name="categorianameupdate" id="categorianameupdate"  required/>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnsavecategoriaedit">Guardar</button>
          </div>
        </div>
      </div>
    </div>


@endsection

@section('js')
  <script src="src/js/layout/categoria.js"></script>
@endsection
