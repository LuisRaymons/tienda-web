@extends('layouts.master')

@section('css')
  <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
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
      <table id="usuario-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
        <thead class="bg-primary text-white">
          <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Tipo Usuario</th>
            <th>Imagen</th>
            <th class="notexport">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
      <form action="" method="post" id="form-new-user">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Nombre</label>
          <input type="text" class="form-control" name="nameuser" id="nameuser" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Correo</label>
          <input type="email" class="form-control" name="emailuser" id="emailuser" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Password</label>
          <input type="text" class="form-control" name="passworduser" id="passworduser" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Confirmar Password</label>
          <input type="text" class="form-control" name="confirmpassworduser" id="confirmpassworduser" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Tipo de usuario</label>
          <select class="form-control" name="typeusernew" id="typeusernew">
            <option value="0">Seleccione un tipo de usuario</option>
            <option value="Administrador">Administrador</option>
            <option value="Vendedor">Vendedor</option>
            <option value="Cliente">Cliente</option>
          </select>
        </div>
        <div class="input-group mb-3">
          <input type="file" class="form-control" name="imgnewuser" id="imgnewuser">
          <label class="input-group-text" for="inputGroupFile02">Imagen</label>
        </div>
        <div class="d-grid gap-2">
          <button class="btn btn-primary" type="button" id="btnsaveuser">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal editar usuario -->
  <div class="modal fade" id="modaledituser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Modificar Usuario</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="post" id="form-edit-user">
                <input type="hidden" name="idupdateuser" id="idupdateuser">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nameuseredit" id="nameuseredit" required/>
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Correo</label>
                  <input type="email" class="form-control" name="emailuseredit" id="emailuseredit" required/>
                  <span id="error-correo-edit" style="color:red; display:none;">Este correo ya esta ocupado por ti o por otro usuario</span>
                </div>
                <div class="mb-3">
                  <label class="checkbox-inline changepasswordcheck">
                    <!-- <input type="checkbox" data-toggle="toggle" data-on="Cambiar" data-off="Mantemer" data-onstyle="danger" data-offstyle="success" data-width="100" data-size="normal" data-style="ios"> Cambiar Contraseña  -->
                    <input type="checkbox" id="checkchangepassword" data-toggle="toggle" data-on="Cambiar" data-off="Mantener" data-style="ios" data-width="100" data-onstyle="danger" data-offstyle="success"> Cambiar Contraseña
                  </label>
                </div>
                <div id="div-passwords" style="display:none">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Password</label>
                    <input type="text" class="form-control" name="passworduseredit" id="passworduseredit" required/>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Confirmar Password</label>
                    <input type="text" class="form-control" name="confirmpassworduseredit" id="confirmpassworduseredit" required/>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Tipo de usuario</label>
                  <select class="form-control" name="typeuseredit" id="typeuseredit">
                    <option value="0">Seleccione un tipo de usuario</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Vendedor">Vendedor</option>
                    <option value="Cliente">Cliente</option>
                  </select>
                </div>

                <div class="input-group mb-3">
                  <input type="file" class="form-control" name="imguseredit" id="imguseredit">
                  <img src="" alt="" id="user-icon-update" width="50px" height="40px">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="btnsaveuseredit">Guardar</button>
            </div>
          </div>
        </div>
      </div>

@endsection

@section('js')
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="src/js/layout/usuario.js"></script>
@endsection
