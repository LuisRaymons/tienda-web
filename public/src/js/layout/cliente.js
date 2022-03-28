let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function() {
  tabledoading();
});
const tabledoading = () => {
  return table = $('#cliente-table').DataTable({
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
              url: "cliente/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" },
             { data: "nombrefull" },
             { data: "telefono" },
             { data: "direccion" },
             { data: "cp" },
             { data: "colonia" },
             {
               "data": 'img',
               "render": function (data, type, JsonResultRow, meta) {
                 return '<img src="'+data+'" width="100%" height="90px"/>';
                }
             },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                  var permisouser = userconsultarrol();

                  if(permisouser.typeuser == 'Administrador'){
                    return '<button type="button" id="ButtonEditar" onclick = "editarcliente('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                    '<button type="button" id="Buttondeletes" onclick = "deletecliente('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
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
            filename: 'Cliente',
            text: '<button class="btn btn-success">Exportar cliente a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Cliente',
            text: '<button class="btn btn-info">Exportar cliente a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          //Botón para PDF
          {
            extend: 'print',
            footer: true,
            title: 'Cliente',
            filename: 'Cliente',
            text: '<button class="btn btn-danger">Exportar cliente a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });
}
const editarcliente = (id) => {

  $.ajax({
    url:"cliente/get/" + id,
    beforeSend:function(e){
      loadinproccess(true, "Cargando informacion del cliente seleccionado..");
    },
    success:function(client){
      if(client.code == 200){
        console.log(client);
        var data = client.data;
        if(data != null){
          $("#nameclientedit").val(data.nombre);
          $("#lastnameclientedit").val(data.apellidos);
          $("#phoneclientedit").val(data.telefono);
          $("#addressclientedit").val(data.direccion);
          $("#cpclientedit").val(data.cp);
          $("#imgclienteiconedit").attr("src",data.img);
          $("#idupdateclient").val(data.id);

          // cargar select de colonia
          $("#coloniaclient").empty();
          $("#coloniaclient").append('<option value="0">Seleccione un codigo postal</option>');

          $.ajax({
            url:"api/codigo/postal",
            type:"POST",
            data:{"CP":data.cp},
            success: function(codigospostales){

              if(codigospostales.code == 200){
                var datacp = codigospostales.data;

                datacp.forEach((cps, i) => {

                  if(cps['colonia'] == data.colonia){
                    optioncp = "<option value='" + cps['codigo postal'] + "' selected>"+ cps['colonia'] + "</option>";
                  } else{
                    optioncp = "<option value='" + cps['codigo postal'] + "'>"+ cps['colonia'] + "</option>";
                  }
                  $("#coloniaclientedit").append(optioncp);
                });

              }
            },
            error:function(e){
              console.log("-----------------------------------error----------------------------");
              console.log(e);
            },
            complete:function(e){
              loadinproccess(false, 'Buscando codigo postal');
            }
          })
          $("#modaleditclient").modal("show");
        } else{
          console.log("Entre...");
          /*
          Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'No se pudo recuperar la informacion del cliente seleccionado',
            showConfirmButton: false,
            timer: 2000
          });
          */
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'No se pudo recuperar la informacion del cliente seleccionado',
              showConfirmButton: false
            })
        }

      }
    },
    error:function(e){
      console.log("Error al recuperar la informacion del cliente");
    },
    complete:function(e){
      loadinproccess(false, "Cargando informacion del cliente seleccionado..");
    }
  });
}
const deletecliente = (id) => {
  Swal.fire({
    title: '¿Deseas eliminar al cliente con el id: ' + id + '?',
    showCancelButton: true,
    confirmButtonText: 'Aceptar'
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {

      $.ajax({
        url:"cliente/delete/" + id,
        type:"DELETE",
        beforeSend:function(e){
          loadinproccess(true,"Eliminando cliente");
        },
        success:function(cliente){
          if(cliente.code == 200){
            Swal.fire({
              position: 'top-end',
              icon: cliente.status,
              title: cliente.msm,
              showConfirmButton: false,
              timer: 2000
            });

            tabledoading();
          }
        },
        error:function(e){
          console.log("Error");
        },
        complete:function(e){
          loadinproccess(false,"Eliminando cliente");
        }
      });
    }
  });
}
$("#cpclient").change(function(e){
  let cp = $("#cpclient").val();
  $("#coloniaclient").empty();
  $("#coloniaclient").append('<option value="0">Seleccione un codigo postal</option>');
  if(cp.length < 5 || cp.length > 5){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'El codigo postal deve tener 5 digitos',
      showConfirmButton: false,
      timer: 1500
    })
  } else{
    $.ajax({
      url:"api/codigo/postal",
      type:"POST",
      data:{"CP":cp},
      beforeSend: function(e){
        loadinproccess(true, 'Buscando codigo postal');
      },
      success: function(codigospostales){

        if(codigospostales.code == 200){
          var data = codigospostales.data;

          data.forEach((cps, i) => {
            optioncp = "<option value='" + cps['codigo postal'] + "'>"+ cps['colonia'] + "</option>";
            $("#coloniaclient").append(optioncp);
          });
        }
      },
      error:function(e){
        console.log("-----------------------------------error----------------------------");
        console.log(e);
      },
      complete:function(e){
        loadinproccess(false, 'Buscando codigo postal');
      }
    })
  }

});
$("#cpclientedit").change(function(e){
  let cp = $("#cpclientedit").val();
  $("#coloniaclientedit").empty();
  $("#coloniaclientedit").append('<option value="0">Seleccione un codigo postal</option>');
  if(cp.length < 5 || cp.length > 5){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'El codigo postal deve tener 5 digitos',
      showConfirmButton: false,
      timer: 1500
    })
  } else{
    $.ajax({
      url:"api/codigo/postal",
      type:"POST",
      data:{"CP":cp},
      beforeSend: function(e){
        loadinproccess(true, 'Buscando codigo postal');
      },
      success: function(codigospostales){

        if(codigospostales.code == 200){
          var data = codigospostales.data;

          data.forEach((cps, i) => {
            optioncp = "<option value='" + cps['codigo postal'] + "'>"+ cps['colonia'] + "</option>";
            $("#coloniaclientedit").append(optioncp);
          });
        }
      },
      error:function(e){
        console.log("-----------------------------------error----------------------------");
        console.log(e);
      },
      complete:function(e){
        loadinproccess(false, 'Buscando codigo postal');
      }
    })
  }

});
$("#btnsaveclient").click(function(e){
  var cp = $("#cpclient").val()
  var colonia = $("#coloniaclient option:selected").html();
  // validar input
  var validFormClientNew = $("#form-valid-client-new").validate({
      rules: {
        nameclient: {
          required: true
        },
        lastnameclient: {
          required: true
        },
        phoneclient: {
          required: true
        },
        addressclient: {
          required: true
        },
        cpclient: {
          required: true
        },
        coloniaclient:{
          required: true
        }
      },
      messages:{
        nameclient: {
          required: "Se requiere de un nombre"
        },
        lastnameclient: {
          required: "Se requiere de los apellidos"
        },
        phoneclient: {
          required: "Se requiere de un numero telefonico"
        },
        addressclient: {
          required: "Se requiere de una diireccion"
        },
        cpclient: {
          required: "Se requiere de un codigo postal"
        },
        coloniaclient:{
          required: "Se requiere de una colonia"
        }
      },
    });
  if(validFormClientNew.form()){
    var formdatanewclient = new FormData($("#form-valid-client-new")[0]);
        formdatanewclient.append("coloniacp",colonia);
    $.ajax({
      url:"cliente/new",
      type:"POST",
      data: formdatanewclient,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nuevo cliente');
      },
      success:function(cliente){
        Swal.fire({
          position: 'top-end',
          icon: cliente.status,
          title: cliente.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();
        $("#form-valid-client-new")[0].reset();
        $("#nav-register").removeClass('active');
        $("#nav-register").attr('aria-selected', false);
        $("#nav-register-target").removeClass('active show');
        $("#nav-table").addClass('active');
        $("#nav-table").attr('aria-selected', true);
        $("#nav-table-target").addClass('active show');
      },
      error:function(e){
        console.log("----------------error en la peticion----------------");
        console.log(e)
      }
    });

  }
});
$("#btnsaveclientupdate").click(function(e){
  var validFormClientNew = $("#form-valid-client-editar").validate({
      rules: {
        nameclientedit: {
          required: true
        },
        lastnameclientedit: {
          required: true
        },
        phoneclientedit: {
          required: true
        },
        addressclientedit: {
          required: true
        },
        cpclientedit: {
          required: true
        },
        coloniaclientedit:{
          required: true
        }
      },
      messages:{
        nameclientedit: {
          required: "Se requiere de un nombre"
        },
        lastnameclientedit: {
          required: "Se requiere de los apellidos"
        },
        phoneclientedit: {
          required: "Se requiere de un numero telefonico"
        },
        addressclientedit: {
          required: "Se requiere de una diireccion"
        },
        cpclientedit: {
          required: "Se requiere de un codigo postal"
        },
        coloniaclientedit:{
          required: "Se requiere de una colonia"
        }
      },
    });
  if(validFormClientNew.form()){
    var cp = $("#cpclientedit").val()
    var colonia = $("#coloniaclientedit option:selected").html();

    var formdataeditclient = new FormData($("#form-valid-client-editar")[0]);
        formdataeditclient.append("coloniacp",colonia);
    $.ajax({
      url:"cliente/update",
      type:"POST",
      data: formdataeditclient,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del cliente');
      },
      success:function(cliente){
        Swal.fire({
          position: 'top-end',
          icon: cliente.status,
          title: cliente.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();

        $("#modaleditclient").modal('hide')
        $("#nav-register").removeClass('active');
        $("#nav-register").attr('aria-selected', false);
        $("#nav-register-target").removeClass('active show');
        $("#nav-table").addClass('active');
        $("#nav-table").attr('aria-selected', true);
        $("#nav-table-target").addClass('active show');
      },
      error:function(e){
        console.log("----------------error en la peticion----------------");
        console.log(e)
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
