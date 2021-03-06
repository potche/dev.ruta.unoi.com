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

$('.commentObservation').on('blur', function (e) {
    var disposicion = $(this)[0].id.split('-');
    
    var answerOrigin = $('#answerOrigin-'+disposicion[1]).val();
    var comment = $('#comment-'+disposicion[1]).val();
    var commentOrigin = $('#commentOrigin-'+disposicion[1]).val();

    if($.trim(comment) !== $.trim(commentOrigin)) {
        save(disposicion[1], answerOrigin, comment, observationId);
        $('#commentOrigin-'+disposicion[1]).val(comment);
    }
});

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
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            console.log("finished");
        });
}


function getActivity() {
    $.get( HttpHost+baseUrl+"/api/v0/observation/activity/"+observationId)
        .done(function(data) {
            if(data){
                var row = '';
                $.each( data, function( key, val ) {
                    //console.log(val);
                    row += '<tr>' +
                        '<td class="text-center" width="20%">' +
                        '<span>' + paserDate(val.startActivity.date) + '</span>' +
                        '</td>' +
                        '<td class="text-justify" width="50%">' +
                        '<span>' + val.activity + '</span>' +
                        '</td>' +
                        '<td class="text-center" width="20%">';

                    if(val.endActivity){
                        row += '<span>' + paserDate(val.endActivity.date) + '</span>';
                    }else{
                        row += '<button type="button" class="btn btn-primary" onclick="finishedActivity('+val.observationActivityId+')"><i class="fa fa-check" aria-hidden="true"></i> Teminar</button>';
                    }

                    row += '</td>' +
                        '<td class="text-center" width="10%">' +
                        '<div class="btn-group">'+
                        '<a href="javascript:void(0)" onclick="editActivity(\'' + paserDate(val.startActivity.date) + '\',\'' + val.activity + '\','+val.observationActivityId+')" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil" aria-hidden="true"></i></a>'+
                        '<a href="javascript:void(0)" onclick="confirmDeleteActivity('+val.observationActivityId+')" title="Borrar" class="btn btn-xs btn-info"><i class="fa fa-times" aria-hidden="true"></i></a>'+
                        '</div>' +
                        '</td>' +
                        '</tr>'
                });
                $('#activityHistory').html(row);
            }
        })
        .fail(function() {
            console.log('error')
        })
        .always(function() {
            console.log( "finished" );
        });
}

$('#ActivityFrm').on('submit',function(e){
    e.preventDefault();
    var startActivity = $.trim($('#observationActivityId').val());
    var activity = $('#activity-'+observationId).val();
    
    if( (startActivity.length !== 0 ) && ( $.trim(activity).length !== 0) ){
        $.post(HttpHost + baseUrl + "/api/v0/observation/saveActivity", {startActivity: startActivity, activity: activity, observationId: observationId})
            .done(function (data) {
                if (data) {
                    //console.log(data);

                    $('#start-' + observationId).html('');
                    $('#observationActivityId').val('');
                    $('#activity-'+observationId).val('');

                    $('#subActivity').prop("disabled", true);
                }
            })
            .fail(function () {
                console.log('error')
            })
            .always(function () {
                getActivity();
                console.log("finished");
            });
    }

});

function editActivity(startActivity,activity,observationActivityId){
    //console.log(observationActivityId);

    $('#start').text(startActivity);
    $('#activity').val(activity);
    $('#editObservationActivityId').val(observationActivityId);
    $('#ActivityFrm').addClass('hidden');
    $('#editActivityFrm').removeClass('hidden');
}

