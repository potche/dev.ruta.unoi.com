/**
 * Created by isra on 27/05/16.
 */
function saveAnswer(btn, observationId) {
    var answerRow = $(btn).attr('id').split('-');
    $(btn).addClass('active').siblings().removeClass('active');
    if($('#comment-'+answerRow[2]).val()){
        var comment = $('#comment-'+answerRow[2]).val();
    }else {
        var comment = $('#comment-'+answerRow[2]).text();
    }

    if($.trim(answerRow[1]) !== $.trim($('#answerOrigin-'+answerRow[2]).val())) {
        save(answerRow[2], answerRow[1], comment, observationId);
        $('#answerOrigin-'+answerRow[2]).val(answerRow[1]);
    }
}


function editComment(questionId) {
    $('#btnS-'+questionId).removeClass('hidden');
    $('#btnE-'+questionId).addClass('hidden');
    $('#comment-'+questionId).replaceWith("<input type='text' id='comment-"+questionId+"' value='"+$('#comment-'+questionId).text()+"' class='form-control'>" );
    $('#comment-'+questionId).focus();
}

function saveComment(questionId, observationId) {
    var answerOrigin = $('#answerOrigin-'+questionId).val();
    var comment = $('#comment-'+questionId).val();
    var commentOrigin = $('#commentOrigin-'+questionId).val();

    if($.trim(comment) !== $.trim(commentOrigin)) {
        save(questionId, answerOrigin, comment, observationId);
        $('#commentOrigin-'+questionId).val(comment);
    }else {
        $('#btnE-'+questionId).removeClass('hidden');
        $('#btnS-'+questionId).addClass('hidden');
        $('#comment-'+questionId).replaceWith("<span id='comment-"+questionId+"'>" + $('#comment-'+questionId).val() + "</span>");
    }

}

function save(questionId, answer, comment, observationId) {
    $('#commentOrigin-'+questionId).val(comment);
    var notify = $.notify('<strong>Actualizando</strong> datos...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });

    $.post(HttpHost + baseUrl + "/api/v0/observation/saveAnswer", {
        questionId: questionId,
        answer: answer,
        comment: comment,
        observationId: observationId
    })
        .done(function (data) {
            if (data) {
                console.log(data)
                notify.update({
                    'type': 'success',
                    'message': 'Los <strong>datos</strong> se han cambiado!',
                    'progress': 100
                });
                if($('#comment-'+questionId).val()){
                    $('#btnE-'+questionId).removeClass('hidden');
                    $('#btnS-'+questionId).addClass('hidden');
                    $('#comment-'+questionId).replaceWith("<span id='comment-"+questionId+"'>" + $('#comment-'+questionId).val() + "</span>");
                }
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            console.log("finished");
        });
}

$('button#feedback-R').hover(function(e){
        $(this).animate({'right':'-1px'}, 500);
    },
    function(e){
        $(this).animate({'right':'-10px'}, 500);
    });

$('button#feedback-R').click(function(){
    console.log('hola');
    $("#acciones").removeClass('hidden');
    $("#cuestionario").addClass('hidden');

    $("#feedback-L").removeClass('hidden');
    $("#feedback-R").addClass('hidden');
});

$('button#feedback-L').hover(function(e){
        $(this).animate({'left':'-1px'}, 500);
    },
    function(e){
        $(this).animate({'left':'-10px'}, 500);
    });

$('button#feedback-L').click(function(){
    console.log('hola');
    $("#cuestionario").removeClass('hidden');
    $("#acciones").addClass('hidden');

    $("#feedback-R").removeClass('hidden');
    $("#feedback-L").addClass('hidden');
});