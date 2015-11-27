/*
 *  Document   : validateSurvey.js
 *  Author     : isra
 *  Description: valida el filtro por Escuela
 */

var ValidateSurvey = function() {


    return {
        init: function(surveyTypeheadData) {

            jQuery.validator.addMethod("valSurvey", function(value, element){

                    if(surveyTypeheadData.indexOf(value) != -1){
                        return true
                    }
                    return false

            }, '<div class="text-danger"><i class="fa fa-times"></i> Ingrese una Evaluacion Valida</div>');

            /*
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Login form - Initialize Validation */
            $('#schoolFrm').validate({
                onfocusout: false,
                onkeyup: false,
                onclick: false,

                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                rules: {
                    surveyIdFrm: {
                        required: true,
                        valSurvey: true
                    }
                },
                messages: {
                    surveyIdFrm: {
                        required: '<div class="text-danger"><i class="fa fa-times"></i> Ingresa una Evalacion</div>'
                    }
                },
                submitHandler: function(form) {
                    return true;
                }
            });
        }
    };
}();
