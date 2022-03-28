let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function() {

    tabledoading();
});

const tabledoading = () => {
  return table = $('#usuario-table').DataTable({
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
              url: "usuario/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" },
             { data: "name" },
             { data: "email" },
             {data: "type"},
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
                    return '<button type="button" id="ButtonEditar" onclick = "editarusuario('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                    '<button type="button" id="Buttondeletes" onclick = "deleteusuario('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
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
            filename: 'Usuarios',
            text: '<button class="btn btn-success">Exportar usuarios a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Usuarios',
            text: '<button class="btn btn-info">Exportar usuarios a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          //Botón para PDF
          {
            extend: 'print',
            footer: true,
            title: 'Lista de Usuarios',
            filename: 'Usuarios',
            text: '<button class="btn btn-danger">Exportar usuarios a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });

}
const editarusuario = (id) =>{
  $.ajax({
    url:"usuario/get/" + id,
    type:"GET",
    beforeSend:function(e){
      loadinproccess(true, "Cargando informacion del usuario seleccionado..");
    },
    success:function(user){
      if(user.code == 200){
        var data = user.data;
        $("#idupdateuser").val(data.id);
        $("#nameuseredit").val(data.name);
        $("#emailuseredit").val(data.email);
        $("#typeuseredit option[value="+ data.type +"]").attr("selected",true);
        $("#user-icon-update").attr("src", data.img);
        $("#modaledituser").modal("show");
      }
    },
    error:function(e){
      console.log("----------error------");
    },
    complete:function(e){
      loadinproccess(false, "Cargando informacion del usuario seleccionado..");
    }
  });
}
const deleteusuario = (id) =>{
  var userid = $("#iduser").val();

  if(userid != ''){
    if(userid != id){
      Swal.fire({
        title: '¿Quieres a eliminiar al usuario con el id: ' + id,
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"usuario/delete/" + id,
            type:"DELETE",
            beforeSend:function(e){
              loadinproccess(true,"Elimando usuario");
            },
            success:function(user){
              if(user.code == 200){
                Swal.fire({
                  position: 'top-end',
                  icon: user.status,
                  title: user.msm,
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
              loadinproccess(false,"Elimando usuario");
            }
          });
        }
      });




    } else{
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'No te puedes auto eliminar',
        showConfirmButton: false,
        timer: 2000
      });
    }
  }
}
$("#btnsaveuser").click(function(e){
  var validFormusernew = $("#form-new-user").validate({
      rules: {
        nameuser: {
          required: true
        },
        emailuser: {
          required: true,
          email: true
        },
        passworduser: {
          required: true,
          minlength: 8
        },
        confirmpassworduser: {
          required: true,
          minlength: 8,
          equalTo:"#passworduser"
        }
      },
      messages:{
        nameuser: {
          required: "Se requiere de un nombre"
        },
        emailuser: {
          required: "Se requiere de un correo",
          email: "El formato de tu correo no es correcto, ejemplo (elcorreo@hotmail.com)"
        },
        passworduser: {
          required: "Escribe tu contraseña para este usuario",
          minlength: "Se requiere de 8 caracteres como minimo",
        },
        confirmpassworduser: {
          required: "Confirma la contraseña para poder registrar a este usuario",
          minlength: "Se requiere de 8 caracteres como minimo",
          equalTo:"LAs contraseñas no cohiciden"
        }
      },
    });
  var typeuser = $("#typeuser option:selected").html();

    if(validFormusernew.form()){
      var formdatanewuser = new FormData($("#form-new-user")[0]);
          formdatanewuser.append("typeuser",typeuser)
      $.ajax({
        url:"usuario/new",
        type:"POST",
        data: formdatanewuser,
        processData: false,
        contentType: false,
        beforeSend:function(e){
          loadinproccess(true, 'Guardando informacion del nuevo usuario');
        },
        success:function(user){
          Swal.fire({
            position: 'top-end',
            icon: user.status,
            title: user.msm,
            showConfirmButton: false,
            timer: 2000
          });

          tabledoading();
          $("#form-new-user")[0].reset();
          $("#nav-register").removeClass('active');
          $("#nav-register").attr('aria-selected', false);
          $("#nav-register-target").removeClass('active show');
          $("#nav-table").addClass('active');
          $("#nav-table").attr('aria-selected', true);
          $("#nav-table-target").addClass('active show');

        }, error:function(e){
          console.log(e);
        }
      });
    }
});
$("#btnsaveuseredit").click(function(e){
  var validFormusernew = $("#form-edit-user").validate({
      rules: {
        nameuser: {
          required: true
        },
        emailuser: {
          email: true
        },
        passworduser: {
          minlength: 8
        },
        confirmpassworduser: {
          minlength: 8,
          equalTo:"#passworduseredit"
        }
      },
      messages:{
        nameuser: {
          required: "Se requiere de un nombre"
        },
        emailuser: {
          email: "El formato de tu correo no es correcto, ejemplo (elcorreo@hotmail.com)"
        },
        passworduser: {
          minlength: "Se requiere de 8 caracteres como minimo",
        },
        confirmpassworduser: {
          minlength: "Se requiere de 8 caracteres como minimo",
          equalTo:"LAs contraseñas no cohiciden"
        }
      },
    });
    if(validFormusernew.form()){
      var formdataupdateuser = new FormData($("#form-edit-user")[0]);

      $.ajax({
        url:"usuario/update",
        type:"POST",
        data: formdataupdateuser,
        processData: false,
        contentType: false,
        beforeSend:function(e){
          loadinproccess(true, 'Guardando informacion del usuario');
        },
        success:function(user){
          if(user.code == 200){
            Swal.fire({
              position: 'top-end',
              icon: user.status,
              title: user.msm,
              showConfirmButton: false,
              timer: 2000
            })

            $("#modaledituser").modal('hide');
            tabledoading();
          }
        },
        error:function(e){
          console.log("----error----");
        },
        complete:function(e){
          loadinproccess(false, 'Guardando informacion del usuario');
        }
      });
    }
});
$("#emailuseredit").on('keyup',function(e){
  var correo = $("#emailuseredit").val();
  var data = {"email":correo};
  $.ajax({
    url:"usuario/disponibilidad/email",
    type:"post",
    data: data,
    success:function(user){
      if(user.code == 200 && user.data > 0){
        $("#error-correo-edit").css("display","block");
      } else{
        $("#error-correo-edit").css("display","none");
      }
    },
    error:function(e){
      console.log("Error en consultar correo");
    }
  });
});
$("#checkchangepassword").change(function(e){
  if($('#checkchangepassword').prop('checked')){
    $("#div-passwords").css("display","block");
  } else{
    $("#div-passwords").css("display","none");
    $("#passworduseredit").val('');
    $("#confirmpassworduseredit").val('');
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
