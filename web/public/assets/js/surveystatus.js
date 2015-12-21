var SurveyTable = function(){

    return{

        init: function(urlPost, redirectUrl){

            var accion = '';
            var survey = '';

            //Asigno variables al dar click en inactivar para manejar evento de inactivación
            $('.disable-survey, .enable-survey').click(function(){

                accion = $(this).hasClass('enable-survey');
                survey = $(this).attr('id');

                $.ajax({

                    url: urlPost,
                    type: 'POST',
                    data: {
                        'surveyid': survey,
                        'surveyStatus': accion
                    }
                }).done(function (data){

                    if(data.status == 200){

                        if (accion == true ){

                            $("#"+survey).attr( "class","disable-survey btn btn-xs btn-danger").html("Inactivar")

                        }else{

                            $("#"+survey).attr( "class","enable-survey btn btn-xs btn-success").html("Activar");
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

            // Manejo evento para mostrar matríz de perfiles y niveles
            $('.btn-matriz').click(function(event){

                event.preventDefault();
                $('#modal_perfiles').modal();
                $('#matriz-perfiles').html($(this).next().html());
            });
        }
    }
}();