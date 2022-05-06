let user = $("#iduser").val();
let typeuser = $("#typeuser").val();
let tokenuser = $("#token_user").val();
let products =  new Array();
let pagototal = 0.00;
let pagototalefectivo  = 0.00;
let cantidadproductselect = 0;
let productid = "";
let productname = "";
let clienteselect = 1;
let pagoselect = 1;

$(document).ready(function(e){
  tabledoading();
  loadingclient($("#clientitems"),"Seleccione cliente");
  loadingproducts($("#productitems"),"Seleccione un producto");
  loadingtypepay($("#typepay"),"Seleccione el metodo de pago");
});

const tabledoading = () =>{
  return table = $('#venta-table').DataTable({
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
          "paging": true,
          lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 100, "All"] ],
          pagingType: "full_numbers",
          pageLength: 25,
          dom: 'lfrtipB',
          ajax: {
              url: "venta/get/table" + "?iduser=1",
              type: "GET"
          },
         serverSide: true,
         columns: [
             { data: "factura" },
             { data: "precio_total" },
             { data: "id_pago" },
             { data: "id_cliente" },
             {
               "data":"id",
               "render": function (data, type, JsonResultRow, meta) {
                 var permisouser = userconsultarrol();

                 if(permisouser.typeuser == 'Administrador'){
                          return '<button type="button" id="ButtonEditar" onclick = "detailfunction('+data+')" class="editar edit-modal btn btn-success botonEditar">'+
                                    '<span class="fa fa-ballot"></span><span class="hidden-xs"> Detalle</span></button><span></span>' +
                                 '<button type="button" id="Buttoneditar" onclick = "editarfunction('+data+')" class="edit edit-modal btn btn-warning botonEditar">'+
                                    '<span class="fa fa-edit"></span><span class="hidden-xs"> editar</span></button>';
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
            title: 'Ventas',
            filename: 'Compras',
            text: '<button class="btn btn-success">Exportar ventas a Excel <i class="fas fa-file-excel"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Archivo CSV',
            filename: 'Ventas',
            text: '<button class="btn btn-info">Exportar ventas a CSV <i class="fas fa-file-csv"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            footer: true,
            title: 'Lista de Compras',
            filename: 'Ventas',
            text: '<button class="btn btn-danger">Exportar ventas a PDF <i class="far fa-file-pdf"></i></button>',
            exportOptions: {
                columns: ':not(.notexport)'
            }
          }
        ]
  });
}
const loadingclient = (type,cadena) =>{
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'cliente/all',
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
const loadingproducts = (type,cadena) => {
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
const loadingtypepay = (type,cadena) =>{
  type.select2({
     placeholder: cadena,
     width: '100%',
     theme: "classic",
     ajax: {
        url: 'api/get/pago',
        dataType: 'json',
        type: 'GET',
        processResults({
           data
        }) {
           return {
              results: $.map(data, function(item) {
                 return {
                    text: item.name,  //'<span><img src="' + item.img + '" class="img-flag"/>' + item.nombre + '</span>',
                    id: item.id,
                 }
              })
           }
        }
     }
  })
}
const addproduct = (id,nombre,cantidad) =>{
  products.push({"id":id,"name":nombre,"acount":cantidad});

  var hash = {};
  products = products.filter(function(current) {
    var exists = !hash[current.id];
    hash[current.id] = true;
    return exists;
  });
}
const buildtabledetail = () => {

  var tr = "";
  var total = 0;

  if(products.length > 0){
    products.forEach((product, i) => {
      console.log(product);
      var formprecio = new FormData();
          formprecio.append('api_token',tokenuser);
          formprecio.append('id',product.id);

      $.ajax({
        url:"productoprecios/get/one",
        type:"POST",
        processData: false,
        contentType: false,
        dataType: "json",
        data:formprecio,
        success:function(precio){

          if(precio.code == 200){
            var data = precio.data;
            var preciototal = 0;

            if(data != undefined){
              preciototal = (data.precio) * (product.acount);
              pagototal = preciototal;

              $("#detailventabody").empty();
              total = total + preciototal;
              tr += "<tr>";
              tr += "<td width='30%'> <input type='number' class='form-control'  value='" + product.acount +"' onchange = 'cambiaitemsproduct(this.value, " + product.id + ")'></td>";
              tr += "<td width='30%'>" + product.name +"</td>";
              tr += "<td width='30%'>" + "$" + preciototal.toFixed(2) +"</td>";
              tr += "<td width='20%'>" +
                      "<i title='Eliminar producto' class='far fa-trash-alt' onclick='deleteproductdetail("+  product.id + ")' style='cursor: pointer;'></i>" +
                    "</td>"
              tr += "</tr>";

              $("#detailventabody").append(tr);
              $("#precioTotaldetail").css("display","block");
              $("#precioTotaldetail").text("$" + total.toFixed(2));
              $("#pagototalcobrar").val(total.toFixed(2)); // pago en efectivo

            } else{
              Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'El precio de este producto no se encuentra en la base de datos',
                text:'Llame a su administrador, para agregar el precio de este producto',
                showConfirmButton: false,
                timer: 5000
              });
            }
          }
        },
        error:function(e){
          console.log("Error");
        }
      });
    });

  } else{
    $("#detailventabody").empty();
    pagototal = 0.00;
    $("#precioTotaldetail").css("display","none");
  }
}
const deleteproductdetail = (id,cantidad,nombre) => {
  var datosfilter = new Array();

  products.forEach((product, i) => {
    countproduct = products.length;
    if(parseInt(product.id) != parseInt(id)){
      datosfilter.push({"id": product.id, "name": product.name, "acount": product.acount});
    }
  });
  products.splice(0,countproduct);
  products = datosfilter;
  buildtabledetail();
}
const cambiaitemsproduct = (cantidaditems,id) =>{
  var datosfilter = new Array();

  products.forEach((product, i) => {
    countproduct = products.length;

    if(parseInt(product.id) == parseInt(id)){
      datosfilter.push({"id": id, "name": product.name, "acount": cantidaditems});
    } else{
      datosfilter.push({"id":  parseInt(product.id), "name": product.name, "acount": product.acount});
    }
  });
  products.splice(0,countproduct);
  products = datosfilter;
  buildtabledetail();
}
const insertVenta = ()  => {

  if(products.length > 0){
    Swal.fire({
      title: '¡Confirmar compra!',
      showDenyButton: true,
      confirmButtonText: 'Si',
      denyButtonText: `No`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        var datasource = new FormData();
            datasource.append("pagototal",pagototalefectivo);
            datasource.append("datosventa",JSON.stringify(products));
            datasource.append("idclient",clienteselect);
            datasource.append("iduser",$("#iduser").val());
            datasource.append("idpago",pagoselect);
        $.ajax({
          url:"venta/new",
          type:"POST",
          processData: false,
          contentType: false,
          dataType: "json",
          data:datasource,
          beforeSend:function(e){
            loadinproccess(true, "Guardando venta, espere un momento porfavor");
          },
          success:function(venta){
            Swal.fire({
              position: 'top-end',
              icon: venta.data.status,
              title: venta.data,
              showConfirmButton: false,
              timer: 1500
            });

            if(venta.code == 200){
              createticketcompra();
              $("#modalPagoRecibe").modal("hide");
              $("#modalventa").modal("hide");
              products =  new Array();
              pagototal = 0.00;
              pagototalefectivo  = 0.00;
              cantidadproductselect = 0;
              productid = "";
              productname = "";
              clienteselect = 1;
              pagoselect = 1;

              $("#detailventabody").empty();
              $("#precioTotaldetail").css("display","none");
              $("#idpago").val("");
              $("#pagototalcobrar").val("");
              $("#cambioventabyefectivo").val("");
              //$("#form-venta-items")[0].reset();
              $("#clientitems").val("");
              $("#productitems").val("");
              $("#typepay").val("");
              loadingclient($("#clientitems"),"Seleccione cliente");
              loadingproducts($("#productitems"),"Seleccione un producto");
              loadingtypepay($("#typepay"),"Seleccione el metodo de pago");
              tabledoading();
            }
          },
          error:function(e){
            console.log(e);
          },
          complete:function(e){
            loadinproccess(false, "Guardando venta, espere un momento porfavor");
          }
        });
      }
    });

  } else{
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'No se encontraron productos en el carrito',
      showConfirmButton: false,
      timer: 1500
    });
  }
}
const createticketcompra = () =>{
  console.log("------------------Creando ticket de compra------------------");
}
const userconsultarrol = () => {
  var arrayuser = new Array({
    "user" : user,
    "typeuser" : typeuser
  });
  return arrayuser[0];
}
const detailfunction = (id) =>{
  $.ajax({
    url:"venta/detalle/" + id,
    beforeSend:function(e){
      loadinproccess(true,'Cargando informacion del cliente');
    },
    success:function(venta){
      if(venta.code == 200){
        var data = venta.data;

        var tableheader = "";
        $("#tablecontainer").empty();

        $("#facturaid").text(data[0]['factura']);
        $("#clienteventa").text(data[0]['cliente']);
        $("#userventa").text(data[0]['usuario']);
        $("#pagoventa").text(data[0]['tipopago'])

        if(data.length > 0){
          tableheader += "<table WIDTH='100%' BORDER><thead><tr BGCOLOR='blue' id='idtableventadetail'><th>cantidad</th><th>producto</th></tr></thead><tbody>";
          data.forEach((v, i) => {
            tableheader += "<tr class='center-text'>";
            tableheader += "<td>" + v['cantidad'] + "</td>";
            tableheader += "<td>" + v['producto'] + "</td>";
            tableheader += "</tr>";
          });
          tableheader += "</tbody></table>";
          $("#tablecontainer").append(tableheader);
        }

        $("#staticmodaldetalleventa").modal("show");
      }
    },
    error:function(e){
      console.error("error en el proceso de consulta de venta");
    },
    complete:function(c){
      loadinproccess(false,'');
    }
  });
}
const editarfunction = (id) =>{
  $.ajax({
    url:"venta/detalle/" + id,
    beforeSend:function(e){
      loadinproccess(true,'Cargando informacion del cliente');
    },
    success:function(venta){
      if(venta.code == 200){
        var data = venta.data;
        loadingclient($("#clientitemsedit"),"Seleccione cliente");
        loadingproducts($("#productitemsedit"),"Seleccione un producto");
        loadingtypepay($("#typepayedit"),"Seleccione el metodo de pago");

        var table = "";
        data.forEach((v, i) => {
          console.log(v);
          table += "<tr>";
          //tr += "<td width='30%'> <input type='number' class='form-control'  value='" + product.acount +"' onchange = 'cambiaitemsproduct(this.value, " + product.id + ")'></td>";
          //table += "<td width='30%'>" + v['cantidad'] + "</td>";
          table += "<td width='30%'><input type='number' class='form-control'  value='" + v['cantidad'] +"' onchange = 'cambiaitemsproduct(this.value, " + v['productoid'] + ")'></td>";
          table += "<td>" + v['producto'] + "</td>";
          table += "<td>" + Math.round(v['precio']) + "</td>";
          table += "<td>" + "<p>Eliminar producto</p>" + "</td>";
          table += "<tr>";
        });
        $("#detailventabodyedit").append(table);

        $("#staticmodaleditventa").modal("show");
      }
    },
    error:function(e){
      console.error("error en el proceso de consulta de venta");
    },
    complete:function(c){
      loadinproccess(false,'');
    }
  });
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

$("#addproductventa").click(function(e){
  productid = $("#productitems").val();
  if(productid != null){
    productname = $("#productitems option:selected").text();
    $(".cantidad-items-product").text(productname);
    $("#token-items").text(cantidadproductselect);

    if(cantidadproductselect < 1){
      $("#items-product").prop( "disabled", true);
      $("#error-product-exists").css("display","block");
    } else{
      $("#items-product").prop( "disabled", false);
      $("#error-product-exists").css("display","none");
      $("#items-product").prop("max",cantidadproductselect);
    }

    $("#addproductmodel").modal("show");
  }
});
$("#btnaddproduct").click(function(e){
  itemsproduct = $("#items-product").val();
  if(itemsproduct == ''){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: '¿Cuantos productos de ' + productname + ' desea agregar al carrito?',
      showConfirmButton: false,
      timer: 2000
    });
  } else{
    $("#addproductmodel").modal("hide");
    addproduct(productid,productname,itemsproduct);
    buildtabledetail();
    $("#items-product").val('');
  }
});
$("#payventa").click(function(e){
  clienteselect = $("#clientitems").val();
  pagoselect  = $("#typepay").val();

  if(clienteselect == null){
    clienteselect = 1;
  }

  if(pagoselect == null){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'Seleccione el metodo de pago',
      showConfirmButton: false,
      timer: 1500
    });
  }else if(products.length < 1){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'No se encontraron productos en el carrito',
      showConfirmButton: false,
      timer: 1500
    });
  } else{
    if(pagoselect == 1){
      $("#modalPagoRecibe").modal("show");
    } else if(pago == 3){

        // Agrega credenciales de SDK
        const mp = new MercadoPago("TEST-05200779-bcbd-401a-bfc7-a80485ea0b47", {
          locale: "es-MX",
        });
        console.log(mp);

        // Inicializa el checkout
        mp.checkout({
          preference: {
            id: "612152203724066",
          },
          render: {
            container: ".cho-container", // Indica el nombre de la clase donde se mostrará el botón de pago
            label: "Pagar", // Cambia el texto del botón de pago (opcional)
          },
        });

    }

  }
});
$("#productitems").change(function(e){
  var productselect = $("#productitems").val();

  $.ajax({
    url:"producto/existencia/" + productselect,
    type:"GET",
    success:function(product){
      if(product.code == 200){

        cantidadproductselect = product.cantidaditems;

        if(cantidadproductselect < 1){
          Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: '¡No se encontraron stock del producto seleccionado!',
            showConfirmButton: false,
            timer: 1500
          });

          $(".swal2-input").prop("disabled", true);
          $("#error-product-exist").css("display", "block");
          $("#btnaddproduct").css("display", "none");
        } else{
          $(".swal2-input").prop("disabled", false);
          $("#error-product-exist").css("display", "none");
          $("#btnaddproduct").css("display", "block");
        }
      }
    },
    error:function(e){
      console.log("Error");
    }
  });


});
$("#btncobrarventa").click(function(e){
  var recibe = $("#idpago").val();

  if(recibe == ''){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'Debes recibir efectivo',
      showConfirmButton: false,
      timer: 1500
    });
  } else{
    var preciofinal = recibe - pagototal;

    if(preciofinal < 0){
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'Necesitas mas efectivo',
        showConfirmButton: false,
        timer: 1500
      });
    } else{
      insertVenta();
      // insercion de la venta
    }
  }
});
$("#idpago").change(function(e){
  var totalventa = $("#pagototalcobrar").val();
  var recibedinero = $("#idpago").val();
  var totaldar = recibedinero - totalventa;

  if(totaldar < 0){
    Swal.fire({
      position: 'top-end',
      icon: 'warning',
      title: 'El dinero recibido no alcanza a cubrir el total de la venta',
      showConfirmButton: false,
      timer: 1500
    });
    $("#btncobrarventa").prop("disabled",true);
  } else{
    pagototalefectivo  =  totalventa;
    $("#cambioventabyefectivo").val(totaldar.toFixed(2));
    $("#btncobrarventa").prop("disabled",false);
  }
});
$("#cancelventa").click(function(e){
  products =  new Array();
  $("#detailventabody").empty();
  pagototal = 0.00;
  buildtabledetail();
});
