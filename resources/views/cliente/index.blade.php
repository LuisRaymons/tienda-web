@extends('layouts.master')

@section('css')
@endsection

@section('content')

  <nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-item nav-link active" id="nav-table" data-toggle="tab" href="#nav-table-target" role="tab" aria-controls="nav-table-target" aria-selected="true">Datos</a>
      <a class="nav-item nav-link {{(Auth::user()->type == 'Administrador') ? '' : 'showcontentuser' }}" id="nav-register" data-toggle="tab" href="#nav-register-target" role="tab" aria-controls="nav-register-target" aria-selected="false">Registro</a>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-table-target" role="tabpanel" aria-labelledby="nav-table">
      <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
      <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">
      <table id="cliente-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
        <thead class="bg-primary text-white">
          <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>Direccion</th>
            <th>CP</th>
            <th>Colonia</th>
            <th>Foto</th>
            <th class="notexport">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
      <form action="" method="post" id="form-valid-client-new">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Nombre</label>
          <input type="text" class="form-control" name="nameclient" id="nameclient" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Apellidos</label>
          <input type="text" class="form-control" name="lastnameclient" id="lastnameclient" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Telefono</label>
          <input type="text" class="form-control" name="phoneclient" id="phoneclient" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Direccion</label>
          <input type="text" class="form-control" name="addressclient" id="addressclient" required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Codigo Postal</label>
          <input type="number" class="form-control" name="cpclient" id="cpclient" onKeyPress="if(this.value.length==5) return false;"required/>
        </div>
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Colonia</label>
          <select class="form-control" name="coloniaclient" id="coloniaclient" required>
            <option value="0">Seleccione un codigo postal</option>
          </select>
        </div>

        <div class="input-group mb-3">
          <input type="file" class="form-control" name="imgnewclient" id="imgnewclient">
          <label class="input-group-text" for="inputGroupFile02">Imagen</label>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" type="button" id="btnsaveclient">Guardar</button>
        </div>

      </form>
    </div>
  </div>
  </div>

  <!-- modal -->
  <div class="modal fade" id="modaleditclient" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Editar Cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" method="post" id="form-valid-client-editar">
            <input type="hidden" name="idupdateclient" id="idupdateclient">
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nameclientedit" id="nameclientedit" required/>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Apellidos</label>
              <input type="text" class="form-control" name="lastnameclientedit" id="lastnameclientedit" required/>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Telefono</label>
              <input type="text" class="form-control" name="phoneclientedit" id="phoneclientedit" required/>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Direccion</label>
              <input type="text" class="form-control" name="addressclientedit" id="addressclientedit" required/>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Codigo Postal</label>
              <input type="number" class="form-control" name="cpclientedit" id="cpclientedit" onKeyPress="if(this.value.length==5) return false;"required/>
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">Colonia</label>
              <select class="form-control" name="coloniaclientedit" id="coloniaclientedit" required>
                <option value="0">Seleccione un codigo postal</option>
              </select>
            </div>
            <div class="input-group mb-3">
              <input type="file" class="form-control" name="imgeditclient" id="imgeditclient">
              <img src="" alt="" id="imgclienteiconedit" width="50px" height="50px">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="btnsaveclientupdate">Guardar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
    </div>
  </div>

@endsection

@section('js')
  <script src="src/js/layout/cliente.js"></script>
@endsection
