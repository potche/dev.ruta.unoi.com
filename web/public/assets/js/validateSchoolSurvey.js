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
        $('#userList').html('');
    }
    showGraphs();
    $('#block-detalle').hide();
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
        graphs(graphSchoolAPI+nameSchoolArray[0], serverSchoolAPI+nameSchoolArray[0], nameSchool, nameSurvey);
    }
    showGraphs();
    $('#block-detalle').hide();
    switchButtonTodo();

});

function switchButtonTodo(){
    if($('#schoolIdFrm').val() == '' && $('#surveyIdFrm').val() == ''){
        $('#todo').prop('disabled', true);
        $('#userList').html('');
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

        $('#block-detalle').show();
        graphs(graphSurveyAPI+nameSurveyArray[0]+'/school/'+nameSchoolArray[0], serverSurveyAPI+nameSurveyArray[0]+'/school/'+nameSchoolArray[0], nameSchool, nameSurvey);

    }else if( ($('#schoolIdFrm').val() != '') && ($('#surveyIdFrm').val() == '') ){

        var nameSchool = $('#schoolIdFrm').val();
        var nameSchoolArray = nameSchool.split("-");
        var nameSurvey = $('.nameSurvey').html();
        $('#divSchool').hide();
        setNameSchool(nameSchool);
        $('#divContentSchool').show();

        graphs(graphSchoolAPI+nameSchoolArray[0], serverSchoolAPI+nameSchoolArray[0], nameSchool, nameSurvey);

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

    $('#userList').html('');
    $('#block-detalle').hide();

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
    //reflowChart();
}

function hideGraphs(){
    $('#withGraphs').hide();
    $('#withOutGraphs').show();
}

function graphsAll(serverAPI, nameSchool, surveyName){
    $( "div.highcharts-container" ).remove();

    $('#container').html('<div class="row">'+
            '<div class="block-content">'+
                '<div class="col-sm-12">'+

                    '<div class="text-center">'+
                        '<button class="btn btn-lg">'+
                            '<i class="fa fa-refresh fa-spin fa-3x"></i>'+
                            '<br/>'+
                            'Cargando, por favor espere...'+
                        '</button>'+
                        '<br/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>');

    $('#containerColumn').html('<div class="row">'+
            '<div class="block-content">'+
                '<div class="col-sm-12">'+

                    '<div class="text-center">'+
                        '<button class="btn btn-lg">'+
                            '<i class="fa fa-refresh fa-spin fa-3x"></i>'+
                            '<br/>'+
                            'Cargando, por favor espere...'+
                        '</button>'+
                        '<br/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>');

    $.ajax({
        url: serverAPI,
        dataType: 'json',
        success: function(results){
            pieGrl(results.global, nameSchool, surveyName);
            columnGrl(results.global, nameSchool, surveyName);
        }
    });

}

function graphs(graphAPI, serverAPI, nameSchool, surveyName){
    $( "div.highcharts-container" ).remove();

    $('#container').html(
                    '<div class="text-center">'+
                        '<button class="btn btn-lg">'+
                            '<i class="fa fa-refresh fa-spin fa-3x"></i>'+
                            '<br/>'+
                            'Cargando, por favor espere...'+
                        '</button>'+
                        '<br/>'+
                    '</div>'
                );

    $('#containerColumn').html(
                    '<div class="text-center">'+
                        '<button class="btn btn-lg">'+
                            '<i class="fa fa-refresh fa-spin fa-3x"></i>'+
                            '<br/>'+
                            'Cargando, por favor espere...'+
                        '</button>'+
                        '<br/>'+
                    '</div>'
                );

    $('#userList').html(
                    '<div id="block-resumenT" class="block" >'+
                        '<div class="block-title">'+
                            '<div class="block-options pull-right">'+
                                '<a href="javascript:void(0)" class="btn btn-info btn-sm btn-primary" data-toggle="block-toggle-content"><i class="fa fa-arrows-v"></i></a>'+
                            '</div>'+
                            '<h2>Tabla de avance <strong>'+nameSchool+'</strong></h2>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="block-content">'+
                                '<div class="col-sm-12">'+
                
                                    '<div class="text-center">'+
                                        '<button class="btn btn-lg">'+
                                            '<i class="fa fa-refresh fa-spin fa-3x"></i>'+
                                            '<br/>'+
                                            'Cargando, por favor espere...'+
                                        '</button>'+
                                        '<br/>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>');

    $.ajax({
        url: serverAPI,
        dataType: 'json',
        success: function(results){

            $.ajax({
                url: graphAPI,
                dataType: 'json',
                success: function(res){
                    if(!res.Error){
                        var global = 0;
                        $.each( res.global, function( key, value ) {
                            global += value.y;
                        });

                        pieGrl(res.global, nameSchool, surveyName);
                        columnGrl(res.global, nameSchool, surveyName);
                        showGraphs();
                    }else{
                        hideGraphs();
                        $('#block-detalle').hide();
                    }
                }
            });

            if(surveyName !== "Todas las Evaluaciones"){
                var surveyArr = surveyName.split('-');
                var schoolArr = nameSchool.split('-');
                $.ajax({
                    url: detail+surveyArr[0]+"/"+schoolArr[0],
                    dataType: 'json',
                    success: function (res) {
                        if(!res.Error) {
                            $('#surveyDetalle').html('<div class="row">' +
                                '<div class="block-content">' +
                                '<div class="col-sm-12">' +

                                '<div class="text-center">' +
                                '<button class="btn btn-lg">' +
                                '<i class="fa fa-refresh fa-spin fa-3x"></i>' +
                                '<br/>' +
                                'Cargando, por favor espere...' +
                                '</button>' +
                                '<br/>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>');

                            var row = '';
                            var div = '';
                            $.each(res.preguntas, function (key, value) {
                                row +=
                                    '<tr>' +
                                    '<td>' + value.orden + '</td>' +
                                    '<td>' + value.pregunta + '</td>';

                                $.each(value.opciones, function (key2, option) {
                                    var idD = '_' + value.orden + '_' + option.opcion.replace(/ /g, '_');
                                    div += '<div class="media" id="' + idD + '">' +
                                        '<div class="media-body">' +
                                        '<div class="block">' +
                                        '<div class="table-responsive">' +
                                        '<table class="table table-vcenter table-striped">' +
                                        '<thead><tr><th><i class="fa fa-user"></i> <span>NOMBRE </span></th>' +
                                        '<th class="hidden-xs"><i class="fa fa-comments-o"></i> <span class="hidden-xs">COMENTARIO </span></th></tr></thead>';
                                    if (option.personas.length !== 0) {
                                        row += '<td> <a href="javascript:void(0)" id="' + idD + '" class="detalle">' + option.personas.length + '</a> </td>';
                                    } else {
                                        row += '<td>' + option.personas.length + '</td>';
                                    }

                                    $.each(option.personas, function (key3, person) {
                                        div += '<tr><td>' + person.nombre + '</td><td class="hidden-xs">' + person.comentario + '</td></tr>';
                                    });
                                    div +=
                                        '</table>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>';
                                });
                                row += '</tr>';

                            });
                            //$('#divContentDetalle').html(div);

                            var divDetalle =
                                '<div class="table-responsive">' +
                                '<p><em>Detalle de la evaluación.</em></p>' +
                                '<table id="datatable-detalle" class="table table-vcenter table-condensed table-bordered">' +
                                '<thead>' +
                                '<tr>' +
                                '<th class="text-center">#</th>' +
                                '<th class="text-center">Indicador</th>' +
                                '<th class="text-center">' +
                                '<span><b>Sí</b></span>' +
                                '</th>' +
                                '<th class="text-center">' +
                                '<span><b>No</b></span>' +
                                '</th>' +
                                '<th class="text-center">' +
                                '<span><b>No sé</b></span>' +
                                '</th>' +
                                '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                row +
                                '</tbody>' +
                                '</table>' +
                                '</div>';
                            $('#surveyDetalle').html(divDetalle);


                            $('.detalle').click(function (event) {
                                var id = event.target.id;
                                var title = id.split('_');
                                var valores = [];
                                $(this).parents("tr").find("td").each(function () {
                                    valores.push($(this).html());
                                });
                                var t = '';
                                if (title[3]) {
                                    t = title[2] + ' ' + title[3];
                                } else {
                                    t = title[2]
                                }

                                $('.titleModalDS').html('<em>' + valores[1] + '</em> <strong>"' + t + '"</strong>');
                                $('.bodyModalDS').html($('div#' + id).html());
                                $('#detalleSurveyM').modal();
                            });

                            $('#right').click(function () {

                            });
                            TablesDatatables3.init();
                        }
                    }
                });
            }

            if(!results.Error){
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

                        var eventoDetalle = 'onclick="detalle( ' + value.persona + ', \'' + value.nombre + '\', ' + avance.toFixed(2) + ', \''+surveyName+'\')" style="cursor: pointer"';
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

                var divUsers =
                    '<div id="block-resumenT" class="block" >'+
                        '<div class="block-title">'+
                            '<div class="block-options pull-right">'+
                                '<a href="javascript:void(0)" class="btn btn-info btn-sm btn-primary" data-toggle="block-toggle-content"><i class="fa fa-arrows-v"></i></a>'+
                            '</div>'+
                            '<h2>Tabla de avance <strong>'+nameSchool+'</strong></h2>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="block-content">'+
                                '<div class="col-sm-12">'+
                                    '<small class="visible-xs"><i class="fa fa-info-circle text-primary"></i> Puedes navegar horizontalmente para ver las demás columnas</small>'+
                                    '<div class="table-responsive">'+
                                        '<p>Estad&iacute;sticas de usuarios que han realizado por lo menos una evaluación. <em>Da <b>click</b> sobre un usuario para ver su detalle</em></p>'+
                                        '<table id="userList-datatable" class="table table-vcenter table-condensed table-bordered">'+
                                            '<thead>'+
                                                '<tr>'+
                                                    '<th style="width: 150px;" class="text-center"><i class="fa fa-users"></i></th>'+
                                                    '<th>Nombre</th>'+
                                                    '<th style="width: 150px;" class="text-center">Progreso</th>'+
                                                    '<th class="text-center">Detalle</th>'+
                                                '</tr>'+
                                            '</thead>'+
                                            '<tbody>'+
                                                row+
                                            '</tbody>'+
                                        '</table>'+
                                        '<br/>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';
                $('#userList').html(divUsers);
                TablesDatatables.init();
            }else{
                $('#userList').html('');
            }
        }
    });

}


function detalle(personId, userName, avance, surveyName){

    graphsUser(personId, userName, surveyName);
    $('#surveyUser').html( '' );
    $('.userName').html(userName);
    $('#avanceUser').html('<div class="progress progress-striped active">'+
        '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'+avance+'" aria-valuemin="0" aria-valuemax="100" style="width: '+avance+'%;">'+avance+'%</div>'+
        '</div>');
    $('#statsUser').modal('show');
}

function graphsUser(personId, userName, surveyName){

    if( surveyName == 'Todas las Evaluaciones' ){
        $.ajax({
            url: serverPersonSurveyAPI+personId,
            dataType: 'json',
            success: function(results){
                createGraphsUser(results.global, userName, surveyName);
                var list = '';
                $.each( results.byPerson, function( key, value ) {
                    $.each( value.evaluaciones, function( key2, item ) {
                        if(item.estatus == '4'){
                            list += "<option value='"+item.id+"|"+JSON.stringify(item.opciones)+"|"+userName+"|"+item.titulo+"|"+personId+"'>"+item.titulo+"</option>";
                        }else{

                        }
                    });
                });

                $('#selectSurveys').html(
                    "<h5>Selecciona alguna evaluación para ver su <b>detalle</b>:</h5>"+
                    "<select id='evaluacioLU' class='form-control' size='1' onchange='(evalUser(this.value))'>"+
                    "<option value='"+0+"|"+JSON.stringify(results.global)+"|"+userName+"|Global|"+personId+"'>Global</option>" + list + "" +
                    "</select>"
                );
            }
        });
    }else{
        var surveyArray = surveyName.split('-');
        $.ajax({
            url: serverSurveyAPI+surveyArray[0]+'/person/'+personId,
            dataType: 'json',
            success: function(results){
                createGraphsUser(results.global, userName, surveyName);

                $('#selectSurveys').html(
                    "<span class='form-control' >"+surveyName+"</span>"
                );

                creaDetalleSurvey(surveyArray[0], surveyArray[1], personId);
            }
        });
    }


}

function createGraphsUser(results, userName, surveyName){
    if(typeof results =='object'){
        // It is JSON
    }else{
        results = $.parseJSON(results);
    }
    pieGrlLU(results, userName, surveyName);
    columnGrlLU(results, userName, surveyName);
}

function evalUser(rs){
    //console.log(rs);
    var arr = rs.split('|');
    createGraphsUser(arr[1],arr[2], arr[3]);

    if(arr[0] != 0){
        creaDetalleSurvey(arr[0], arr[3], arr[4]);
    }else{
        $('#surveyUser').html( '' );
    }

}

function creaDetalleSurvey(surveyId, title, personId){
    $.ajax({
        url: serverSurveyUserAPI+surveyId+'/person/'+personId,
        dataType: 'json',
        success: function(results){
            var surveyU = '';
            $.each( results.persons, function( key, person ) {
                $.each( person.surveys, function( key2, survey ) {
                    $.each( survey.questions, function( key3, question ) {
                        $.each( question.answers, function( key4, answer ) {
                            //console.log(answer.answer);
                            surveyU +=
                                '<tr>' +
                                    '<td class="hidden-sm hidden-xs text-center">'+question.orderQ+'</td>' +
                                    '<td>'+question.question+'</td>' +
                                    '<td>'+answer.answer+'</td>' +
                                    '<td>'+answer.comment+'</td>' +
                                '</tr>';
                        });

                    });
                });
            });

            var divSuervey =
                '<div class="row">'+
                '<div class="block-content">'+
                '<div class="col-sm-12 col-md-12 col-lg-12">'+
                '<div class="block">'+
                '<div class="block-title">'+
                '<h2>Evaluación <strong>'+title+'</strong></h2>'+
                '</div>'+
                '<div class="table-responsive">'+
                '<p><em>Detalle de la evaluación.</em></p>'+
                '<table id="example-datatable" class="table table-vcenter table-condensed table-bordered">'+
                '<thead>'+
                '<tr>'+
                '<th class="hidden-sm hidden-xs text-center">#</th>'+
                '<th class="text-center">Pregunta</th>'+
                '<th class="text-center">'+
                '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Respuesta</b></span>'+
                '<span class="visible-xs-inline"><b><i class="fa fa-pencil-square-o"></i></b></span>'+
                '</th>'+
                '<th class="text-center">'+
                '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Comentario</b></span>'+
                '<span class="visible-xs-inline"><b><i class="fa fa-commenting-o"></i></b></span>'+
                '</th>'+
                '</tr>'+
                '</thead>'+
                '<tbody>'+
                surveyU+
                '</tbody>'+
                '</table>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>';
            $('#surveyUser').html(divSuervey);
            TablesDatatables2.init();
        }
    });
}

var TablesDatatables = function(col) {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#userList-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 0, col ] } ],
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

var TablesDatatables2 = function() {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#example-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 0, 3 ] } ],
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');
        }
    };
}();

var TablesDatatables3 = function() {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#datatable-detalle').dataTable({
                columnDefs: [ { orderable: true, targets: [ 0, 4 ] } ],
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');
        }
    };
}();