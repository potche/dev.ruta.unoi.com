/**
 * Created by isra on 27/05/16.
 */
function saveAnswer(btn, interactionId) {
    var answerRow = $(btn).attr('id').split('-');
    $(btn).addClass('active').siblings().removeClass('active');
    if($('#comment-'+answerRow[2]).val()){
        var comment = $('#comment-'+answerRow[2]).val();
    }else {
        var comment = $('#comment-'+answerRow[2]).text();
    }

    if($.trim(answerRow[1]) !== $.trim($('#answerOrigin-'+answerRow[2]).val())) {
        save(answerRow[2], answerRow[1], comment, interactionId);
        $('#answerOrigin-'+answerRow[2]).val(answerRow[1]);
    }
}


function editComment(questionId) {
    $('#btnS-'+questionId).removeClass('hidden');
    $('#btnE-'+questionId).addClass('hidden');
    $('#comment-'+questionId).replaceWith("<input type='text' id='comment-"+questionId+"' value='"+$('#comment-'+questionId).text()+"' class='form-control'>" );
    $('#comment-'+questionId).focus();
}

function saveComment(questionId, interactionId) {
    var answerOrigin = $('#answerOrigin-'+questionId).val();
    var comment = $('#comment-'+questionId).val();
    var commentOrigin = $('#commentOrigin-'+questionId).val();

    if($.trim(comment) !== $.trim(commentOrigin)) {
        save(questionId, answerOrigin, comment, interactionId);
        $('#commentOrigin-'+questionId).val(comment);
    }else {
        $('#btnE-'+questionId).removeClass('hidden');
        $('#btnS-'+questionId).addClass('hidden');
        $('#comment-'+questionId).replaceWith("<span id='comment-"+questionId+"'>" + $('#comment-'+questionId).val() + "</span>");
    }

}

$('.commentInteraction').on('blur', function (e) {
    var disposicion = $(this)[0].id.split('-');

    var answerOrigin = $('#answerOrigin-'+disposicion[1]).val();
    var comment = $('#comment-'+disposicion[1]).val();
    var commentOrigin = $('#commentOrigin-'+disposicion[1]).val();

    if($.trim(comment) !== $.trim(commentOrigin)) {
        save(disposicion[1], answerOrigin, comment, interactionId);
        $('#commentOrigin-'+disposicion[1]).val(comment);
    }
});

function save(questionId, answer, comment, interactionId) {
    $('#commentOrigin-'+questionId).val(comment);
    var notify = $.notify('<strong>Actualizando</strong> datos...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });

    $.post("/api/v0/interaction/saveAnswer", {
        questionId: questionId,
        answer: answer,
        comment: comment,
        interactionId: interactionId
    })
        .done(function (data) {
            if (data) {
                console.log(data)
                notify.update({
                    'type': 'success',
                    'message': 'Los <strong>datos</strong> se han cambiado!',
                    'progress': 100
                });
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            console.log("finished");
        });
}

$('#finalizar').on('click',function (e) {
    $('#finishModal').modal();
});

function finishedInteraction(interactionId) {
    $('#finishModal').modal('hide');
    var notify = $.notify('<strong>Finalizando</strong> la Interacci&oacute;n Constructiva...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });
    $.post( "/api/v0/interaction/finish", {interactionId: interactionId})
        .done(function(data) {
            if(data.status == 'OK'){
                notify.update({
                    'type': 'success',
                    'message': 'La <strong>Interacción</strong> se han finalizado!',
                    'progress': 100
                });
            }
        })
        .fail(function() {
            console.log('error')
        })
        .always(function() {
            console.log( "finished" );
            window.location.href = uriBack;
        });
}