let user = $("#iduser").val();
let typeuser = $("#typeuser").val();
let emailuser = $("#emailuser").val();
let tokenUser = $("#token_user").val();
var idproductos = 0;

$(document).ready(function(e){
  tabledoading();
  loadingproducts($("#product"),"Seleccione un producto",0);
});

const tabledoading = () =>{
  return table = $('#producto-precio-table').DataTable({
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
              url: "productoprecios/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" , "orderable": true },
             { data: "nombre" , "orderable": true },
             {
               "data": 'img',
               "render": function (data, type, JsonResultRow, meta) {
                 return '<img src="'+data+'" width="100%" height="90px"/>';
                }
             },
             { data: "precio", "orderable": true},
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();
                 if(permisosuser.typeuser == 'Administrador'){
                   return '<button type="button" id="ButtonEditar" onclick = "editarproductoprecio('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                   '<button type="button" id="Buttondeletes" onclick = "deleteproductoprecio('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
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
            filename: 'Productos',
            text: '<button class="btn btn-success">Exportar productos a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Productos',
            text: '<button class="btn btn-info">Exportar productos a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          //Botón para PDF
          {
            extend: 'print',
            footer: true,
            title: 'Archivo PDF',
            filename: 'Productos',
            text: '<button class="btn btn-danger">Exportar productos a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });
}
const userconsultarrol = () => {
  var arrayuser = new Array({
    "user" : user,
    "typeuser" : typeuser
  });
  return arrayuser[0];
}
const loadingproducts = (type,cadena) => {
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'productoprecios/missing',
        dataType: 'json',
        type: 'GET',
        processResults({
           data
        }) {
           return {
              results: $.map(data, function(item) {
                 return {
                    text: item.nombre,
                    id: item.id,
                 }
              })
           }
        }
     }
  });
}
const loadingproductsprecio = (type,cadena,id) => {
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'productoprecios/exists',
        dataType: 'json',
        type: 'GET',
        processResults({
           data
        }) {
           return {
              results: $.map(data, function(item) {
                selecteditems = (parseInt(item.id) == parseInt(id)) ? true : false;
                 return {
                    text: item.nombre,
                    id: item.id,
                    selected: selecteditems
                 }
              })
           }
        }
     }
  });
}
$("#btnsavepriceproduct").click(function(e){

  var validpriceproduct = $("#form-new-product-price").validate({
      rules: {
        product:{required: true},
        precioproduct:{required: true}
      },
      messages:{
        product:{required: "Seleccione un producto"},
        precioproduct:{required: "Ingrese el precio del producto"}
      }
  });
  if(validpriceproduct.form()){
    (async () =>{
    const { value: email } = await Swal.fire({
      title: 'Ingresa tu correo',
      input: 'email',
      inputLabel: 'Para continuar ingresa tu correo',
      inputPlaceholder: 'Enter your email address'
    });

    if (email) {
      if(email == emailuser){
        var formdatanewprecioproducto = new FormData($("#form-new-product-price")[0]);
        $.ajax({
          url: "productoprecios/new",
          type: "POST",
          data:formdatanewprecioproducto,
          processData: false,
          contentType: false,
          beforeSend:function(e){
            loadinproccess(true,"Creando categoria");
          },
          success:function(precioproduct){
            Swal.fire({
              position: 'top-end',
              icon: precioproduct.status,
              title: precioproduct.msm,
              showConfirmButton: false,
              timer: 2000
            });

            if(precioproduct.code == 200){
              $("#namenewcategoria").val("");
              $("#nav-register").removeClass('active');
              $("#nav-register").attr('aria-selected', false);
              $("#nav-register-target").removeClass('active show');
              $("#nav-table").addClass('active');
              $("#nav-table").attr('aria-selected', true);
              $("#nav-table-target").addClass('active show');
              tabledoading();
            }
          }, error:function(e){
            console.log("Error");
          },
          complete:function(e){
            loadinproccess(false,"Creando categoria");
          }
        });
      } else{
        Swal.fire({
          position: 'top-end',
          icon: 'warning',
          title: 'No tienes permisos para modificar el precio de productos',
          showConfirmButton: false,
          timer: 2000
        });
      }
    } else{
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'No tienes permisos para modificar el precio de productos',
        showConfirmButton: false,
        timer: 2000
      });
    }
    })()
  }

});
$("#btneditsaveproductprecio").click(function(e){

  var validpriceproductedit = $("#form-edit-product-precio").validate({
      rules: {
        product:{required: true},
        precioproduct:{required: true}
      },
      messages:{
        product:{required: "Seleccione un producto"},
        precioproduct:{required: "Ingrese el precio del producto"}
      }
  });
  if(validpriceproductedit.form()){
    $("#staticmodaleditprecioproduct").modal("hide");
    (async () =>{
    const { value: emailedit } = await Swal.fire({
      title: 'Ingresa tu correo',
      input: 'email',
      inputLabel: 'Para continuar ingresa tu correo',
      inputPlaceholder: 'Enter your email address'
    });

    if (emailedit) {
      if(emailedit == emailuser){
        var dataproduct = new FormData($("#form-new-product-price")[0]);
        dataproduct.append("id",idproductos);
        dataproduct.append("precioproduct",$("#precioedit").val());
        $.ajax({
          url: "productoprecios/update",
          type: "POST",
          data:dataproduct,
          processData: false,
          contentType: false,
          beforeSend:function(e){
            loadinproccess(true,"Creando categoria");
          },
          success:function(precioproduct){
            Swal.fire({
              position: 'top-end',
              icon: precioproduct.status,
              title: precioproduct.msm,
              showConfirmButton: false,
              timer: 2000
            });

            if(precioproduct.code == 200){
              $("#namenewcategoria").val("");
              $("#nav-register").removeClass('active');
              $("#nav-register").attr('aria-selected', false);
              $("#nav-register-target").removeClass('active show');
              $("#nav-table").addClass('active');
              $("#nav-table").attr('aria-selected', true);
              $("#nav-table-target").addClass('active show');
              $("#staticmodaleditprecioproduct").modal("hide");
              tabledoading();
            } else{
              $("#staticmodaleditprecioproduct").modal("show");
            }
          }, error:function(e){
            console.log("Error");
            $("#staticmodaleditprecioproduct").modal("show");
          },
          complete:function(e){
            loadinproccess(false,"Creando categoria");
          }
        });
      } else{
        Swal.fire({
          position: 'top-end',
          icon: 'warning',
          title: 'No tienes permisos para modificar el precio de productos',
          showConfirmButton: false,
          timer: 2000
        });
      }
    } else{
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'No tienes permisos para modificar el precio de productos',
        showConfirmButton: false,
        timer: 2000
      });
    }
    })()

  }

});
const editarproductoprecio = (id) =>{

  formdata = new FormData();
  formdata.append('id',id);

  $.ajax({
    url: "productoprecios/get/one",
    type: "POST",
    data: formdata,
    processData: false,
    contentType: false,
    beforeSend:function(e){
      loadinproccess(true,"Cargando informacion dell precio de producto");
    },
    success:function(precioproduct){
      if(precioproduct.code == 200){
        var data = precioproduct.data;
        $("#staticmodaleditprecioproduct").modal("show");
        $("#productedit").val(data['nombre']);
        $("#precioedit").val(data['precio']);
        idproductos = data['idproductprecio'];
      } else{

        Swal.fire({
          position: 'top-end',
          icon: precioproduct.status,
          title: precioproduct.msm,
          showConfirmButton: false,
          timer: 2000
        });
      }

    }, error:function(e){
      console.log("Error");
    },
    complete:function(e){
      loadinproccess(false,"Cargando informacion dell precio de producto");
    }
  });
}
const deleteproductoprecio = (id) =>{
  (async () =>{
  const { value: emailedit } = await Swal.fire({
    title: 'Ingresa tu correo',
    input: 'email',
    inputLabel: 'Para continuar con la eliminacion ingresa tu correo',
    inputPlaceholder: 'Ingresa tu correo electronico'
  });
  console.log(emailedit);

  if (emailedit != '') {
    if(emailedit == emailuser){
      var dataproduct = new FormData();
      dataproduct.append("id",id);
      $.ajax({
        url: "productoprecios/delete/" + id,
        type: "delete",
        data:dataproduct,
        processData: false,
        contentType: false,
        beforeSend:function(e){
          loadinproccess(true,"Cargando precio de productos");
        },
        success:function(precioproduct){
          Swal.fire({
            position: 'top-end',
            icon: precioproduct.status,
            title: precioproduct.msm,
            showConfirmButton: false,
            timer: 2000
          });

          if(precioproduct.code == 200){
            tabledoading();
          }
        }, error:function(e){
          console.log("Error");
        },
        complete:function(e){
          loadinproccess(false,"Creando categoria");
        }
      });
    } else{
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'No tienes permisos para modificar el precio de productos',
        showConfirmButton: false,
        timer: 2000
      });
    }
  } else{
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'No tienes permisos para modificar el precio de productos',
      showConfirmButton: false,
      timer: 2000
    });
  }
  })()
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
        showConfirmButton: false
        /*
        onClose: () => {
            clearInterval(timerInterval)
        }
        */
    });
}
