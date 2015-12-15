/*
 *  Document   : validateSchool.js
 *  Author     : isra
 *  Description: valida el filtro por Escuela
 */

$('#closeSchool').click(function(){

    $('#divContentSchool').toggle();
    $('#schoolIdFrm').val('');
    var nameSchool = 'Todas las Escuelas';
    setNameSchool( nameSchool );
    var nameSurvey = $('.nameSurvey').html();
    $('#divSchool').toggle();

    if($('#surveyIdFrm').val() == ''){
        graphsAll(serverAllAPI, nameSchool, nameSurvey);
    }else{
        var nameSurvey = $('#surveyIdFrm').val();
        var nameSurveyArray = nameSurvey.split("-");
        setNameSurvey(nameSurvey);
        graphsAll(serverSurveyAPI+nameSurveyArray[0], nameSchool, nameSurvey);
        $('#block-resumenT').hide();
    }
    showGraphs();

    switchButtonTodo();

});

$('#closeSurvey').click(function(){

    $('#divContentSurvey').toggle();
    $('#surveyIdFrm').val('');
    var nameSurvey = 'Todas las Evaluaciones';
    setNameSurvey( nameSurvey );
    var nameSchool = $('.nameSchool').html();
    $('#divSurvey').toggle();

    if($('#schoolIdFrm').val() == ''){
        graphsAll(serverAllAPI, nameSchool, nameSurvey);
    }else{
        var nameSchool = $('#schoolIdFrm').val();
        var nameSchoolArray = nameSchool.split("-");
        setNameSchool(nameSchool);
        graphs(serverSchoolAPI+nameSchoolArray[0], nameSchool, nameSurvey);
    }
    showGraphs();
    switchButtonTodo();

});

function switchButtonTodo(){
    if($('#schoolIdFrm').val() == '' && $('#surveyIdFrm').val() == ''){
        $('#todo').prop('disabled', true);
        $('#block-resumenT').hide();
    }
}

function findFilter(){

    if( ($('#schoolIdFrm').val() != '') && ($('#surveyIdFrm').val() != '') ){

        var nameSchool = $('#schoolIdFrm').val();
        var nameSchoolArray = nameSchool.split("-");
        var nameSurvey = $('.nameSurvey').html();
        $('#divSchool').hide();
        setNameSchool(nameSchool);
        $('#divContentSchool').show();

        var nameSurvey = $('#surveyIdFrm').val();
        var nameSurveyArray = nameSurvey.split("-");
        var nameSurveyArray = nameSurvey.split("-");
        $('#divSurvey').hide();
        setNameSurvey(nameSurvey);
        $('#divContentSurvey').show();

        graphs(serverSurveyAPI+nameSurveyArray[0]+'/school/'+nameSchoolArray[0], nameSchool, nameSurvey);

    }else if( ($('#schoolIdFrm').val() != '') && ($('#surveyIdFrm').val() == '') ){

        var nameSchool = $('#schoolIdFrm').val();
        var nameSchoolArray = nameSchool.split("-");
        var nameSurvey = $('.nameSurvey').html();
        $('#divSchool').hide();
        setNameSchool(nameSchool);
        $('#divContentSchool').show();

        graphs(serverSchoolAPI+nameSchoolArray[0], nameSchool, nameSurvey);

    }else if( ($('#schoolIdFrm').val() == '') && ($('#surveyIdFrm').val() != '') ){
        var nameSchool = $('.nameSchool').html();
        var nameSurvey = $('#surveyIdFrm').val();
        var nameSurveyArray = nameSurvey.split("-");
        $('#divSurvey').hide();
        setNameSurvey(nameSurvey);
        $('#divContentSurvey').show();

        graphsAll(serverSurveyAPI+nameSurveyArray[0], nameSchool, nameSurvey);

    }

    $("#buscar").prop('disabled', true);
    $('#todo').prop('disabled', false);

}

function resetFilter(){

    $('#schoolIdFrm').val('');
    $('#divSchool').show();
    setNameSchool('Todas las Escuelas');
    $('#divContentSchool').hide();

    $('#surveyIdFrm').val('');
    $('#divSurvey').show();
    setNameSurvey('Todas las Evaluaciones');
    $('#divContentSurvey').hide();

    graphsAll(serverAllAPI, nameSchool, nameSurvey);
    $('#todo').prop('disabled', true);

    $('#block-resumenT').hide();

}

function setNameSchool(nameSchool){
    $('.nameSchool').html( nameSchool );
}

function setNameSurvey(nameSurvey){
    $('.nameSurvey').html( nameSurvey );
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

function showGraphs(){
    $('#withOutGraphs').hide();
    $('#withGraphs').show();
    reflowChart();
}

function hideGraphs(){
    $('#withGraphs').hide();
    $('#withOutGraphs').show();
}

function graphsAll(serverAPI, nameSchool, surveyName){
    $.ajax({
        url: serverAPI,
        dataType: 'json',
        success: function(results){
            pieGrl(results.global, nameSchool, surveyName);
            columnGrl(results.global, nameSchool, surveyName);
        }
    });
}

function graphs(serverAPI, nameSchool, surveyName){
    $.ajax({
        url: serverAPI,
        dataType: 'json',
        success: function(results){

            var global = 0;
            $.each( results.global, function( key, value ) {
                global += value.y;
            });

            if(global){
                pieGrl(results.global, nameSchool, surveyName);
                columnGrl(results.global, nameSchool, surveyName);
                showGraphs();
            }else{
                hideGraphs();
            }


            //tabla de usuarios
            var row = '';
            $.each( results.byPerson, function( key, value ) {
                var realizadas = 0;
                var pendientes = 0;
                //console.log( value.nombre );
                $.each( value.evaluaciones, function( key2, value2 ) {
                    //console.log( value2.estatus );
                    if(value2.estatus == '4'){
                        realizadas++;
                    }else{
                        pendientes++;
                    }
                });

                var total = (realizadas+pendientes);
                var avance = (realizadas/total)*100;
                if( avance == 0 ){
                    var eventoDetalle = '';
                    var colorEye = '#d4d4d4';
                    var colorAlert = 'style="color: red;"';
                }else {
                    var eventoDetalle = 'onclick="detalle( ' + value.persona + ', \'' + value.nombre + '\', ' + avance + ', \'\')" style="cursor: pointer"';
                    var colorEye = '#46B7BF'
                    var colorAlert = '';
                }
                row += '<tr '+eventoDetalle+'>'+
                    '<td class="text-center">'+
                    '<img src="/public/assets/images/login/icon_niño1.png" alt="avatar" class="img-circle">'+
                    '</td>'+
                    '<td>'+
                    value.nombre+
                '<div class="progress progress-striped active">'+
                    '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:'+avance+'%;">'+
                    avance.toFixed(2)+' %'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td class="text-center" '+colorAlert+' >'+
                    realizadas +'/'+ total+
                    '</td>'+
                    '<td class="text-center"><i class="fa fa-eye fa-2x" style="color: '+colorEye+'"> </i></td>'+
                '</tr>';
            });
            //TablesDatatables.destroy();
            $('#userList').html(row);
            //TablesDatatables.init(3);
            $('#block-resumenT').show();
        }
    });
}