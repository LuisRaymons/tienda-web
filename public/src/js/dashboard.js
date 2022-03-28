let user = $("#iduser").val();
let username =$("#nameuser").val();
$(document).ready(function() {
  cargarproductinexistentes();
  totalventabyuser();
  charventastotalesmes();
  charventasbyclient();
});

const cargarproductinexistentes = () => {
  $.ajax({
    url:"producto/inexistentes",
    type:"GET",
    success:function(inexistentes){

      if(inexistentes.code == 200){
        var data = inexistentes.data;
        $("#faltantesproduct").text("Te faltan " + data.length + " productos en almacen");
        $("#productos-inexistentes").empty();
        data.forEach((product, i) => {

          var cardproduct = "<div class='col-xl-3 col-sm-6 grid-margin stretch-card'>";
          cardproduct += "<div class='card cardcolor'>";
          cardproduct += "<div class='card-body'>";
          cardproduct += "<div class='row'>";
          cardproduct += "<div class='col-9'>";
          cardproduct += "<div class='d-flex align-items-center align-self-start'>";
          cardproduct += "<h5 class='title-colo-white'> Quedan " + product.stock + " Stock</h5>";
          cardproduct += "</div>";
          cardproduct += "</div>";
          cardproduct += "<div class='col-3'>";
          cardproduct += "<div class='icon icon-box-success'>";
          cardproduct += "<span class='mdi mdi-arrow-top-right icon-item iconspanproduct'></span>";
          cardproduct += "</div>";
          cardproduct += "</div>";
          cardproduct += "</div>";
          cardproduct += "<h6 class='text-muted font-weight-normal'>" + product.nombre + "</h6>";
          cardproduct += "</div>";
          cardproduct += "</div>";
          cardproduct += "</div>";
          $("#productos-inexistentes").append(cardproduct);
        });
      }
    },
    error:function(e){
      console.log("Error");
    }
  });
}
const totalventabyuser = () => {
  var userid = $("#iduser").val();

  var from = formatearFecha(obtenerFechaInicioDeMes());
  var to = formatearFecha(obtenerFechaFinDeMes());

  var formdata = new FormData();
      formdata.append("id",userid);
      formdata.append("from",from);
      formdata.append("to",to);

      $.ajax({
        url:"venta/total",
        type:"POST",
        data:formdata,
        processData: false,
        contentType: false,
        success:function(ventas){
          if(ventas.code = 200){
            $("#totalventasmes").text("Total de ventas del mes $" + ventas.data);
          }
        },
        error:function(e){
          console.log("Error");
        }
      });
}

// fechas
const obtenerFechaInicioDeMes = () => {
	const fechaInicio = new Date();
	return new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), 1);
}
const obtenerFechaFinDeMes = () => {
	const fechaFin = new Date();
	return new Date(fechaFin.getFullYear(), fechaFin.getMonth() + 1, 0);
}
const formatearFecha = (fecha) => {
	const mes = fecha.getMonth() + 1;
	const dia = fecha.getDate();
	return `${fecha.getFullYear()}-${(mes < 10 ? '0' : '').concat(mes)}-${(dia < 10 ? '0' : '').concat(dia)}`;
};
const setmes = (mesinput) => {
  $mes = "";
  switch (mesinput) {
    case 01:
      $mes = "Enero";
    break;
    case 02:
      $mes = "Febrero";
    break;
    case 03:
      $mes = "Marzo";
    break;
    case 04:
      $mes = "Abril";
    break;
    case 05:
      $mes = "Mayo";
    break;
    case 06:
      $mes = "Junio";
    break;
    case 07:
      $mes = "Julio";
    break;
    case 08:
      $mes = "Agosto";
    break;
    case 09:
      $mes = "Septiembre";
    break;
    case 10:
      $mes = "Octubre";
    break;
    case 11:
      $mes = "Noviembre";
    break;
    case 12:
      $mes = "Diciembre";
    break;
  }

  return $mes
}


// graficas
const charventastotalesmes = () => {
  const $grafica = document.querySelector("#chartventastotalesmes");

  $.ajax({
    url:"venta/total/mes",
    type: "GET",
    success:function(venta){
      if(venta.code == 200){
        var data = venta.data;

        var datosfull = new Array();;
        data.forEach((v, i) => {
          datos = {
            label:v.name,
            data: v.ventas,
            backgroundColor: v.backgroundColor,
            borderColor: v.borderColor,
            borderWidth: 1,
          }
          datosfull.push(datos);
        });

        new Chart($grafica, {
            type: 'line',// Tipo de gráfica
            data: {
                labels: data[0]['meses'],
                datasets: datosfull
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                },
            }
        });

      }
    },
    error:function(e){
      console.log("Error");
    }
  });


}
const charventasbyclient = () => {
  let ventaschartuser;
  var mesesetiquetas = new Array();
  var totalventa = [];
  const $grafica = document.querySelector("#ventasporuser");

  $.ajax({
    url:"venta/total/mes/" + user,
    type:"GET",
    success:function(venta){

      if(venta.code == 200){
        var data = venta.data;
        const datosVentas2020 = {
            label: "Ventas por mes de " + username,
            data: data.ventas, //[5000, 1500, 8000, 5102], // La data es un arreglo que debe tener la misma cantidad de valores que la cantidad de etiquetas
            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de fondo
            borderColor: 'rgba(54, 162, 235, 1)', // Color del borde
            borderWidth: 1,// Ancho del borde
        };
        ventaschartuser = new Chart($grafica, {
            type: 'line',// Tipo de gráfica
            data: {
                labels: data.meses, //mesesetiquetas,
                datasets: [
                    datosVentas2020,
                    // Aquí más datos...
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                },
            }
        });

      }
    },
    error:function(e){
      console.log("Error");
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
