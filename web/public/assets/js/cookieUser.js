var cookie = {};
cookie = (function(window, undefined) {

    var cname = 'login-user';

//    function set(cvalue, exdays) {
//
//        var now = new Date();
//        now.setTime(now.getTime() + (exdays * 24 * 60 * 60 * 1000));
//        var expires = 'expires=' + now.toUTCString();
//
//        document.cookie = cname + '=' + cvalue + '; ' + expires;
//
//    }	// end method messages


    function get() {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[ i ];
            while (c.charAt(0) == ' ') c = c.substring(1);
            if (c.indexOf(name) != -1){ console.log(c.substring(name.length, c.length)); return c.substring(name.length, c.length);}
        }
        return 0;
        //return cname;
    }	// end method get

    return {
//        action: function() {
//
//            set($('#nick').val(), 7);
//
//        },
        display: function() {
            var value = get();

            if (value !== 0) {
                var nick = $('#login-user');
                var showNick = $('#showNick');
                $('.nick').hide(); //muestro mediante clase
                nick.attr('type', 'hidden');
                showNick.removeClass('hidden');
                showNick.html(value + ' <a id="changeNick" href="#"><span class="fa fa-times text-primary"></span></a>');
                nick.val(value);
                $('#avatar').html('<img width="130px" height="130px" id="avatar" class="img-circle img-responsive" src="public/assets/images/login/icon_niño1.png" alt="Foto de Usuario" style="margin: 0 auto;"/>');

                $('#changeNick').click(function() {
                    $('.nick').show('slow'); //muestro mediante clase
                    showNick.slideUp(200);
                    nick.attr('type', 'text');
                    nick.val('');
                    nick.blur();
                    $('#avatar').html('<h2 style="color: rgba( 255,255,255,0.7 )"><span class="fa fa-user fa-3x"></span></h2>');
                });
            }else{
                $('#avatar').html('<h2 style="color: rgba( 255,255,255,0.7 )"><span class="fa fa-user fa-3x"></span></h2>');
            }
        }

    };

}(window));

var error = {};
error = (function(window, undefined) {

    function message(errorNumber) {
        if (errorNumber === 1) {
            return '<div><span class="fa fa-times fa-5x text-danger"></span><div><h4>Datos de acceso incorrectos</h4><p><em>Por favor verifica los datos ingresados.</em></p>';
        }	// end if
        return false;
    }	// end method message

    return {
        showDialog: function(errorNumber) {

            var textMessage = message(errorNumber);
            if (textMessage) {
                $('#dialog').modal();
                $('#title-dialog').html('Mensaje');
                $('#textMessage').html(textMessage);
            }	// end if
        }
    };

}(window));

if (typeof errorNumber != 'undefined')
    error.showDialog(errorNumber);

cookie.display();

//$('#pass').keyup(function() {
//
//    var password = $('#pass');
//    var hash = $('#hash');
//    
//    if (password.val() != '') {
//        cookie.action();
//        hash.val(CryptoJS.MD5(password.val()));
//    }	// end if
//
//});