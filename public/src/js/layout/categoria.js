let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function(e){
  tabledoading();
});
const tabledoading = () =>{
  return table = $('#categoria-table').DataTable({
          language: {
      			"decimal": "",
      			"emptyTable": "No hay información",
      			"info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
      			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
      			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
      			"infoPostFix": "",
      			"thousands": ",",
      			"lengthMenu": "Mostrar _MENU_ Entradas",
      			"loadingRecords": "Cargando...",
      			"processing": "Procesando...",
      			"search": "Buscar:",
      			"zeroRecords": "Sin resultados encontrados",
      			"paginate": {
      				"first": "Primero",
      				"last": "Ultimo",
      				"next": "Siguiente",
      				"previous": "Anterior"
      			}
      		},
          destroy: true,
  		    processing: true,
  		    serverSide: true,
          stateSave: true,
          paging: true,
          lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 100, "All"] ],
          pagingType: "full_numbers",
          pageLength: 25,
          dom: 'lfrtipB',
          ajax: {
              url: "categoria/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" },
             { data: "nombre" },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();

                 if(permisosuser.typeuser == 'Administrador'){
                   return '<button type="button" id="ButtonEditar" onclick = "editarcategoria('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                   '<button type="button" id="Buttondeletes" onclick = "deletecategoria('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
                 } else{
                   return '<label>No aplica</label>';
                 }
                }
             }
         ],
        select: true,
        buttons: [{
            //Botón para Excel
            extend: 'excel',
            footer: true,
            title: 'Compras',
            filename: 'Compras',
            text: '<button class="btn btn-success">Exportar compras a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Compras',
            text: '<button class="btn btn-info">Exportar compras a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            footer: true,
            title: 'Lista de Compras',
            filename: 'Compras',
            text: '<button class="btn btn-danger">Exportar compras a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });

}
const editarcategoria = (id) =>{
  $.ajax({
    url:"categoria/get/" + id,
    beforeSend:function(e){
      loadinproccess(true,"Consultando informacion");
    },
    success:function(categoria){
      if(categoria.code == 200){
        var data = categoria.data;
        $("#idupdatecategoria").val(data.id);
        $("#categorianameupdate").val(data.nombre);
        $("#modaleditcategoria").modal("show");
      }
    },
    error:function(e){
      console.log("Error");
    },
    complete:function(e){
      loadinproccess(false,"Consultando informacion");
    }
  });
}
const deletecategoria = (id) => {
  Swal.fire({
    title: 'Elimanar categoria',
    text: "¿Quieres eliminar a la categoria con el id " + id + "?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url:"categoria/delete/" + id,
        type:"DELETE",
        beforeSend:function(e){
          loadinproccess(true,"Eliminando categoria");
        },
        success:function(categoria){
          if(categoria.code == 200){
            Swal.fire({
              position: 'top-end',
              icon: categoria.status,
              title: categoria.msm,
              showConfirmButton: false,
              timer: 2000
            });
            tabledoading();
          }
        },
        error:function(e){
          console.log("error");
        },
        complete:function(e){
          loadinproccess(false,"Eliminando categoria");
        }
      });
    }
  });
}
const userconsultarrol = () => {
  var arrayuser = new Array({
    "user" : user,
    "typeuser" : typeuser
  });
  return arrayuser[0];
}

$("#btnsavecategoriaedit").click(function(e){
  var validFormCategoriaEdit = $("#form-edit-categoria").validate({
      rules: {
        categorianameupdate:{required: true},
      },
      messages:{
        categorianameupdate:{required: "Se requiere el nombre de la categoria"},
      },
  });
  if(validFormCategoriaEdit.form()){
    var formdataeditcategoria = new FormData($("#form-edit-categoria")[0]);

    $.ajax({
      url:"categoria/update",
      type:"POST",
      data:formdataeditcategoria,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true,"Modificando informacion de la categoria");
      },
      success:function(categoria){
        Swal.fire({
          position: 'top-end',
          icon: categoria.status,
          title: categoria.msm,
          showConfirmButton: false,
          timer: 2000
        });

        if(categoria.code){
          tabledoading();
          $("#modaleditcategoria").modal("hide");
        }
      },
      error:function(e){
        console.log("Error");
      },
      complete:function(e){
        loadinproccess(false,"Modificando informacion de la categoria");
      }
    });
  }
});
$("#btnsavecategorianew").click(function(e){
  var validFormcategorianew = $("#form-new-categoria").validate({
      rules: {
        namenewcategoria:{required: true},
      },
      messages:{
        namenewcategoria:{required: "Se requiere el nombre de la categoria"},

      },
  });
  if(validFormcategorianew.form()){
    var formdatanewcategoria = new FormData($("#form-new-categoria")[0]);

    $.ajax({
      url:"categoria/new",
      type:"POST",
      data:formdatanewcategoria,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true,"Creando categoria");
      },
      success:function(categoria){
        Swal.fire({
          position: 'top-end',
          icon: categoria.status,
          title: categoria.msm,
          showConfirmButton: false,
          timer: 2000
        });

        if(categoria.code == 200){
          $("#namenewcategoria").val("");
          $("#nav-register").removeClass('active');
          $("#nav-register").attr('aria-selected', false);
          $("#nav-register-target").removeClass('active show');
          $("#nav-table").addClass('active');
          $("#nav-table").attr('aria-selected', true);
          $("#nav-table-target").addClass('active show');
          tabledoading();
        }
      },
      error:function(e){
        console.log("Error");
      },
      complete:function(e){
        loadinproccess(false,"Creando categoria");
      }
    });
  }
});


// loading
const loadinproccess = (valor, cadena) => {
    let val = valor;
    let timerInterval;
    let aux = 50000;
    if (val == false) {
        aux = 100;
    }
    var spinner = '<div class="spinner-border" style="width: 3rem;height:3rem;color:#f39c12;" role="status"><span class = "sr-only" > Loading... < /span> </div>';
    Swal.fire({
        html: "<h5>Espere un momento </h5><h6>" + cadena + "</h6><br>" + spinner,
        timer: aux,
        width: 300,
        allowOutsideClick: false,
        showConfirmButton: false,
        onClose: () => {
            clearInterval(timerInterval)
        }
    });
}
