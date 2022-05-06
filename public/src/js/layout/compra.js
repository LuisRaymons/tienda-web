let user = $("#iduser").val();
let typeuser = $("#typeuser").val();

$(document).ready(function(e){
  tabledoading();
  loadingproduct($('#productnewcompra'),'Seleccione un producto');
  loadingpromotor($('#promotornewcompra'),'Seleccione un promotor');

});
const tabledoading = () =>{
  return table = $('#compra-table').DataTable({
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
              url: "compra/get/table",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "id" },
             { data: "folio" },
             { data: "cantidad_stock" },
             { data: "precio_total" },
             { data: "almacen" },
             { data: "namepromo" },
             { data: "nameproduct" },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 permisosuser = userconsultarrol();

                 if(permisosuser.typeuser == 'Administrador'){
                   return '<button type="button" id="ButtonEditar" onclick = "editarfunction('+data+')" class="editar edit-modal btn btn-warning botonEditar"><span class="fa fa-edit"></span><span class="hidden-xs"> Editar</span></button><span>     </span>';
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
const editarfunction = (id) =>{
  $.ajax({
    url:"compra/get/" + id,
    type:"GET",
    beforeSend:function(e){
      loadinproccess(true,"Buscando informacion de la compra");
    },
    success:function(compra){

      if(compra.code == 200){
        var data = compra.data;

        $("#idupdatecompra").val(data.id);
        $("#idalmacenedit").val(data.id_almacen);
        $("#imgsrccompraedit").attr("src",data.img);
        $("#modaleditcompra").modal("show");
        $("#stockcompraedit").val(data.cantidad_stock);
        $("#preciocompraedit").val(data.precio_total);

        $('#producteditcompra').val(data.id_producto);
        $('#promotoreditcompra').val(data.id_promotor);

        // Cargar producto
        $.ajax({
          url:"producto/get/" + data.id_producto,
          type:"GET",
          success:function(producto){
            if(producto.code == 200){
              loadingproduct($('#producteditcompra'),producto.data.nombre);
            }

          },
          error:function(e){
            console.log("Error al cargar datos del producto");
          }
        });
        // Cargar promotor
        $.ajax({
          url:"promotor/get/" + data.id_promotor,
          type:"GET",
          success:function(promotor){
            if(promotor.code == 200){
              loadingpromotor($('#promotoreditcompra'),promotor.data.nombre);
            }
          },
          error:function(e){
            console.log("Error al cargar datos del promotor");
          }
        });
      }
    },
    error:function(e){
      console.log("---error---");
    },
    complete:function(e){
      loadinproccess(false,"Buscando informacion de la compra");
    }
  });
}
const loadingproduct = (type,cadena) =>{
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'producto/all',
        dataType: 'json',
        type: 'GET',
        processResults({
           data
        }) {
           return {
              results: $.map(data, function(item) {
                 return {
                    text: item.nombre,  //'<span><img src="' + item.img + '" class="img-flag"/>' + item.nombre + '</span>',
                    id: item.id,
                 }
              })
           }
        }
     }
  });
}
const loadingpromotor = (type, cadena) =>{
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'promotor/all',
        dataType: 'json',
        type: 'GET',
        processResults({
           data
        }) {
           return {
              results: $.map(data, function(item) {
                 return {
                    text: item.nombre,  //'<span><img src="' + item.img + '" class="img-flag"/>' + item.nombre + '</span>',
                    id: item.id,
                 }
              })
           }
        }
     }
  });
}

$("#btnsavecompra").click(function(e){
  var validFormcompraNew = $("#form-new-compra").validate({
      rules: {
        stockcompra:{required: true},
        preciocompra:{required: true},
        productnewcompra:{required: true},
        promotornewcompra:{required: true}
      },
      messages:{
        stockcompra:{required: "Seleccione la cantidad de compra que se hiso por producto"},
        preciocompra:{required: "Seleccione el costo total de la compra"},
        productnewcompra:{required: "Seleccione el producto de la compra"},
        promotornewcompra:{required: "Seleccione el promotor de la compra"}
      },
    });
  if(validFormcompraNew.form()){
    var validFormcompraNew = new FormData($("#form-new-compra")[0]);

    $.ajax({
      url:"compra/new",
      type:"POST",
      data:validFormcompraNew,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nueva compra');
      },
      success:function(compra){
        Swal.fire({
          position: 'top-end',
          icon: compra.status,
          title: compra.msm,
          showConfirmButton: false,
          timer: 2000
        });
        tabledoading();
        $("#form-new-compra")[0].reset();
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
$("#btnsavecompraedit").click(function(e){

  var validFormcompraedit = $("#form-edit-compra").validate({
      rules: {
        stockcompraedit:{required: true},
        preciocompraedit:{required: true}
      },
      messages:{
        stockcompraedit:{required: "Seleccione la cantidad de compra que se hiso por producto"},
        preciocompraedit:{required: "Seleccione el costo total de la compra"}
      },
    });
  if(validFormcompraedit.form()){
    var validFormcompraedit = new FormData($("#form-edit-compra")[0]);

    $.ajax({
      url:"compra/update",
      type:"POST",
      data:validFormcompraedit,
      processData: false,
      contentType: false,
      beforeSend:function(e){
        loadinproccess(true, 'Guardando informacion del nueva compra');
      },
      success:function(compra){
        console.log(compra);
        Swal.fire({
          position: 'top-end',
          icon: compra.status,
          title: compra.msm,
          showConfirmButton: false,
          timer: 2000
        });
        $("#modaleditcompra").modal("hide");
        tabledoading();
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
