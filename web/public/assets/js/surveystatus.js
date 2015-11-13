var SurveyTable = function(){

    return{

        init: function(urlPost, redirectUrl){

            var accion = '';
            var survey = '';

            //Asigno variables al dar click en inactivar para manejar evento de inactivación
            $('.disable-survey, .enable-survey').click(function(){

                accion = $(this).hasClass('enable-survey');
                survey = $(this).attr('id');
            });

            //Manejo evento para enviar cambio de estado de evaluación
            $('#confInactivar').click(function(){

                $(this).modal("hide");
                $.ajax({

                    url: urlPost,
                    type: 'POST',
                    data: {
                        'surveyid': survey,
                        'surveyStatus': accion
                    }
                }).done(function (data){

                    window.location.href=redirectUrl;
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