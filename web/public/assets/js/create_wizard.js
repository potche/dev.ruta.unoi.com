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
                        'eval[closingdate]':{
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
                        'eval[closingdate]':{
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

            $('#eval\\[closingdate\\]').rules('add', { dateAfter: new Date() });


            if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

                $('#eval\\[closingdate\\]').prop('class','form-control');
                $('#eval\\[closingdate\\]').prop('type','date');
                $('#eval\\[closingdate\\]').removeAttr('placeholder');

            }

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

                            '<div class="col-sm-3 perfil"><div id ="perfil_'+perfil_id+'_'+nivel_id+'" class="block" style="text-align: center;">' +
                            '<div class="block-title themed-background">' +
                            '<div class="block-options pull-right">' +
                            '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-toggle-content"><i class="fa fa-times"></i></a>' +
                            '</div>' +
                            '</div>' +
                            '<b class="titulo_perfil">'+perftitle+'</b><br><em class="nivel_perfil">'+nivtitle+'</em>' +
                            '<div class="row"><br></div><div class="form-group" hidden>' +
                            '<input type="text" id="eval[perfiles]['+perfil_id+'][]" name="eval[perfiles]['+perfil_id+'][]" class="form-control" value="'+nivel_id+'">' +
                            '</div>' +
                            '</div></div>';

                        $(elem).appendTo('#perf-niv-agregados');
                        $("#count_profiles").val(count+1);
                        $("#error_select_perfiles").remove();

                    }
                } else if((perfil_id === '' || nivel_id === '') && !$("#error_select_perfiles").is(':visible')) {

                    $('<span id="error_select_perfiles" class="text-danger help-block animation-slideDown">' +
                        '<i class="fa fa-times"></i> ' +
                        'Debes agregar un perfil con nivel' +
                        '</span>').appendTo('#perf-niv-agregados');
                }
            }

            function borrarTodosPerfiles(){

                $('.perfil').remove();
                $('#count_profiles').val(0);
            }

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
             * Evento para agregar todos los perfiles y niveles
             *
             */

            $("#btn_all_profiles").click(function(){

                borrarTodosPerfiles();

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
             * Evento para borrar todos los perfiles y niveles
             */

            $("#btn_borrar_perfiles").click(function(){

                borrarTodosPerfiles();
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
             * Manejamos el evento para agregar preguntas
             */

            function showQuestionErrors(message){

                if(!$("#error_preguntas").is(':visible')) {

                    $('<span id="error_preguntas" class="text-danger animation-slideDown"><i class="fa fa-times"></i>'+message+'</span>').appendTo('#div-preg-agregadas');

                } else{

                    $('#error_preguntas').html('<i class="fa fa-times"></i>'+message);
                }
            }

            $("#btn_add_pregunta").click(function(){

                var countPreg = parseInt($("#count_questions").val());

                var pregunta = wipeQuestion($("#pregunta").val());
                var categoria_id = $("#select-categoria").val();
                var cattexto = $("#select-categoria option:selected").text();
                var exists = $('.pregtexto[pregunta="'+pregunta+'"]').html();

                if(typeof exists === 'undefined'){

                    if(pregunta != '' && categoria_id != ''){

                        var elem =

                            '<div class="block pregunta-div">' +
                            '<div class="block-title">' +
                            '<div class="block-options pull-left">' +
                            '<a href="javascript:void(0)"  id ="dragger" class="btn btn-xs" onmouseover="" style="cursor: all-scroll"><i class="fa fa-bars"></i></a>' +
                            '</div>' +
                            '<div class="block-options pull-right">' +
                            '<a href="javascript:void(0)"  id ="deleter" class="btn btn-danger btn-xs" data-toggle="block-hide"><i class="fa fa-times"></i></a>' +
                            '</div>' +
                            '<h4><em class="cattexto" >'+cattexto+'</em></h4>'+
                            '</div>' +
                            '<p class="pregtexto" style="text-align: center;" pregunta="'+pregunta+'">'+pregunta+'</p>'+
                            '<div class="form-group" hidden>' +
                            '<input type="text" id="eval[preguntas][]" name="eval[preguntas][]" class="form-control" value="'+categoria_id+'::'+pregunta+'">' +
                            '</div>' +
                            '</div>';

                        $(elem).appendTo('#div-preg-agregadas');
                        $("#count_questions").val(countPreg+1);
                        $("#error_preguntas").remove();

                    } else if((pregunta === '' || categoria_id === '')){

                        showQuestionErrors("Por favor selecciona o agrega una pregunta con una categoría");
                    }
                }else showQuestionErrors("Por favor agrega una pregunta que no haya sido agregada a esta evaluación");

            });


            //Evento para eliminar perfiles/niveles agregados

            $('#perf-niv-agregados').on("click", ".block-title #deleter", function() {

                $(this).closest("div .perfil").remove();
                var count = $("#count_profiles").val();
                $("#count_profiles").val(parseInt(count)-1);
            });

            //Evento para eliminar preguntas que ya han sido agregadas

            $('#div-preg-agregadas').on("click", ".block-title #deleter", function() {
                $(this).closest(".pregunta-div").remove();
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
             * Esta funcion construye la matriz para el preview de perfiles de la evaluacion
             * @returns {string}
             */
            function parseProfilesforModal(){

                var profs = [];
                var rows = '';
                $('.perfil').each(function(){

                    var perfil = $(this).find('.titulo_perfil').text();
                    var nivel = $(this).find('.nivel_perfil').text();

                    if(typeof profs[perfil] === 'undefined'){

                        profs[perfil] = [];
                    }
                    profs[perfil].push(nivel);
                });

                for(var p in profs) {

                    rows = rows+'<tr>'+
                        '<td>'+p+'</td><td><ul>';

                    for(var n in profs[p]){

                        rows = rows+'<li>'+
                            profs[p][n]+
                            '</li>';
                    }
                    rows = rows+
                        '</ul></td></tr>';
                }
                return rows;
            }

            function parseQuestionsforModal() {

                var count = 0;
                var pregs = '';
                $('.pregunta-div').each(function(){

                    pregs =
                        pregs+
                        '<tr><td class="text-left"><b>'+
                        (++count)+'.- '+
                        $(this).find('.cattexto').text()+
                        ': </b><em>'+
                        $(this).find('.pregtexto').text()+
                        '</em></td></tr>';
                });

                return pregs;
            }


            function loadModalInfo(){

                $('#title').html($('#eval\\[title\\]').val());
                $('#closedate').html($('#eval\\[closingdate\\]').val());
                $('#preview-perfniv').html(parseProfilesforModal());
                $('#questions').html(parseQuestionsforModal());
            }

            /**
             * Comportamiento de botones anterior y siguiente
             */

            $("#back2, #next2").click(function(event){
                setInputLabels();
                setFinishedLabel();
                event.preventDefault();

                if ($("#next2").attr('value') == 'Finalizar'){

                    loadModalInfo();
                }
            });

            /**
             * Manejo de confirmación del modal
             */
            $('#confirmar').click(function(){

                $('#advanced-wizard').submit();
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