let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function(e){
  tabledoading();
});

const tabledoading = () =>{
  return table = $('#provedor-table').DataTable({
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
              url: "promotor/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" },
             { data: "nombre" },
             { data: "direccion" },
             { data: "telefono" },
             { data: "sitioWeb" },
             {
               "data": 'img',
               "render": function (data, type, JsonResultRow, meta) {
                 return '<img src="'+data+'" width="100%" height="90px"/>';
                }
             },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();

                 if(permisosuser.typeuser == 'Administrador'){
                   return '<button type="button" id="ButtonEditar" onclick = "editarpromotor('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                   '<button type="button" id="Buttondeletes" onclick = "deletepromotor('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
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
            filename: 'Promotores',
            text: '<button class="btn btn-success">Exportar a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Promotores',
            text: '<button class="btn btn-info">Exportar a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          //Botón para PDF
          {
            extend: 'print',
            footer: true,
            title: 'Lista de Promotores',
            filename: 'Promotores',
            text: '<button class="btn btn-danger">Exportar a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });

}
const editarpromotor = (id) => {

  $.ajax({
    url:"promotor/get/" + id,
    type:"GET",
    beforeSend:function(e){
      loadinproccess(true,"Cargando informacion del promotor seleccionado");
    },
    success:function(promotor){

      if(promotor.code == 200){
        var data = promotor.data;

        $("#idpromotor").val(data.id);
        $("#namepromotoredit").val(data.nombre);
        $("#addresspromotoredit").val(data.direccion);
        $("#phonepromotoredit").val(data.telefono);
        $("#webpromotoredit").val(data.sitioWeb);
        $("#srclogopromotor").attr("src",data.img);


        $("#modaleditpromotor").modal("show");
        //imgnewpromotoredit
      }
    },
    error:function(e){
      console.log("error");
    },
    complete:function(e){
      loadinproccess(false,"Cargando informacion del promotor seleccionado");
    }
  });
}
const deletepromotor = (id) => {
    Swal.fire({
      title: '¿Quieres eliminar al promotor con el id: ' + id,
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $.ajax({
          url:"promotor/delete/" + id,
          type:"DELETE",
          beforeSend:function(e){
            loadinproccess(true,"Elimando promotor");
          },
          success:function(promotor){
            if(promotor.code == 200){
              Swal.fire({
                position: 'top-end',
                icon: promotor.status,
                title: promotor.msm,
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
            loadinproccess(false,"Elimando promotor");
          }
        });
      }
    })
}

$("#btnsavepromotor").click(function(e){
  var validFormPromotorNew = $("#form-new-promotor").validate({
      rules: {
        namepromotor: {required: true},
        addresspromotor: {required: true},
        phonepromotor: {required: true}
      },
      messages:{
        namepromotor: {required: "Se requiere el nombre"},
        addresspromotor: {required: "Se requiere del domicilio"},
        phonepromotor: {required: "Se requiere de un telefono"}
      },
    });
  if(validFormPromotorNew.form()){
    var formdatanewpromotor = new FormData($("#form-new-promotor")[0]);
    $.ajax({
      url:"promotor/new",
      type:"POST",
      data: formdatanewpromotor,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nuevo promotor');
      },
      success:function(promotor){
        Swal.fire({
          position: 'top-end',
          icon: promotor.status,
          title: promotor.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();
        $("#form-new-promotor")[0].reset();
        $("#nav-register").removeClass('active');
        $("#nav-register").attr('aria-selected', false);
        $("#nav-register-target").removeClass('active show');
        $("#nav-table").addClass('active');
        $("#nav-table").attr('aria-selected', true);
        $("#nav-table-target").addClass('active show');
      },
      error:function(e){
        console.log(e);
      }
    });
  }
});
$("#btnsavepromotorupdate").click(function(e){
  var validFormPromotoredit = $("#form-edit-promotor").validate({
      rules: {
        namepromotoredit: {required: true},
        addresspromotoredit: {required: true},
        phonepromotoredit: {required: true}
      },
      messages:{
        namepromotoredit: {required: "Se requiere el nombre"},
        addresspromotoredit: {required: "Se requiere del domicilio"},
        phonepromotoredit: {required: "Se requiere de un telefono"}
      },
    });
  if(validFormPromotoredit.form()){
    var formdataeditpromotor = new FormData($("#form-edit-promotor")[0]);
    $.ajax({
      url:"promotor/update",
      type:"POST",
      data: formdataeditpromotor,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nuevo promotor');
      },
      success:function(promotor){

        Swal.fire({
          position: 'top-end',
          icon: promotor.status,
          title: promotor.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();
        $("#modaleditpromotor").modal("hide");
      },
      error:function(e){
        console.log(e);
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
