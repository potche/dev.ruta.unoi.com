/*
 *  Document   : validateSchool.js
 *  Author     : isra
 *  Description: valida el filtro por Escuela
 */

$('#closeSchool').click(function(){
    $('#divContentSchool').toggle();
    $('#schoolIdFrm').val('');
    setTagNameSchool( 'Todas las Escuelas' );
    $('#divSchool').toggle();
});

$('#closeSurvey').click(function(){
    $('#divContentSurvey').toggle();
    $('#surveyIdFrm').val('');
    setTagNameSurvey( 'Todas las Evaluaciones' );
    $('#divSurvey').toggle();
});

function findFilter(){

    if( ($('#schoolIdFrm').val() != '') && ($('#surveyIdFrm').val() != '') ){

        var nameSchool = $('#schoolIdFrm').val();
        $('#divSchool').hide();
        setNameSchool(nameSchool);
        setTagNameSchool(nameSchool);
        $('#divContentSchool').show();

        var nameSurvey = $('#surveyIdFrm').val();
        $('#divSurvey').hide();
        setNameSurvey(nameSurvey);
        setTagNameSurvey(nameSurvey);
        $('#divContentSurvey').show();

    }else if( ($('#schoolIdFrm').val() != '') && ($('#surveyIdFrm').val() == '') ){

        var nameSchool = $('#schoolIdFrm').val();
        var nameSchoolArray = nameSchool.split("-");
        $('#divSchool').hide();
        setNameSchool(nameSchool);
        setTagNameSchool(nameSchool);
        $('#divContentSchool').show();

    }else if( ($('#schoolIdFrm').val() == '') && ($('#surveyIdFrm').val() != '') ){

        var nameSurvey = $('#surveyIdFrm').val();
        var nameSurveyArray = nameSurvey.split("-");
        $('#divSurvey').hide();
        setNameSurvey(nameSurvey);
        setTagNameSurvey(nameSurvey);
        $('#divContentSurvey').show();

        graphs('http://dev.evaluaciones.unoi.com/app_dev.php/api/v0/stats/results/survey/'+nameSurveyArray[0], nameSchool, nameSurvey);

    }

    $("#buscar").prop('disabled', true);
    $('#todo').prop('disabled', false);

    //graphs(nameSchool, nameSurvey);
}

function resetFilter(){
    $('#schoolIdFrm').val('');
    $('#divSchool').show();
    setTagNameSchool('Todas las Escuelas');
    $('#divContentSchool').hide();

    $('#surveyIdFrm').val('');
    $('#divSurvey').show();
    setTagNameSurvey('Todas las Evaluaciones');
    $('#divContentSurvey').hide();

    var serverAPI = "http://dev.evaluaciones.unoi.com/app_dev.php/api/v0/stats/results";
    graphs(serverAPI, nameSchool, nameSurvey);
    $('#todo').prop('disabled', true);
}

function setNameSchool(nameSchool){
    $('#nameSchool').html( nameSchool );
}

function setTagNameSchool(nameSchool){
    $('#tagNameSchool').html( nameSchool );
}

function setNameSurvey(nameSurvey){
    $('#nameSurvey').html( nameSurvey );
}

function setTagNameSurvey(nameSurvey){
    $('#tagNameSurvey').html( nameSurvey );
}

function showErrorBuscar(okSchool, okSurvey, schoolIdFrm, surveyIdFrm){
    if( okSchool && okSurvey ){
        $('#errorBuscar').hide();
        if( (schoolIdFrm.val() == '') && (surveyIdFrm.val() == '') ){
            $("#buscar").prop('disabled', true);
        }else{
            $("#buscar").prop('disabled', false);
        }
    }
}

function graphs(serverAPI, nameSchool, surveyName){
    $.ajax({
        url: serverAPI,
        dataType: 'json',
        success: function(results){
            pieGrl(results.global, nameSchool, surveyName);
            columnGrl(results.global, nameSchool, surveyName);
        }
    });
}