let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function() {
    tabledoading();
});

const tabledoading = () => {
  return table = $('#almacen-table').DataTable({
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
              url: "almacen/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [

             { data: "productname" },
             { data: "entrada" },
             { data: "salida" },
             { data: "stock" },
             { data: "username" },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();

                 if(permisosuser.typeuser == 'Administrador'){
                    return '<button type="button" id="ButtonEditar" onclick = "editaralmacen('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>';
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
            title: 'Archivo',
            filename: 'Almacen',
            text: '<button class="btn btn-success">Exportar almacen a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Almacen',
            text: '<button class="btn btn-info">Exportar almacen a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          //Botón para PDF
          {
            extend: 'print',
            footer: true,
            title: 'Almacen',
            filename: 'Almacen',
            text: '<button class="btn btn-danger">Exportar almacen a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });

}
const editaralmacen = (id) => {

  $.ajax({
    url:"almacen/get/" + id,
    type:"GET",
    beforeSend:function(e){
      loadinproccess(true, "Cargando informacion del almacen seleccionado..");
    },
    success:function(almacen){

      if(almacen.code == 200){
        var data = almacen.data;
        $("#idalmacenedit").val(data.id);
        $("#entryalmacen").val(data.entrada);
        $("#exitalmacen").val(data.salida);
        $("#stockalmacen").val(data.stock);

        $("#modaleditaralmacen").modal("show");

      }
    },
    error:function(e){
      console.log("Error")
    },
    complete:function(e){
      loadinproccess(false, "Cargando informacion del almacen seleccionado..");
    }
  });

}
$("#btnsaveupdatealmacen").click(function(e){

  var validFormalmacenedit = $("#form-edit-almacen").validate({
      rules: {
        idalmacenedit: {
          required: true
        },
        iduseredit: {
          required: true
        },
        entryalmacen: {
          required: true
        },
        exitalmacen: {
          required: true
        },
        stockalmacen: {
          required: true
        }
      },
      messages:{
        idalmacenedit: {
          required: "Se requiere del id de almacen"
        },
        iduseredit: {
          required: "Se requiere del id del usuario"
        },
        entryalmacen: {
          required: "Se requiere de un numero de entradas"
        },
        exitalmacen: {
          required: "Se requiere de una numero de existencia"
        },
        stockalmacen: {
          required: "Se requiere de un numero de stock existentes"
        }
      },
    });
  if(validFormalmacenedit.form()){
    var formdataeditalmacen = new FormData($("#form-edit-almacen")[0]);

    $.ajax({
      url:"almacen/update",
      type:"POST",
      processData: false,
      contentType: false,
      data:formdataeditalmacen,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del almacen');
      },
      success:function(almacen){
        if(almacen.code == 200){
          tabledoading();
          $("#modaleditaralmacen").modal("hide");
        }
        Swal.fire({
          position: 'top-end',
          icon: almacen.status,
          title: almacen.msm,
          showConfirmButton: false,
          timer: 2000
        });

      },
      error:function(e){
        console.log("-------error-------")
      },
      complete:function(e){
        loadinproccess(false, 'Guardando informacion del almacen');
      }
    });

  }
});

const userconsultarrol = () => {
  var arrayuser = new Array({
    "user" : user,
    "typeuser" : typeuser
  });
  return arrayuser[0];
}
