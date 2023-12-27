var validformlogin;
var validformregister;


$(document).ready(function() {
    var user = localStorage.getItem("user");

    if(user != null){
      window.location.replace("home");
    }

    validformlogin = $("#form-login").validate({
        rules: {
          emailLogin: {
            required: true,
            email: true
          },
          passwordLogin: {
            required: true
          }
        },
        messages:{
          emailLogin: {
              required: "Se requiere de un correo para poder entrar",
              email: "El formato de tu correo no es correcto, ejemplo (elcorreo@hotmail.com)"
          },
          passwordLogin: {
            required: "Se requiere tu contraseña para poder entrar"
          }
        },
      });
    validformregister = $("#form-new-register").validate({
        rules: {
          nameregister: {
            required: true,
          },
          emailregister: {
            required: true,
            email: true
          },
          passwordregister: {
            required: true,
            minlength : 8
          },
          confirmpasswordregister: {
            required: true,
            minlength : 8,
            equalTo : "#passwordregister"
          }
        },
        messages:{
          nameregister: {
            required: "Se requiere el nombre del usuario",
          },
          emailregister: {
              required: "Se requiere de un correo para poder entrar",
              email: "El formato de tu correo no es correcto, ejemplo (elcorreo@hotmail.com)"
          },
          passwordregister: {
            required: "Se requiere tu contraseña para poder entrar",
            minlength: "Minimo de 8 caracteres para la contraseña"
          },
          confirmpasswordregister: {
            required: "Se requiere la confirmacion de contraseña",
            minlength : "Minimo de 8 caracteres para la confirmacion contraseña",
            equalTo : "Las constraseñas no cohiciden"
          }
        },
      });
});


$("#btn-login").click(function(e){

  if(validformlogin.form()){
    var email = $("#email").val();
    var password = $("#password").val();

    var formData = new FormData(document.getElementById("form-login"));

    if(email != '' && password != ''){

      if(validarcorreo(email)){

        $.ajax({
          headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          url:"login",
          type:"POST",
          dataType : 'json',
          processData: false,
          contentType: false,
          data:formData,
          beforeSend: function(e){
            loadinproccess(true, 'Buscando usuario');
          },
          success:function(usuario){
            if(usuario.code == 200){
              
              localStorage.setItem("user", usuario.data.name);
              
              window.location.replace("home");
            } else{
              Swal.fire({
                position: 'top-end',
                icon: usuario.status,
                title: usuario.msm,
                showConfirmButton: false,
                timer: 2000
              });
            }
          },
          error:function(e){
            console.log("------------------error---------------------");
            console.log(e);
          }
        });
      }
    } else{
      Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: 'Se requiere de un correo y una contraseña para loguearte',
        showConfirmButton: false,
        timer: 2000
      });
    }
  }
});
$("#btnnewUser").click(function(e){

  if(validformregister.form()){
    var formData = new FormData(document.getElementById("form-new-register"));
    $.ajax({
      url:"register",
      type:"POST",
      dataType : 'json',
      processData: false,
      contentType: false,
      data:formData,
      beforeSend: function(e){
        loadinproccess(true, 'Registrando usuario');
      },
      success:function(user){
        Swal.fire({
          position: 'top-end',
          icon: user.status,
          title: user.msm,
          showConfirmButton: false,
          timer: 2000
        });
        if(user.code == 200){
          $("#form-new-register")[0].reset();
        }
      },
      error:function(e){
        console.log("Error");
      }

    })
  }
});

// validar formato de correo
const validarcorreo = (correo) =>{
  return /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(correo);
}

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
