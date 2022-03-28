let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function(e){
  tabledoading();
  loadingcategoria();
});
const tabledoading = () => {
  return table = $('#product-table').DataTable({
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
              url: "producto/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" , "orderable": true },
             { data: "nombre" , "orderable": true },
             { data: "descripcion" , "orderable": true },
             {
               "data":"precioPorKilo",
               "render": function (data, type, JsonResultRow, meta) {
                 if(data == 'true'){
                   return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked disabled style="margin: auto;">';
                 } else{
                   return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" disabled style="margin: auto;">';
                 }
                }
             },
             {
               "data": 'img',
               "render": function (data, type, JsonResultRow, meta) {
                 return '<img src="'+data+'" width="100%" height="90px"/>';
                }
             },
             { data: "categoria", "orderable": true},
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();
                 if(permisosuser.typeuser == 'Administrador'){
                   return '<button type="button" id="ButtonEditar" onclick = "editarproducto('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>' +
                   '<button type="button" id="Buttondeletes" onclick = "deleteproducto('+data+')" class="delete edit-modal btn btn-danger botondelete"><span class="fa fa-trash-alt"></span><span class="hidden-xs"> Eliminar</span></button>';
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
const editarproducto = (id) => {

  $.ajax({
    url:"producto/get/" + id,
    type:"GET",
    beforeSend:function(e){
      loadinproccess(true,"Cargando informacion del producto");
    },
    success:function(producto){

      if(producto.code == 200){
        var data = producto.data;
        $("#idproductedit").val(data.id);
        $("#nameproductedit").val(data.nombre);
        $("#descriptionproductedit").val(data.descripcion);
        if(data.precioPorKilo == "true"){
          $("#pricekiloproductedit").prop('checked', true);
        } else{
          $("#pricekiloproductedit").prop('checked', false);
        }
        $("#imgproducteditsrc").attr("src",data.img);
        loadingcategoriaupdate(data.id_categoria);
      }
    },
    error:function(e){
      console.log("Error");
    },
    complete:function(e){
      loadinproccess(false,"Cargando informacion del producto");
    }
  });

  $("#modaleditproduct").modal("show");
}
const deleteproducto = (id) => {
    Swal.fire({
        title: 'Quieres eliminar al producto con el id: ' + id,
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"producto/delete/" + id,
            type:"DELETE",
            beforeSend:function(e){
              loadinproccess(true,"Eliminando producto");
            },
            success:function(product){
              if(product.code == 200){
                Swal.fire({
                  position: 'top-end',
                  icon: product.status,
                  title: product.msm,
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
              loadinproccess(false,"Eliminando producto");
            }
          });
        }
      })






}
$("#btnsaveproduct").click(function(e){
  var validFormClientNew = $("#form-new-product").validate({
      rules: {
        nameproduct:{required: true},
        descriptionproduct:{required: true},
        categoriaProduct:{required: true}
      },
      messages:{
        nameproduct:{required: "Se requiere el nombre del producto"},
        descriptionproduct:{required: "Se requiere de una pequeña descripcion del producto"},
        categoriaProduct:{required: "Seleccione una categoria de producto"}

      },
  });
  if(validFormClientNew.form()){
    var formdatanewproduct = new FormData($("#form-new-product")[0]);
        formdatanewproduct.append("preciocatekilo",$("#pricekiloproduct").prop('checked'));
    $.ajax({
      url:"producto/new",
      type:"POST",
      data: formdatanewproduct,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nuevo producto');
      },
      success:function(product){
        Swal.fire({
          position: 'top-end',
          icon: product.status,
          title: product.msm,
          showConfirmButton: false,
          timer: 2000
        });
        $("#form-new-product")[0].reset();
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
$("#btnsaveproductedit").click(function(e){
  var validFormClientEdit = $("#form-edit-product").validate({
      rules: {
        nameproductedit:{required: true},
        descriptionproductedit:{required: true},
        categoriaProductedit:{required: true}
      },
      messages:{
        nameproductedit:{required: "Se requiere el nombre del producto"},
        descriptionproductedit:{required: "Se requiere de una pequeña descripcion del producto"},
        categoriaProductedit:{required: "Seleccione una categoria de producto"}

      },
  });
  if(validFormClientEdit.form()){
    var formdataeditproduct = new FormData($("#form-edit-product")[0]);
        formdataeditproduct.append("preciocatekilo",$("#pricekiloproduct").prop('checked'));
    $.ajax({
      url:"producto/update",
      type:"POST",
      data: formdataeditproduct,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del producto');
      },
      success:function(product){
        $("#modaleditproduct").modal('hide');
        Swal.fire({
          position: 'top-end',
          icon: product.status,
          title: product.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();
      }, error:function(e){
        console.log(e);
      }
    });
  }
});
const loadingcategoria = () =>{
  $("#categoriaProduct").empty();
  $("#categoriaProduct").append('<option value="0">Seleccione una categoria</option>');
  $.ajax({
    url:"categoria/producto",
    type:"GET",
    success:function(categoria){

      if(categoria['code'] == 200){
        var data = categoria['data'];

        data.forEach((c, i) => {
          optioncategoria = "<option value='" + c['id'] + "'>"+ c['nombre'] + "</option>";
          $("#categoriaProduct").append(optioncategoria);
        });

      }
    },
    error:function(e){
      console.log("----------------------error-----------------");
      console.log(e);
    }
  });
}
const loadingcategoriaupdate = (idselect) =>{
  $("#categoriaProductedit").empty();
  $("#categoriaProductedit").append('<option value="0">Seleccione una categoria</option>');
  $.ajax({
    url:"categoria/producto",
    type:"GET",
    success:function(categoria){

      if(categoria['code'] == 200){
        var data = categoria['data'];

        data.forEach((c, i) => {

          if(idselect == c['id']){
            optioncategoria = "<option value='" + c['id'] + "' selected>"+ c['nombre'] + "</option>";
          } else{
            optioncategoria = "<option value='" + c['id'] + "'>"+ c['nombre'] + "</option>";
          }
          $("#categoriaProductedit").append(optioncategoria);
        });

      }
    },
    error:function(e){
      console.log("----------------------error-----------------");
      console.log(e);
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
