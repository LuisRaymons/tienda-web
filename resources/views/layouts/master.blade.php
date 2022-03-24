<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="src/img/tienda.ico">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.0/css/dataTables.dateTime.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.5.4/css/colReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <title>Tienda LRVA</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    @yield('css')
  </head>
  <body>
    <div class="container-scroller">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="logoname" href="home">Tienda LRVA</a>
        </div>
        <ul class="nav">
          <li class="nav-item profile">
            <div class="profile-desc">
              <div class="profile-pic">
                <div class="count-indicator">
                  <img class="img-xs rounded-circle " src="{{Auth::user()->img}}" alt="">
                  <span class="count bg-success"></span>
                </div>
                <div class="profile-name">
                  <h5 class="mb-0 font-weight-normal">{{Auth::user()->name}}</h5>
                  <span>{{Auth::user()->type}}</span>
                </div>
              </div>
            </div>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="home">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="categoria">
              <span class="menu-icon">
                <i class="mdi mdi-arrange-bring-to-front"></i>
              </span>
              <span class="menu-title">Categoria de Productos</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="cliente">
              <span class="menu-icon">
                <i class="mdi mdi-account-group-outline"></i>
              </span>
              <span class="menu-title">Cliente</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="usuario">
              <span class="menu-icon">
                <i class="mdi mdi-account-network-outline"></i>
              </span>
              <span class="menu-title">Usuario</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="producto">
              <span class="menu-icon">
                <i class="mdi mdi-tag"></i>
              </span>
              <span class="menu-title">Producto</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="almacen">
              <span class="menu-icon">
                <i class="mdi mdi-store"></i>
              </span>
              <span class="menu-title">Almacen</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="promotor">
              <span class="menu-icon">
                <i class="mdi mdi-account-child-circle"></i>
              </span>
              <span class="menu-title">Promotor</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="compra">
              <span class="menu-icon">
                <i class="mdi mdi-cart-arrow-down"></i>
              </span>
              <span class="menu-title">Compra</span>
            </a>
          </li>
          <li class="nav-item menu-items {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
            <a class="nav-link" href="venta">
              <span class="menu-icon">
                <i class="mdi mdi-cart-arrow-up"></i>
              </span>
              <span class="menu-title">Venta</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="container-fluid page-body-wrapper">
        <nav class="navbar p-0 fixed-top d-flex flex-row">
          <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="home">Tienda LRVA</a>
          </div>
          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="mdi mdi-menu"></span>
            </button>
            <ul class="navbar-nav navbar-nav-right">
              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="{{Auth::user()->img}}" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name">{{Auth::user()->name}}</p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Perfil</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Configuraciones</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <!-- <a class="dropdown-item preview-item" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                  id="logout">
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Cerrar Session
                      </p>
                    </div>
                  </a> -->

                  <a href="#" class="dropdown-item preview-item" id="logout">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Cerrar Session
                      </p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center">{{Auth::user()->type}}</p>
                </div>
              </li>
            </ul>
          </div>
        </nav>
        <div class="main-panel">
          <div class="content-wrapper">
              <main class="content">
                 @yield('content')
              </main>
          </div>

          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © LRVA 2022</span>
            </div>
          </footer>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.1.0/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/misc.js"></script>
    @yield('js')
  </body>
</html>
