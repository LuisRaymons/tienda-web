<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Tienda LRVA</title>
    <link rel="icon" href="src/img/tienda.ico">
    <link rel="stylesheet" href="../src/plugging/bootstrap/css/bootstrap.min.css">
    
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
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="src/plugging/alertify/alertify.default.css">

    <link rel="stylesheet" href="src/plugging/MaterialDesign/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/css/style.css">
    @yield('css')
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <!-- Logo cerrar session -->
          <a href="logout">Cerrar session</a>
        </ul>
      </nav>

      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="home" class="brand-link">
          <span class="brand-text font-weight-light">Tienda Virtual</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                <img src="{{Auth::user()->img}}" class="img-circle elevation-2" alt="User Image">
              </div>
              <div class="info">
                <a href="#" class="d-block">{{Auth::user()->name}}</a>
              </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2 navbar-expand-lg">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }} active">
                  <a href="home" class="nav-link">
                    <i class="nav-icon mdi mdi-speedometer"></i>
                    <p> Dashboard </p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="categoria" class="nav-link">
                    <i class="nav-icon mdi mdi-arrange-bring-to-front"></i>
                    <p>Categoria de Productos<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="cliente" class="nav-link">
                    <i class="nav-icon mdi mdi-account-group-outline"></i>
                    <p>Cliente<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="usuario" class="nav-link">
                    <i class="nav-icon mdi mdi-account-network-outline"></i>
                    <p>Usuario<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="producto" class="nav-link">
                    <i class="nav-icon mdi mdi-tag"></i>
                    <p>Producto<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="almacen" class="nav-link">
                    <i class="nav-icon mdi mdi-store"></i>
                    <p>Almacen<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="promotor" class="nav-link">
                    <i class="nav-icon mdi mdi-account-child-circle"></i>
                    <p>Promotor<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="compra" class="nav-link">
                    <i class="nav-icon mdi mdi-cart-arrow-down"></i>
                    <p>Compra<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
                  <a href="venta" class="nav-link">
                    <i class="nav-icon mdi mdi-cart-arrow-up"></i>
                    <p>Venta<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>

                <li class="nav-item {{(Auth::user()->type == 'Administrador')   ? '' : 'showcontentuser' }}">
                  <a href="productoprecios" class="nav-link">
                    <i class="nav-icon mdi mdi-currency-usd-off"></i>
                    <p>Precio Producto<!-- <span class="right badge badge-danger">New</span> --></p>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
      </aside>

      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            @yield('content')
          </div>
        </section>

      </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
      <script src="../src/plugging/bootstrap/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
      <script src="../src/plugging/jqueryValidate/jquery.validate.min.js"></script>
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
      <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
      <script src="src/plugging/sweetalert2/sweetalert2.all.min.js"></script>
      <script src="src/plugging/alertify/alertify.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js"></script>
      <script src="src/js/adminlte.js" charset="utf-8"></script>
      @yield('js')
  </body>
</html>
