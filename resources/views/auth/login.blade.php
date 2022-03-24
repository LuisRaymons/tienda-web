@extends('layouts.app')

@section('css')
  <link type="text/css" rel="stylesheet" href="src/css/login.css"/>
@endsection

@section('content')
  <div class="container">
    <div class="loginlayout">
      <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <button class="nav-link active textwhite" id="nav-login-tab" data-bs-toggle="tab" data-bs-target="#nav-login" type="button" role="tab" aria-controls="nav-login" aria-selected="true">Login</button>
          <button class="nav-link textwhite" id="nav-register-tab" data-bs-toggle="tab" data-bs-target="#nav-register" type="button" role="tab" aria-controls="nav-register" aria-selected="false">Registrarme</button>
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-login" role="tabpanel" aria-labelledby="nav-login-tab">
          <h3 class="container-center-titulo textwhite">Login</h3>
          <div class="container">
            <img src="src/img/user-login2.png" alt="Usurio icon" id="img-login-user">
            <form action="" method="post" id="form-login">
              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fa fa-user form-control-feedback"></i>
                  <input type="text" name="email" id="email" class="form-control" placeholder="Correo electronico" required/>
                </div>
              </div>
              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fas fa-key form-control-feedback"></i>
                  <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required/>
                </div>
              </div>
              <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary" id="btn-login">Entrar</button>
              </div>
            </form>
          </div>
        </div>
        <div class="tab-pane fade" id="nav-register" role="tabpanel" aria-labelledby="nav-register-tab">
          <h3 class="container-center-titulo textwhite">Registrarme</h3>
          <div class="container">
            <form action="" method="post" id="form-new-register">
              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fa fa-user form-control-feedback"></i>
                  <input type="text" name="nameregister" id="nameregister" class="form-control" placeholder="Nombre completo" required>
                </div>
              </div>
              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fas fa-envelope form-control-feedback"></i>
                  <input type="email" name="emailregister" id="emailregister" class="form-control" placeholder="Correo" required>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fas fa-key form-control-feedback"></i>
                  <input type="password" name="passwordregister" id="passwordregister" class="form-control" placeholder="Contraseña" required>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-group has-feedback">
                  <i class="fas fa-key form-control-feedback"></i>
                  <input type="password" name="confirmpasswordregister" id="confirmpasswordregister" class="form-control" placeholder="Confirmar Contraseña" required>
                </div>
              </div>
              <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary" id="btnnewUser">Crear cuenta</button>
              </div>
            </form>
          </div>
          <p class="textcenterparrafo">Recuerda que su cuenta debe <br/> ser activada por un administrador </p>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('js')
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
  <script src="src/js/login.js"></script>
@endsection
