/*
 *  Document   : login.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Login page
 */

var Login = function() {

    // Function for switching form views (login, reminder and register forms)
    var switchView = function(viewHide, viewShow, viewHash){
        viewHide.slideUp(250);
        viewShow.slideDown(250, function(){
            $('input').placeholder();
        });

        if ( viewHash ) {
            window.location = '#' + viewHash;
        } else {
            window.location = '#';
        }
    };

    return {
        init: function() {
            /* Switch Login, Reminder and Register form views */
            var formLogin       = $('#form-login'),
                formReminder    = $('#form-reminder'),
                formRegister    = $('#form-register');

            $('#link-register-login').click(function(){
                switchView(formLogin, formRegister, 'register');
            });

            $('#link-register').click(function(){
                switchView(formRegister, formLogin, '');
            });

            $('#link-reminder-login').click(function(){
                switchView(formLogin, formReminder, 'reminder');
            });

            $('#link-reminder').click(function(){
                switchView(formReminder, formLogin, '');
            });

            // If the link includes the hashtag 'register', show the register form instead of login
            if (window.location.hash === '#register') {
                formLogin.hide();
                formRegister.show();
            }

            // If the link includes the hashtag 'reminder', show the reminder form instead of login
            if (window.location.hash === '#reminder') {
                formLogin.hide();
                formReminder.show();
            }

            jQuery.validator.addMethod("noSpace", function(value, element) {
                return value.indexOf(" ") < 0 && value != "";
            }, "Por favor no ponga espacios");


            /*
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Login form - Initialize Validation */
            $('#form-login').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    e.closest('.form-group').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'login-user': {
                        required: true,
                        minlength: 2,
                        noSpace: true
                    },
                    'login-password': {
                        required: true,
                        minlength: 5,
                        noSpace: true
                    }
                },
                messages: {
                    'login-user': {
                        required: 'Por favor ingresa tu Usuario del LMS',
                        minlength: 'Tu Usuario del LMS debe tener una longitud mayor a 2 caracteres'
                    },
                    'login-password': {
                        required: 'Por favor ingresa tu Contraseña',
                        minlength: 'Tu Contraseña debe tener una longitud mayor a 5 caracteres'
                    }
                },
                submitHandler: function (form) {
                    var base_path = $('#baseUrl').val();
                    var redirurl = $('#redir').val();
                    var params = $('#with').val();

                    $('#loading').slideDown("fast");
                    $.ajax({
                        type: "POST",
                        data: $(form).serialize(),
                        url: base_path+"/ajax/autentication",
                        success: function (result) {

                            var rs = result.split("|");
                            var mailOrig = rs[1];
                            limpia();
                            switch (rs[0]) {
                                case '1':
                                    $('#loading').slideUp("fast");
                                    $('#ValidaMailM').modal();
                                    $('.titleM').html('Validaci&oacute;n de email');
                                    $('.bodyM').html(
                                        'Se le ha enviado un Email al correo: <br/> <br/>' +
                                        '<span id="email" class="email" type="email"><b>'+mailOrig+'</b></span>' +
                                        ' | <a id="edit" style="cursor: pointer" onclick="changeEmail();">Cambiar</a><br/> ' +
                                        'Verifique que este sea su correo.<br/><br/>' +
                                        '<br/>' +
                                        '<button type="button" class="btn btn-primary" onclick="enviaEmail(\''+base_path+'\',\''+mailOrig+'\',\''+rs[2]+'\',\''+rs[3]+'\',\''+rs[4]+'\');">Reenviar Email</button>'
                                    );
                                    /*
                                    $('.bodyM').html(
                                        'Para activar tu cuenta en la red social, necesitamos validar tu correo electr&oacute;nico envi&aacute;ndote un c&oacute;digo. <br/> <br/>' +
                                        'Verifica que este sea tu correo.<br/><br/>' +
                                        '<span id="email" class="email"><b>'+mailOrig+'</b></span>' +
                                        ' | <a id="edit" style="cursor: pointer" onclick="changeEmail();">Editar</a> ' +
                                        '<br/>' +
                                        '<button type="button" class="btn btn-primary" onclick="enviaEmail(\''+base_path+'\',\''+mailOrig+'\');">Enviar C&oacute;digo</button>' +
                                        '<br/><br/>'+
                                        'Introducir C&oacute;digo: <input type="number" class="input-sm" id="code" name="code"> ' +
                                        '<button type="button" class="btn btn-primary" onclick="checkCode(\''+base_path+'\');">Activar</button>'
                                    );
                                    */
                                    break;
                                case '2':
                                    $('#loading').slideUp("fast");
                                    $('#ValidaMailM').modal();
                                    $('.titleM').html('Validaci&oacute;n de email');
                                    $('.bodyM').html(
                                        '<h4>El correo <b>'+mailOrig+'</b> ya esta registrado por un usuario en el sistema, ' +
                                        'por favor ingrese otro, ya que el correo debe de ser único. </h4> <br/>' +
                                        'Email: <input id="email" class="email" type="email" onkeyup="valNewMail();"><br/> ' +
                                        '<br/>' +
                                        '<button disabled type="button" id="enviaCorreo" class="btn btn-primary" onclick="enviaEmail(\''+base_path+'\',\''+mailOrig+'\',\''+rs[2]+'\',\''+rs[3]+'\',\''+rs[4]+'\');">Enviar Email</button>'
                                    );
                                    break;
                                case '97':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html(rs[1]);
                                    break;
                                case '98':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html(rs[1]);
                                    break;
                                case '100':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('No se pudo autenticar exitosamente, inténtelo de nuevo');
                                    break;
                                case '101':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('No cuenta con el perfil adecuado para ingresar');
                                    break;
                                case '102':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('No cuenta con ningún periodo activo');
                                    break;
                                case '103':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('No pertenece a la Regi&oacute;n');
                                    break;
                                case '104':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('Su cuenta no esta Activa, contacte a su Coordinador para mayor informaci&oacute;n');
                                    break;
                                case '105':
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html('Su password es incorrecto, por favor intentelo de nuevo');
                                    break;
                                case 'ok':

                                    if(redirurl != 'none'){

                                        if(params != 'none'){

                                            login(base_path+"/"+redirurl+"/"+params);
                                            break;
                                        }else{

                                            login(base_path+"/"+redirurl);
                                            break;
                                        }
                                    }
                                    login(base_path+"/inicio");
                                    break;
                                default :
                                    $('#loading').slideUp("fast");
                                    $('#errorM').modal();
                                    $('.bodyError').html(rs);
                                    break
                            }
                        },
                        error: function() {
                            $('#frmNewCACR').fadeTo("slow", 0.15, function() {
                                $('#error').fadeIn();
                            });
                        }
                    });
                }
            });

            /* Reminder form - Initialize Validation */
            $('#form-reminder').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    e.closest('.form-group').removeClass('has-success has-error');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'reminder-email': {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    'reminder-email': 'Please enter your account\'s email'
                }
            });

            /* Register form - Initialize Validation */
            $('#form-register').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    if (e.closest('.form-group').find('.help-block').length === 2) {
                        e.closest('.help-block').remove();
                    } else {
                        e.closest('.form-group').removeClass('has-success has-error');
                        e.closest('.help-block').remove();
                    }
                },
                rules: {
                    'register-firstname': {
                        required: true,
                        minlength: 2
                    },
                    'register-lastname': {
                        required: true,
                        minlength: 2
                    },
                    'register-email': {
                        required: true,
                        email: true
                    },
                    'register-password': {
                        required: true,
                        minlength: 5
                    },
                    'register-password-verify': {
                        required: true,
                        equalTo: '#register-password'
                    },
                    'register-terms': {
                        required: true
                    }
                },
                messages: {
                    'register-firstname': {
                        required: 'Please enter your firstname',
                        minlength: 'Please enter your firstname'
                    },
                    'register-lastname': {
                        required: 'Please enter your lastname',
                        minlength: 'Please enter your lastname'
                    },
                    'register-email': 'Please enter a valid email address',
                    'register-password': {
                        required: 'Please provide a password',
                        minlength: 'Your password must be at least 5 characters long'
                    },
                    'register-password-verify': {
                        required: 'Please provide a password',
                        minlength: 'Your password must be at least 5 characters long',
                        equalTo: 'Please enter the same password as above'
                    },
                    'register-terms': {
                        required: 'Please accept the terms!'
                    }
                }
            });
        }
    };
}();


