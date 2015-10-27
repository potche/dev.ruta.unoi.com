var CrearWizard = function() {

    return {
        init: function(questions) {

            /* Inicializamos el wizard para crear evaluación con validaciones */
            $('#advanced-wizard').formwizard({
                disableUIStyles: true,
                validationEnabled: true,
                validationOptions: {
                    errorClass: 'help-block animation-slideDown',
                    errorElement: 'span',
                    errorPlacement: function(error, e) {
                        e.parents('.form-group > div').append(error);
                    },
                    highlight: function(e) {
                        $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                        $(e).closest('.help-block').remove();
                    },
                    success: function(e) {
                        e.closest('.form-group').removeClass('has-success has-error');
                        //e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                        e.closest('.help-block').remove();
                    },
                    rules: {
                        'eval[title]': {
                            required: true,
                            minlength: 2,
                            maxlength: 250
                        },
                        'eval[description]': {
                            minlength: 2,
                            maxlength: 500
                        }
                    },
                    messages: {
                        'eval[title]': {
                            required: 'Por favor, ingresa un título para la evaluación',
                            minlength: 'Ingresa al menos dos caracteres para el título',
                            maxlength: 'El título no puede ser mayor a 250 caracteres'
                        },
                        'eval[description]': {
                            minlength: 'Ingresa al menos dos caracteres para la descripción',
                            maxlength: 'La descripción no puede ser mayor a 500 caracteres'
                        }
                    }
                },
                inDuration: 0,
                outDuration: 0
            });

            function agregaPerfil(perfil_id, nivel_id, nivtitle, perftitle) {

                if(perfil_id != '' && nivel_id != '') {

                    var elem = '<div class="block perfil" style="text-align: center;">' +
                        '<div class="block-title themed-background">' +
                        '<div class="block-options pull-right">' +
                        '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-toggle-content"><i class="fa fa-times"></i></a>' +
                        '</div>' +
                        '</div>' +
                        '<p>'+perftitle+' de '+nivtitle+'</p>' +
                        '<div class="form-group" hidden>' +
                        '<input type="text" id="eval[perfiles]['+perfil_id+'][]" name="eval[perfiles]['+perfil_id+'][]" class="form-control" value="'+nivel_id+'">' +
                        '</div>' +
                        '</div>';

                    $(elem).appendTo('#perf-niv-agregados');
                }
            }

            /**
             * Evento para agregar todos los perfiles y niveles
             *
             */

            $("#btn_all_profiles").click(function(){

                $('.perfil').remove();

                $("#select-perfil option").each(function()
                {
                    var perfil_id = $(this).val();
                    var perftitle = $(this).text();

                    $("#select-nivel option").each(function()
                    {
                        var nivel_id = $(this).val();
                        var nivtitle = $(this).text();

                        agregaPerfil(perfil_id,nivel_id,nivtitle,perftitle);
                    });
                });
            });


            /**
             * Manejamos el evento del botón para agregar perfiles
             */

            $("#btn_add_profile").click(function(){

                var perfil_id = $("#select-perfil").val();
                var perftitle = $("#select-perfil option:selected").text();
                var nivel_id = $("#select-nivel").val();
                var nivtitle = $("#select-nivel option:selected").text();

                agregaPerfil(perfil_id,nivel_id,nivtitle,perftitle);
            });

            /**
             * Manejamos el evento del botón para agregar preguntas
             */

            $("#btn_add_pregunta").click(function(){

                var pregunta = $("#pregunta").val();
                var categoria_id = $("#select-categoria").val();
                var cattexto = $("#select-categoria option:selected").text();

                if(pregunta != '' && categoria_id != ''){

                    var elem =
                        '<div class="block pregunta">' +
                            '<div class="block-title">' +
                                '<div class="block-options pull-right">' +
                                    '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-toggle-content"><i class="fa fa-times"></i></a>' +
                                '</div>' +
                            '<h4><em>'+cattexto+'</em></h4>'+
                            '</div>' +
                            '<p style="text-align: center;">'+pregunta+'</p>'+
                            '<div class="form-group" hidden>' +
                                '<input type="text" id="eval[preguntas][]" name="eval[preguntas][]" class="form-control" value="'+categoria_id+'::'+(pregunta)+'">' +
                            '</div>' +
                        '</div>';

                    $(elem).appendTo('#div-preg-agregadas');
                }
            });


            //Manejamos el evento para eliminar perfiles/niveles

            $('#perf-niv-agregados').on("click", ".block-title #deleter", function () {
                $(this).closest("div .perfil").remove();
            });

            $('#div-preg-agregadas').on("click", ".block-title #deleter", function () {
                $(this).closest("div .pregunta").remove();
            });

            /**
             * Cambiamos los labels de los botones al español y manejamos
             * el caso donde es el último paso.
             */

            function setInputLabels(){

                $("#back2").attr('value','Atrás');
                $("#next2").attr('value','Siguiente');
            }

            function setFinishedLabel(){

                if($('#advanced-third').is(':visible')){

                    $("#next2").attr('value','Finalizar');
                }
            }

            setInputLabels();
            setFinishedLabel();

            /**
             * Comportamiento de botones anterior y siguiente
             */

            $("#next2").click(function(){
                setInputLabels();
                setFinishedLabel();
            });

            $("#back2").click(function(){
                setInputLabels();
                setFinishedLabel();
            });

            /**
             * Cargamos las preguntas para el autocompletado
             *
             */

            //var preguntas = ["Pregunta de prueba"];
            $('.input-typeahead').typeahead({ source: questions });

        }
    };
}();