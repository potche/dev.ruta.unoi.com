function getResume(surveyId, personId){

    $.get( "/api/v0/result/survey/"+surveyId+"/person/"+personId)
        .done(function( data ) {
            console.log(data.persons[0].surveys[0].surveyId);
            //console.log(data.persons[0].surveys[0].title);
            //console.log(data.persons[0].surveys[0].answerDate);
            createDataTableRes(data.persons[0].surveys[0].questions);
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            //console.log("get Resume finished")
        });
}

function getSurveyId(uri){
    var usriArr = uri.split('/');
    var zise = usriArr.length;

    return usriArr[zise-1];
}

function createDataTableRes(dat){

    var dataTableTareas =
        '<div class="table-responsive">'+
            '<table id="tbl-datatable-tarea" class="table table-bordered">'+
                '<thead>'+
                    '<tr>'+
                        '<th class="hidden-sm hidden-xs"><strong>#</strong></th>'+
                        '<th class="text-center" style="width: 50%;"><strong>Pregunta</strong></th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><strong>Respuesta</strong></span>'+
                            '<span class="visible-xs-inline"><strong><i class="fa fa-pencil-square-o"></i></strong></span>'+
                        '</th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><b>Comentario</b></span>'+
                            '<span class="visible-xs-inline"><strong><i class="fa fa-commenting-o"></i></strong></span>'+
                        '</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody>';

    var dataTableResumen =
        '<div class="table-responsive">'+
            '<table id="tbl-datatable-resumen" class="table table-bordered">'+
                '<thead>'+
                    '<tr>'+
                        '<th class="hidden-sm hidden-xs"><strong>#</strong></th>'+
                        '<th class="text-center"><strong>Pregunta</strong></th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><strong>Respuesta</strong></span>'+
                            '<span class="visible-xs-inline"><strong><i class="fa fa-pencil-square-o"></i></strong></span>'+
                        '</th>'+
                        '<th class="text-center">'+
                            '<span class="visible-lg-inline visible-md-inline visible-sm-inline hidden-xs"><strong>Comentario</strong></span>'+
                            '<span class="visible-xs-inline"><strong><i class="fa fa-commenting-o"></i></strong></span>'+
                        '</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody>';

    var si = 0;
    var no = 0;
    var noSe = 0;
    var categories = [];

    $.each(dat, function (key, value) {

        categories.push(value.category);

        var orderQ = value.orderQ;
        var question = value.question;
        var answerId = value.answers[0].answerId;
        var answer = value.answers[0].answer;
        var comment = value.answers[0].comment;

        if(answer === 'Sí'){
            si++;
            dataTableResumen +=
                '<tr>' +
                    '<td class="hidden-sm hidden-xs text-center">'+orderQ+'</td>'+
                    '<td>'+question+'</td>'+
                    '<td>'+answer+'</td>'+
                    '<td>'+escapeHtml(comment)+'</td>'+
                '</tr>';
        }else{
            //console.log(value.questionId);
            //console.log(value.answers[0].answerId);

            dataTableTareas +=
                '<tr>' +
                    '<td class="hidden-sm hidden-xs text-center">'+orderQ+'</td>'+
                    '<td>'+question+'</td>'+
                    '<td>' +
                        '<div class="btn-group-wrap" style="text-align: center">' +
                        '<div id="btn-group-'+answerId+'" class="btn-group" style="margin: 0 auto; text-align: center; width: inherit; display: inline-block;">'+
                            '<button id="btn-si-'+answerId+'" class="btn btn-alt btn-primary" data-toggle="tooltip" data-placement="top" title="Sí" onclick="changeRes(this, '+answerId+', \''+answer+'\')">' +
                                '<span class="visible-lg-inline visible-md-inline hidden-sm hidden-xs"><b>Sí</b></span>' +
                                '<span class="visible-sm-inline visible-xs-inline"><b><i class="fa fa-check"></i></b></span>'+
                            '</button>'
                            ;

            if(answer === 'No'){
                no++;
                dataTableTareas +=
                            '<button id="btn-no-'+answerId+'" class="btn btn-alt btn-primary active" data-toggle="tooltip" data-placement="top" title="No" onclick="changeRes(this, '+answerId+', \''+answer+'\')">' +
                                '<span class="visible-lg-inline visible-md-inline hidden-sm hidden-xs"><b>No</b></span>' +
                                '<span class="visible-sm-inline visible-xs-inline"><b><i class="fa fa-times"></i></b></span>'+
                            '</button>'+
                            '<button id="btn-noSe-'+answerId+'" class="btn btn-alt btn-primary" data-toggle="tooltip" data-placement="top" title="No sé" onclick="changeRes(this, '+answerId+', \''+answer+'\')">' +
                                '<span class="visible-lg-inline visible-md-inline hidden-sm hidden-xs"><b>No sé</b></span>' +
                                '<span class="visible-sm-inline visible-xs-inline"><b><i class="fa fa-question"></i></b></span>'+
                            '</button>'+
                        '</div>'+
                        '</div>';
            }else{
                noSe++;
                dataTableTareas +=
                            '<button id="btn-no-'+answerId+'" class="btn btn-alt btn-primary" data-toggle="tooltip" data-placement="top" title="No" onclick="changeRes(this, '+answerId+', \''+answer+'\')">' +
                                '<span class="visible-lg-inline visible-md-inline hidden-sm hidden-xs"><b>No</b></span>' +
                                '<span class="visible-sm-inline visible-xs-inline"><b><i class="fa fa-times"></i></b></span>'+
                            '</button>'+
                            '<button id="btn-noSe-'+answerId+'" class="btn btn-alt btn-primary active" data-toggle="tooltip" data-placement="top" title="No sé" onclick="changeRes(this, '+answerId+', \''+answer+'\')">' +
                                '<span class="visible-lg-inline visible-md-inline hidden-sm hidden-xs"><b>No sé</b></span>' +
                                '<span class="visible-sm-inline visible-xs-inline"><b><i class="fa fa-question"></i></b></span>'+
                            '</button>'+
                        '</div>' +
                        '</div>';
            }

            dataTableTareas +=
                    '</td>'+
                    '<td>' +
                        '<span id="'+answerId+'" class="col-xs-9 ">'+escapeHtml(comment)+'</span>' +
                        '<div class="col-xs-3">' +
                            '<button id="btnE-'+answerId+'" type="button" class="btn btn-alt btn-default pull-right" data-toggle="tooltip" data-placement="top" title="Editar comentario" onclick="editComment(\''+answerId+'\')"><i class="fa fa-pencil" aria-hidden="true"></i></button>' +
                            '<button id="btnS-'+answerId+'" type="button" class="btn btn-alt btn-default pull-right hidden" data-toggle="tooltip" data-placement="top" title="Guardar comentario"  onclick="saveComment(\''+answerId+'\')"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>' +
                        '</div>' +
                    '</td>'+
                '</tr>';
        }

    });

    dataTableTareas +=
                '</tbody>'+
            '</table>'+
        '</div>';

    dataTableTareas +=
                '</tbody>'+
            '</table>'+
        '</div>';

    $('#table-tareas').html(dataTableTareas);
    TareasDatatables.init();

    var pie_stats = '[{"name":"S\u00ed","y":'+si+'},{"name":"No","y":'+no+'},{"name":"No s\u00e9","y":'+noSe+'}]';
    var bars_stats = '{"categories":'+JSON.stringify(Array.from(new Set(categories)))+',"series":[{"name":"S\u00ed","data":['+si+']},{"name":"No","data":['+no+']},{"name":"No s\u00e9","data":['+noSe+']}]}';
    Charts.init(pie_stats,bars_stats);

    $('#table-resumen').html(dataTableResumen);
    ResumenDatatables.init();

}

