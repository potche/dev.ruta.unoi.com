var NotifStatus = function(){

    return{

        init: function(urlPost){

            var accion = '';
            var person = '';

            $('.disable-notif, .enable-notif').click(function(){

                accion = $(this).hasClass('enable-notif');
                person = $(this).attr('id');

                console.log(accion);

                $.ajax({

                    url: urlPost,
                    type: 'POST',
                    data: {
                        'personid': person,
                        'status': accion
                    }

                }).done(function (data){

                    if(data.status == 200){

                        if (accion == true ){

                            $("#"+person).attr( "class","disable-notif btn btn-xs btn-danger").html("Desactivar")
                            $("#currentMailing").attr("class","fa fa-check-circle text-success");

                        }else{

                            $("#"+person).attr( "class","enable-notif btn btn-xs btn-success").html("Activar");
                            $("#currentMailing").attr("class","fa fa-times-circle text-danger");
                        }

                        $.bootstrapGrowl("<h4>Cambio exitoso</h4><p>"+data.message+"</p>", {
                            type: 'success',
                            delay: 5000,
                            allow_dismiss: true
                        });
                    }
                    else{

                        $.bootstrapGrowl("<h4>Error</h4><p>"+data.message+"</p>", {
                            type: 'error',
                            delay: 5000,
                            allow_dismiss: true
                        });
                    }
                });
            });
        }
    }
}();