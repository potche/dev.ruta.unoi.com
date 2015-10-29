var CrearWizard = function() {

    return {
        init: function(questions) {

            // Inicializamos el wizard para crear evaluación con validaciones

            $('#advanced-wizard').formwizard({
                disableUIStyles: true,
                validationEnabled: true,
                validationOptions: {
                    errorClass: 'help-block animation-slideDown',
                    errorElement: 'span',
                    ignore: '',
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
                        },
                        'closingdate':{
                            required: true,
                            date: true
                        },
                        'count_profiles':{
                            min: 1
                        },
                        'count_questions':{
                            min: 1
                        }
                    },
                    messages: {
                        'eval[title]': {
                            required: '<i class="fa fa-times"></i> Por favor, ingresa un título para la evaluación',
                            minlength: '<i class="fa fa-times"></i> Ingresa al menos dos caracteres para el título',
                            maxlength: '<i class="fa fa-times"></i> El título no puede ser mayor a 250 caracteres'
                        },
                        'eval[description]': {
                            minlength: '<i class="fa fa-times"></i> Ingresa al menos dos caracteres para la descripción',
                            maxlength: '<i class="fa fa-times"></i> La descripción no puede ser mayor a 500 caracteres'
                        },
                        'closingdate':{
                            required: '<i class="fa fa-times"></i> Selecciona una fecha de cierre para esta evaluación',
                            date: '<i class="fa fa-times"></i> Selecciona una fecha del calendario o ingresa una fecha válida'
                        },
                        'count_profiles':{
                            min: '<i class="fa fa-times"></i> Elige al menos un perfil con nivel, posteriormente haz click en Siguiente'
                        },
                        'count_questions':{
                            min: '<i class="fa fa-times"></i> Agrega al menos una pregunta con su categoría y al terminar haz click en Finalizar'
                        }
                    }
                },
                inDuration: 0,
                outDuration: 0
            });
            // Finaliza init wizard

            /**
             * Se valida que la fecha ingresada sea un día mayor al de hoy
             */

            jQuery.validator.addMethod("dateAfter", function(value, element, params) {

                if (!/Invalid|NaN/.test(new Date(value))) {

                    return (new Date(value) > params);
                }
                return (isNaN(value) && isNaN(params) || (Number(value) > Number(params)));

            },'<i class="fa fa-times"></i> La fecha de cierre no puede ser antes de hoy.');

            $("#closingdate").rules('add', { dateAfter: new Date() });



            /**
             *
             * Función para agregar perfil con nivel
             *
             * @param perfil_id
             * @param nivel_id
             * @param nivtitle
             * @param perftitle
             */

           function agregaPerfil(perfil_id, nivel_id, nivtitle, perftitle) {

                var count = parseInt($("#count_profiles").val());

                if(perfil_id != '' && nivel_id != '') {

                    var dynamicId = 'perfil_'+perfil_id+'_'+nivel_id;

                    if ($("#" + dynamicId).length == 0){

                        var elem =

                            '<div class="col-sm-3"><div id ="perfil_'+perfil_id+'_'+nivel_id+'" class="block perfil" style="text-align: center;">' +
                            '<div class="block-title themed-background">' +
                            '<div class="block-options pull-right">' +
                            '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-toggle-content"><i class="fa fa-times"></i></a>' +
                            '</div>' +
                            '</div>' +
                            '<b>'+perftitle+'</b><br><em>'+nivtitle+'</em>' +
                            '<div class="row"><br></div><div class="form-group" hidden>' +
                            '<input type="text" id="eval[perfiles]['+perfil_id+'][]" name="eval[perfiles]['+perfil_id+'][]" class="form-control" value="'+nivel_id+'">' +
                            '</div>' +
                            '</div></div>';

                        $(elem).appendTo('#perf-niv-agregados');
                        $("#count_profiles").val(count+1);
                    }
                }
            }

            /**
             * Evento para agregar todos los perfiles y niveles
             *
             */

            $("#btn_all_profiles").click(function(){

                $('.perfil').remove();
                $('#count_profiles').val(0);

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
             * Evento del botón para agregar perfiles
             */

            $("#btn_add_profile").click(function(){

                var perfil_id = $("#select-perfil").val();
                var perftitle = $("#select-perfil option:selected").text();
                var nivel_id = $("#select-nivel").val();
                var nivtitle = $("#select-nivel option:selected").text();

                agregaPerfil(perfil_id,nivel_id,nivtitle,perftitle);

            });


            /**
             * Función que limpia de caracteres escapables las preguntas de una evaluación
             *
             * @param question
             * @returns {void|string|XML}
             */

            function wipeQuestion(question) {

               return question.replace(/[`~@#%^&*_|+=:'"<>\{\}\[\]\\\/]/gi, '');
            }

            /**
             * Manejamos el evento del botón para agregar preguntas
             */

            $("#btn_add_pregunta").click(function(){

                var countPreg = parseInt($("#count_questions").val());

                var pregunta = wipeQuestion($("#pregunta").val());
                var categoria_id = $("#select-categoria").val();
                var cattexto = $("#select-categoria option:selected").text();

                if(pregunta != '' && categoria_id != ''){

                    var elem =
                        '<div class="col-sm-12"><div class="block pregunta">' +
                            '<div class="block-title">' +
                                '<div class="block-options pull-right">' +
                                    '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-toggle-content"><i class="fa fa-times"></i></a>' +
                                '</div>' +
                            '<h4><em>'+cattexto+'</em></h4>'+
                            '</div>' +
                            '<p style="text-align: center;">'+pregunta+'</p>'+
                            '<div class="form-group" hidden>' +
                                '<input type="text" id="eval[preguntas][]" name="eval[preguntas][]" class="form-control" value="'+categoria_id+'::'+pregunta+'">' +
                            '</div>' +
                        '</div></div>';

                    $(elem).appendTo('#div-preg-agregadas');
                    $("#count_questions").val(countPreg+1);
                }
            });
            
            //Evento para eliminar perfiles/niveles

            $('#perf-niv-agregados').on("click", ".block-title #deleter", function() {

                $(this).closest("div .perfil").remove();
                var count = $("#count_profiles").val();
                $("#count_profiles").val(parseInt(count)-1);
            });

            //Evento para eliminar preguntas que ya han sido agregadas

            $('#div-preg-agregadas').on("click", ".block-title #deleter", function() {
                $(this).closest("div .pregunta").remove();
                var countPreg = $("#count_questions").val();
                $("#count_questions").val(parseInt(countPreg)-1);
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

                if($('#advanced-third').is(':visible')) {

                    $("#next2").attr('value','Finalizar');
                }
            }

            setInputLabels();
            setFinishedLabel();

            /**
             * Comportamiento de botones anterior y siguiente
             */


            $("#back2, #next2").click(function(){
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