$('#editActivityFrm').on('submit',function(e){
    e.preventDefault();
    var activity = $('#activity').val();
    var observationActivityId = $('#editObservationActivityId').val();
    var notify = $.notify('<strong>Editando</strong> registro...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });
    if( $.trim(activity).length !== 0 ){
        $.post(HttpHost + baseUrl + "/api/v0/observation/editActivity", {observationActivityId: observationActivityId, activity: activity})
            .done(function (data) {
                if (data.status) {
                    //console.log(data);
                    notify.update({
                        'type': 'success',
                        'message': 'El <strong>registro</strong> se han actualizado!',
                        'progress': 100
                    });

                    $('#start').html('');
                    $('#editObservationActivityId').val('');
                    $('#activity').val('');
                    $('#ActivityFrm').removeClass('hidden');
                    $('#editActivityFrm').addClass('hidden');
                    $('#subEditActivity').prop("disabled", true);
                    $('#subActivity').prop("disabled", true);
                }
            })
            .fail(function () {
                console.log('error')
            })
            .always(function () {
                getActivity();
                console.log("finished");
            });
    }

});

function finishedActivity(observationActivityId) {
    //console.log(observationActivityId);
    var notify = $.notify('<strong>Finalizando</strong> Actividad...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });
    $.post(HttpHost + baseUrl + "/api/v0/observation/finishedActivity", {observationActivityId: observationActivityId})
        .done(function (data) {
            if (data.status) {
                //console.log(data);
                notify.update({
                    'type': 'success',
                    'message': 'La <strong>Actividad</strong> se han Finalizado!',
                    'progress': 100
                });

                $('#start').html('');
                $('#editObservationActivityId').val('');
                $('#activity').val('');
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            getActivity();
            console.log("finished");
        });
}

function confirmDeleteActivity(observationActivityId) {
    $('#genericTitle').html('Confirmación');
    $('#genericBody').html('Realmente desea <b>eliminar</b> la Actividad');
    $('#genericOk').attr('data-action', 'deleteActivity');
    $('#genericOk').attr('data-result', observationActivityId);

    $('#genericModal').modal();
}

function confirmDeleteGallery(observationActivityId, typeImg) {
    $('#genericTitle').html('Confirmación');
    $('#genericBody').html('Realmente desea <b>eliminar</b> la Foto');
    $('#genericOk').attr('data-action', 'deleteGallery');
    $('#genericOk').attr('data-img', typeImg);
    $('#genericOk').attr('data-result', observationActivityId);

    $('#genericModal').modal();
}

$('#genericOk').click(function () {
    switch ($('#genericOk').attr('data-action')){
        case 'deleteActivity':
            deleteActivity($('#genericOk').attr('data-result'));
            $('#genericModal').modal('hide');
            break;
        case 'deleteGallery':
            if($('#genericOk').attr('data-img') == 'A'){
                deleteImgA($('#genericOk').attr('data-result'));
                $('#genericModal').modal('hide');

            }else {
                deleteImgB($('#genericOk').attr('data-result'));
                $('#genericModal').modal('hide');
            }
            break;
        default:
            break;
    }

});

function deleteActivity(observationActivityId){
    //console.log(observationActivityId);
    var notify = $.notify('<strong>Eliminando</strong> registro...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });
    $.post(HttpHost + baseUrl + "/api/v0/observation/deleteActivity", {observationActivityId: observationActivityId})
        .done(function (data) {
            if (data.status) {
                //console.log(data);
                notify.update({
                    'type': 'success',
                    'message': 'El <strong>registro</strong> se han eliminado!',
                    'progress': 100
                });

                $('#start').html('');
                $('#editObservationActivityId').val('');
                $('#activity').val('');
                $('#ActivityFrm').removeClass('hidden');
                $('#editActivityFrm').addClass('hidden');
                $('#subEditActivity').prop("disabled", true);
                $('#subActivity').prop("disabled", true);
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            getActivity();
            console.log("finished");
        });
}


$(".seleccionable").click(function(){

    $disposicion = $(this)[0].id.split('-');
    
    $('.seleccionable').removeClass('selected');
    $(this).addClass('selected');

    var notify = $.notify('<strong>Guardando</strong> disposición...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });

    $.post( HttpHost+baseUrl+"/api/v0/observation/saveDisposition", {disposition: $disposicion[1], observationId: observationId})
        .done(function(data) {
            if(data){

                console.log(data);
                notify.update({
                    'type': 'success',
                    'message': 'Se <strong>ha</strong> guardado exitosamente!',
                    'progress': 100
                });

            }
        })
        .fail(function() {
            console.log('error')
        })
        .always(function() {
            console.log( "finished" );
        });
});

function getDisposition() {
    $.get( HttpHost+baseUrl+"/api/v0/observation/disposition/"+observationId)
        .done(function(data) {
            if(data.disposition){
                if(data){
                    $('#label-'+data.disposition +'-'+ observationId).addClass('selected');
                }
            }
        })
        .fail(function() {
            console.log('error')
        })
        .always(function() {
            console.log( "finished" );
        });
}



$('#imageA').change(function () {

    if($("#imageA").val()) {
        $('#uploadA').prop("disabled", false);
        $('#findA').removeClass('btn-info');
        $('#findA').addClass('btn-default');
    }else {
        console.log("Not file");
        $('#uploadA').prop("disabled", true);
    }
});

$('#imageB').change(function () {

    if($("#imageB").val()) {
        $('#uploadB').prop("disabled", false);
        $('#findB').removeClass('btn-info');
        $('#findB').addClass('btn-default');
    }else {
        console.log("Not file");
        $('#uploadB').prop("disabled", true);
    }
});

$('.upload-all').click(function(){
    //submit all form
    $('form').submit();
});
$('.cancel-all').click(function(){
    //submit all form
    $('form .cancel').click();
});

$('#A').on('submit',function(e){
    e.preventDefault();
    uploadImage($(this));
});

$('#B').on('submit',function(e){
    e.preventDefault();
    uploadImage($(this));
});

function uploadImage($form){
    $form.find('.progress-bar').removeClass('progress-bar-success')
        .removeClass('progress-bar-danger');
    var formdata = new FormData($form[0]); //formelement
    var request = new XMLHttpRequest();
    //progress event...
    request.upload.addEventListener('progress',function(e){
        var percent = Math.round(e.loaded/e.total * 100);
        $form.find('.progress-bar').width(percent+'%').html(percent+'%');
    });
    //progress completed load event
    request.addEventListener('load',function(e){
        console.log(e);
        $form.find('.progress-bar').addClass('progress-bar-success').html('cargar completada....');
        getGallery();
        $form.find('.progress-bar').width('0%').html('0%');
        $form.find('.progress-bar').removeClass('progress-bar-success').html('');
    });
    request.open('post', HttpHost+baseUrl+'/api/v0/observation/saveImg');
    request.send(formdata);
    $form.on('click','.cancel',function(){
        request.abort();
        $form.find('.progress-bar')
            .addClass('progress-bar-danger')
            .removeClass('progress-bar-success')
            .html('cargar abortada...');
    });
}

function getGallery() {
    $.get( HttpHost+baseUrl+"/api/v0/observation/gallery/"+observationId)
        .done(function(data) {
            if(data){
                $.each( data, function( key, val ) {
                    //var urlImage = HttpHost+'/'+val.ruta;
                    var urlImage = val.ruta;
                    if(val.type.toUpperCase() === 'A'){
                        //console.log(val.ruta);
                        $('#imgA').attr('src',urlImage);
                        $('#hrefA').attr('href',urlImage);
                        $('.galleryA').css("display", "block");
                        $('#fotoA').hide();
                    }

                    if(val.type.toUpperCase() === 'B'){
                        //console.log(val.ruta);
                        $('#imgB').attr('src',urlImage);
                        $('#hrefB').attr('href',urlImage);
                        $('.galleryB').css("display", "block");
                        $('#fotoB').hide();
                    }

                });
            }
        })
        .fail(function() {
            console.log('error')
        })
        .always(function() {
            console.log( "finished" );
        });
}

function editImgA(){
    $('.galleryA').hide();
    $('#A').find('input').val('');
    $('#editA').removeClass('hide');
    $('#fotoA').show();
}

function cancelEditImgA(){
    $('#fotoA').hide();
    $('#editA').addClass('hide');
    $('.galleryA').show();
}

function deleteImgA(observationId){
    $('.galleryA').hide();
    console.log($('#imgA').attr('src'));
    $('#A').find('input').val('');
    $('#fotoA').show();

    $.post(HttpHost + baseUrl + "/api/v0/observation/deleteImg", {observationId: observationId, type: 'A'})
        .done(function (data) {
            if (data) {
                console.log(data);
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            console.log("finished");
        });
}

function editImgB(){
    $('.galleryB').hide();
    $('#B').find('input').val('');
    $('#editB').removeClass('hide');
    $('#fotoB').show();
}

function cancelEditImgB(){
    $('#fotoB').hide();
    $('#editB').addClass('hide');
    $('.galleryB').show();
}

function deleteImgB(observationId){
    $('.galleryB').hide();
    console.log($('#imgB').attr('src'));
    $('#B').find('input').val('');
    $('#fotoB').show();

    $.post(HttpHost + baseUrl + "/api/v0/observation/deleteImg", {observationId: observationId, type: 'B'})
        .done(function (data) {
            if (data) {
                console.log(data);
            }
        })
        .fail(function () {
            console.log('error')
        })
        .always(function () {
            console.log("finished");
        });
}

$('#closeB').on('click',function(e){
    $('#widgetFotoB').hide();
    $('#fotoB').show();
});


$('#finalizar').on('click',function (e) {
    $('#finishModal').modal();
});

function finishedObservation(observationId) {
    $('#finishModal').modal('hide');
    var notify = $.notify('<strong>Finalizando</strong> observaci&oacute;n...', {
        allow_dismiss: false,
        showProgressbar: true,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        }
    });
    $.post( HttpHost+baseUrl+"/api/v0/observation/finish", {observationId: observationId})
        .done(function(data) {
            if(data.status == 'OK'){
                notify.update({
                    'type': 'success',
                    'message': 'La <strong>observación</strong> se han finalizado!',
                    'progress': 100
                });

                $.get( HttpHost+baseUrl+"/api/v0/observation/sendMail/"+observationId)
                    .done(function(data) {

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

    $('body').removeClass().addClass('animation-fadeInLeft');

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

    $('body').removeClass().addClass('animation-fadeInRight');

    $("#feedback-R").removeClass('hidden');
    $("#feedback-L").addClass('hidden');
});