function limpia() {
    $('#login-user').val('');
    $('#login-password').val('');
//    $('#error').val(1);
}

function baseUrl() {
    var pathArray = location.href.split('/');
    var protocol = pathArray[0];
    var host = pathArray[2];
    var url = protocol + '//' + host;
    alert(url);
}

function changeEmail(){
    var $input = $("<input>", {
        val: $('#email').text(),
        type: "text",
        name: 'email',
        id: "email"
    });
    $input.addClass("email");
    $('#email').replaceWith($input);
};

function enviaEmail(base_path, mailOrig, personId, code, name){
    var email = $('#email').val();
    if(email == ''){
        email = mailOrig;
    }

    $.post( base_path+"/forwardMail", { email: email, personId: personId, code: code, name: name })
        .done(function( data ) {
            $('#enviaCorreo').html('Reenviar');
            $('#successM').modal();
            $('.titleSuccessM').html('Validaci&oacute;n de correo');
            $('.bodySuccessM').html('Se te ha enviado el c&oacute;digo a tu correo, recuerda revisar en los correos no deseados');
        });
};

function checkCode(base_path){
    var code = $('#code').val();
    if(code == ''){
        $('#errorM').modal();
        $('.bodyError').html('Por favor ingrese el C&oacute;digo que recibi&oacute; en su correo');
    }else{
        $.post( base_path+"/checkCode", { code: code })
            .done(function( data ) {
                if(data == '1'){
                    $('#ValidaMailM').modal('hide');
                    $('#redirectM').modal();
                    $('.titleRedirectM').html('Validaci&oacute;n de correo');
                    $('.bodyRedirectM').html('Se ha validado correctamente el correo');
                }else {
                    $('#successM').modal();
                    $('.titleSuccessM').html('Validaci&oacute;n de correo');
                    $('.bodySuccessM').html('El c&oacute;digo que ingres&oacute; es incorrecto, favor de corroborarlo.');
                }
            });
        }
};

function login(base_path){
    window.location.replace(base_path);
}

function valNewMail(){
    var email = $('#email').val();
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(re.test(email)){
        $("#enviaCorreo").prop('disabled', false);
    }else{
        $("#enviaCorreo").prop('disabled', true);
    }
}