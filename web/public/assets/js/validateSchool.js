/*
 *  Document   : validateSchool.js
 *  Author     : isra
 *  Description: valida el filtro por Escuela
 */

var ValidateSchool = function() {


    return {
        init: function(exampleTypeheadData) {

            jQuery.validator.addMethod("valSchool", function(value, element){

                    if(exampleTypeheadData.indexOf(value) != -1){
                        return true
                    }
                    return false

            }, '<div class="text-danger"><i class="fa fa-times"></i> Ingrese una Escuela Valida</div>');

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
                    schooIdFrm: {
                        required: true,
                        valSchool: true
                    }
                },
                messages: {
                    schooIdFrm: {
                        required: '<div class="text-danger"><i class="fa fa-times"></i> Ingresa una Escuela</div>'
                    }
                },
                submitHandler: function(form) {
                    return true;
                }
            });
        }
    };
}();
