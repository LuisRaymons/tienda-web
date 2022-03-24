@extends('layouts.master')

@section('css')
@endsection

@section('content')

  <nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-item nav-link active" id="nav-table" data-toggle="tab" href="#nav-table-target" role="tab" aria-controls="nav-table-target" aria-selected="true">Datos</a>
      <a class="nav-item nav-link {{(Auth::user()->type == 'Administrador') ? '' : 'showcontentuser'}}" id="nav-register" data-toggle="tab" href="#nav-register-target" role="tab" aria-controls="nav-register-target" aria-selected="false">Registro</a>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
    <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
    <div class="tab-pane fade show active" id="nav-table-target" role="tabpanel" aria-labelledby="nav-table">
      <table id="provedor-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
        <thead class="bg-primary text-white">
          <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Sitio web</th>
            <th>Logo</th>
            <th class='notexport'>Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
      <form action="" method="post" id="form-new-promotor">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Nombre</label>
          <input type="text" class="form-control" name="namepromotor" id="namepromotor" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Dirección</label>
          <input type="text" class="form-control" name="addresspromotor" id="addresspromotor" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Telefono</label>
          <input type="text" class="form-control" name="phonepromotor" id="phonepromotor" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Citio web</label>
          <input type="text" class="form-control" name="webpromotor" id="webpromotor" required/>
        </div>
        <div class="input-group mb-3">
          <input type="file" class="form-control" name="imgnewpromotor" id="imgnewpromotor">
          <label class="input-group-text" for="inputGroupFile02">Logo de la empresa</label>
        </div>
        <div class="d-grid gap-2">
          <button class="btn btn-primary" type="button" id="btnsavepromotor">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal editar promotor -->
  <div class="modal fade" id="modaleditpromotor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Modificar Promotor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="" method="post" id="form-edit-promotor">
                  <input type="hidden" name="idpromotor" id="idpromotor">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="namepromotoredit" id="namepromotoredit" required/>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="addresspromotoredit" id="addresspromotoredit" required/>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Telefono</label>
                    <input type="text" class="form-control" name="phonepromotoredit" id="phonepromotoredit" required/>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Citio web</label>
                    <input type="text" class="form-control" name="webpromotoredit" id="webpromotoredit" required/>
                  </div>
                  <div class="input-group mb-3">
                    <input type="file" class="form-control" name="imgnewpromotoredit" id="imgnewpromotoredit">
                    <img src="" alt="" id="srclogopromotor" width="60px" height="40px">
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnsavepromotorupdate">Guardar</button>
              </div>
            </div>
          </div>
        </div>
@endsection

@section('js')
  <script src="src/js/layout/promotor.js"></script>
@endsection
