var SurveyStatus = function(){

    return{
        init: function(urlPost, redirectUrl){

            var accion = '';
            var survey = '';

            $('.disable-survey, .enable-survey').click(function(){

                accion = $(this).hasClass('enable-survey');
                survey = $(this).attr('id');

                console.log(accion)
                console.log(survey)
            });

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
        }
    }
}();