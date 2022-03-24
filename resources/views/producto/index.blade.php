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
    <table id="product-table" class="table table-striped table-bordered shadow-lg mt-4" style="width:100%">
      <thead class="bg-primary text-white">
        <tr>
          <th>Id</th>
          <th>Nombre</th>
          <th>Descripcion</th>
          <th>X Kilo</th>
          <th>Imagen</th>
          <th>Categoria</th>
          <th class='notexport'>Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="tab-pane fade" id="nav-register-target" role="tabpanel" aria-labelledby="nav-register">
    <form action="" method="post" id="form-new-product">
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nameproduct" id="nameproduct" required/>
      </div>
      <div class="mb-3">
        <label for="floatingTextarea2">Descripcion</label>
        <div class="form-floating">
          <textarea class="form-control" placeholder="Descripcion del producto" name="descriptionproduct" id="descriptionproduct" style="height: 100px"></textarea>
        </div>
      </div>
      <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="pricekiloproduct" id="pricekiloproduct">
          <label class="form-check-label" for="disabledFieldsetCheck">Precio por Kilo</label>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="file" class="form-control" name="imgnewproduct" id="imgnewproduct">
        <label class="input-group-text" for="inputGroupFile02">Imagen</label>
      </div>
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Categoria</label>
        <select class="form-control" name="categoriaProduct" id="categoriaProduct" required>
        </select>
      </div>
      <div class="d-grid gap-2">
        <button class="btn btn-primary" type="button" id="btnsaveproduct">Guardar</button>
      </div>
    </form>
  </div>
  </div>

  <!-- Modal editar -->
  <div class="modal fade" id="modaleditproduct" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Modificar Producto</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="post" id="form-edit-product">
                <input type="hidden" name="idproductedit" id="idproductedit">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="nameproductedit" id="nameproductedit" required/>
                </div>
                <div class="mb-3">
                  <label for="floatingTextarea2">Descripcion</label>
                  <div class="form-floating">
                    <textarea class="form-control" placeholder="Descripcion del producto" name="descriptionproductedit" id="descriptionproductedit" style="height: 100px"></textarea>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pricekiloproductedit" id="pricekiloproductedit">
                    <label class="form-check-label" for="disabledFieldsetCheck">
                      Precio por Kilo
                    </label>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="file" class="form-control" name="imgproductedit" id="imgproductedit">
                  <label class="input-group-text" for="inputGroupFile02">Imagen</label>
                  <img src="" alt="" id="imgproducteditsrc" width="60px" height="40px;">
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Categoria</label>
                  <select class="form-control" name="categoriaProductedit" id="categoriaProductedit" required>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="btnsaveproductedit">Guardar</button>
            </div>
          </div>
        </div>
      </div>
@endsection


@section('js')
  <script src="src/js/layout/producto.js"></script>
@endsection