function changeRes(btn, id, answer){
    var answerNew = $(btn).text();

    if($.trim(answerNew) !== $.trim(answer)){
        var notify = $.notify('<strong>Actualizando</strong> datos...', {
            allow_dismiss: false,
            showProgressbar: true,
            animate: {
                enter: 'animated fadeInRight',
                exit: 'animated fadeOutRight'
            }
        });

        $.post( "/api/v0/answerHistory/addRes", { answerId: id, answer: answerNew })
            .done(function( data ) {
                $("#btn-group-"+id+" > .btn").removeClass("active");
                $(btn).addClass("active");

                getResume(surveyId, personId);
                notify.update({'type': 'success', 'message': 'Las <strong>gráficas</strong> han cambiado!', 'progress': 100});
            })
            .fail(function(error) {
                console.log( error );
            })
            .always(function() {
                console.log("valid Assigned finished")
            });

    }else{
        console.log('boooo: ' + $.trim(answerNew) +'|'+ $.trim(answer));
    }

}

var commentOld = '';

function editComment(id){
    $('#btnS-'+id).removeClass('hidden');
    $('#btnE-'+id).addClass('hidden');
    commentOld = escapeHtml($("#"+id).text());

    $('#'+id).html('<input type="text" value="'+escapeHtml(commentOld)+'" class="form-control"/>');
}

function saveComment(id){
    var commentNew = $('#'+id).find('input').val().replace(/'/g,"");

    if($.trim(commentNew) !== $.trim(commentOld)){
        $.post( "/api/v0/answerHistory/addComment", { answerId: id, comment: commentNew })
            .done(function( data ) {

                $('#btnE-'+id).removeClass('hidden');
                $('#btnS-'+id).addClass('hidden');

                $('#'+id).text(commentNew);
            })
            .fail(function(error) {
                //console.log( error );
            })
            .always(function() {
                //console.log("valid Assigned finished")
            });
    }else{
        $('#btnE-'+id).removeClass('hidden');
        $('#btnS-'+id).addClass('hidden');

        $('#'+id).text(commentOld);
    }

}

var TareasDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-datatable-tarea').DataTable({
                pageLength: -1,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todo']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-datatable_info').hide();
        }
    };
}();

var ResumenDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-datatable-resumen').DataTable({
                pageLength: -1,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todo']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-datatable_info').hide();
        }
    };
}();

var TablesDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-datatable').DataTable({
                pageLength: -1,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todo']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-datatable_info').hide();
        }
    };
}();

var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '',
    "/": '&#x2F;'
};

function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
}