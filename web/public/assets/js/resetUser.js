/**
 * Created by indigo on 29/08/16.
 */


function resetUser(id, name, user, surveyId, survey, executioner) {
    var message;

    if(surveyId){
        message = "¿Realmente desea Reiniciar el Instrumento <b>"+survey+"</b> del Usuario <b>"+name+"</b> perteneciente al colegio <b>"+nameSchool+"</b>?.";
    }else {
        message = "¿Realmente desea Reiniciar los Instrumentos del Usuario <b>"+name+"</b> perteneciente al colegio <b>"+nameSchool+"</b>?.";
    }

    $(".titleModal").html("Reiniciar Usuario");
    $(".bodyModal").html(
        "<div class='modal-body'>"+
        "<div class='row'>"+
        "<div class='col-xs-3 text-center'>" +
        "<img src='/public/assets/images/login/icon_niño1.png' class='img-circle'>"+
        "</div>"+
        "<div class='col-xs-9 text-justify'>" +
        message +
        "</div>"+
        "</div>"+
        "</div>"+
        "<div class='modal-footer'>"+
        "<button type='button' class='btn btn-info' data-dismiss='modal'>Cancelar</button>"+
        "<button onclick='resetUserOk("+id+", \""+user+"\", \""+name+"\", "+schoolId+", \""+nameSchool+"\", "+surveyId+", \""+survey+"\","+executioner+")' type='button' class='btn btn-success' >Aceptar</button>"+
        "</div>"
    );

    $("#confirmModal").modal();
}

function resetUserOk(personId, user, name, schoolId, school, surveyId, survey, executioner) {
    console.log(personId);
    console.log(surveyId);
    var urlApi;
    var parameters;
    var message;

    if(surveyId){
        urlApi = HttpHost+baseUrl+"/api/v0/admin/resetPersonSurvey";
        parameters = {personId: personId, user: user, name: name, schoolId: schoolId, school: school, surveyId: surveyId, survey: survey, executioner: executioner};
        message = "<h1>El Instrumento  del usuario fue Reseteado Correctamente</h1>";
    }else {
        urlApi = HttpHost+baseUrl+"/api/v0/admin/resetPerson";
        parameters = {personId: personId, user: user, name: name, schoolId: schoolId, school: school, executioner: executioner};
        message = "<h1>Los Instrumentos fueron Reseteado Correctamente</h1>";
    }

    $.post( urlApi , parameters, function() {
        $("#resetAll").prop('disabled',true);
        $("#resetAll").attr("data-survey","");
        $("#resetAll").attr("data-school","");
        $("#combinacion").html('');
    }).done(function(data) {
        $(".titleModal").html("Resetar Estadísticas Usuario");
        $(".bodyModal").html(
            "<div class='modal-body'>" +
            message+
            "</div>" +
            "<div class='modal-footer'>" +
            "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>" +
            "</div>"
        );

        $('#userList').html("");
        $('#optionSchool').val('').trigger('chosen:updated');
        $('#optionSurvey').val('').trigger('chosen:updated');
    })
        .fail(function(error) {
            console.log(error);
            $(".titleModal").html("Error - Reiniciar Estadísticas Usuario");
            $(".bodyModal").html(
                "<div class='modal-body'>"+
                "<h1>El Instrumento <b>no</b> fue Reseteado Correctamente, por favor intentelo mas tarde. </h1>"+
                "</div>"+
                "<div class='modal-footer'>"+
                "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>"+
                "</div>"
            );
        });

}

function resetSchoolOk(schoolId, school, surveyId, survey, executioner) {
    console.log(schoolId);
    console.log(surveyId);
    var urlApi;
    var parameters;
    var message;

    if(surveyId){
        urlApi = HttpHost+baseUrl+"/api/v0/admin/resetSchoolSurvey";
        parameters = {schoolId: schoolId, school: school, surveyId: surveyId, survey: survey, executioner: executioner};
        message = "<h1>El Instrumento  del Colegio fue Reseteado Correctamente</h1>";
    }else {
        urlApi = HttpHost+baseUrl+"/api/v0/admin/resetSchool";
        parameters = {schoolId: schoolId, school: school, executioner: executioner};
        message = "<h1>Los Instrumentos fueron Reseteado Correctamente</h1>";
    }

    $.post( urlApi, parameters, function() {
        $("#resetAll").prop('disabled',true);
        $("#resetAll").attr("data-survey","");
        $("#resetAll").attr("data-school","");
        $("#combinacion").html('');
    }).done(function(data) {
        $(".titleModal").html("Reiniciar Colegio");
        $(".bodyModal").html(
            "<div class='modal-body'>" +
            message +
            "</div>" +
            "<div class='modal-footer'>" +
            "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>" +
            "</div>"
        );

        $('#userList').html("");
        $('#optionSchool').val('').trigger('chosen:updated');
        $('#optionSurvey').val('').trigger('chosen:updated');
    })
        .fail(function(error) {
            console.log(error);
            $(".titleModal").html("Error - Reiniciar Colegio");
            $(".bodyModal").html(
                "<div class='modal-body'>"+
                "<h1>Los Instrumentos <b>no</b> se Reiniciaron Correctamente, por favor intentelo mas tarde. </h1>"+
                "</div>"+
                "<div class='modal-footer'>"+
                "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>"+
                "</div>"
            );
        });

}


function setNameSchool(nameSchool){
    $('.nameSchool').html( nameSchool );
}


var TablesDatatables = function() {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#userList-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 2, 2 ] } ],
                pageLength: 5,
                lengthMenu: [[5,10,15,20,25,30, -1], [5,10,15,20,25,30, 'All']],
                paging: true,
                searching: true
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');
        }
    };
}();