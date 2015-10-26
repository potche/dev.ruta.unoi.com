var CrearWizard = function() {

    return {
        init: function() {

            /* Bara de progreso*/
            $('#progress-wizard').formwizard({focusFirstInput: true, disableUIStyles: true, inDuration: 0, outDuration: 0});

            // Cambiar progreso cada que se logra un paso
            var progressBar = $('#progress-bar-wizard');
            progressBar
                .css('width', '33%')
                .attr('aria-valuenow', '33');

            $("#progress-wizard").bind('step_shown', function(event, data){
                if (data.currentStep === 'progress-first') {
                    progressBar
                        .css('width', '33%')
                        .attr('aria-valuenow', '33')
                        .removeClass('progress-bar-warning progress-bar-success')
                        .addClass('progress-bar-danger');
                }
                else if (data.currentStep === 'progress-second') {
                    progressBar
                        .css('width', '66%')
                        .attr('aria-valuenow', '66')
                        .removeClass('progress-bar-danger progress-bar-success')
                        .addClass('progress-bar-warning');
                }
                else if (data.currentStep === 'progress-third') {
                    progressBar
                        .css('width', '100%')
                        .attr('aria-valuenow', '100')
                        .removeClass('progress-bar-danger progress-bar-warning')
                        .addClass('progress-bar-success');
                }
            });

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


            /**
             * Cambiamos los labels de los botones al español y manejamos
             * el caso donde es el último paso.
             */

            var profiles = [];

            $("#btn_add_profile").click(function(){

                var perfil_id = $("#select-perfil").val();
                var perftitle = $("#select-perfil option:selected").text();
                var nivel_id = $("#select-nivel").val();
                var nivtitle = $("#select-nivel option:selected").text();

                if(perfil_id != '' && nivtitle != ''){
                    profiles.push([perfil_id,perftitle,nivel_id,nivtitle]);
                }


            });

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
             * Comportamiento de botones
             */

            $("#next2").click(function(){
                setInputLabels();
                setFinishedLabel();
            });

            $("#back2").click(function(){
                setInputLabels();
                setFinishedLabel();
            });



        }
    };
}();