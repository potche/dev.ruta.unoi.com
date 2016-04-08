/**
 * Actualiza el progreso de la barra de estado
 * @param progress
 */
function setProgress(progress){
    $("#progressbar").animate({width: progress+'%'});
    $("#progressbar").attr("aria-valuenow",progress);

    if(progress==100){
        $("#sendAnswers").show();
    }
}
/**
 * Mostramos la preguntada dada su ID
 * @param id
 */
function showQuest(id){
    $('#questionId_'+ id).show();
}
/**
 * Ocultamos la pregunta dada su ID
 * @param id
 */
function hideQuest(id){
    $('#questionId_'+ id).hide();
}
/**
 * Evento cuando se selecciona una opción de respuesta
 * @param buttonid
 */
function optionClick(buttonid){

    var contestada = false;
    $("button[data-x='"+activeQuestion+"']").each(function(){
        if ($(this).attr('data-selected') === 'true'){
            contestada= true;
        }
        $(this).attr('data-selected', 'false');
        $(this).removeClass("active");
    });

    if (!contestada){
        answered++;
    }


    $('#'+buttonid+"[data-x='"+activeQuestion+"']").attr('data-selected', 'true');
    $('#'+buttonid+"[data-x='"+activeQuestion+"']").addClass("active");
    $("#question_"+activeQuestion).addClass("active");
    $("#question_"+activeQuestion+" span").first().html('<i class="fa fa-check"></i>');
    $("#mainComment").show("slow");
    var totalProgress = (answered / totalQ)*100;
    setProgress( totalProgress );
}
/**
 * Establecemos la pregunta activa
 * @param id
 */
function setCurrentQ(id){
    if(activeQuestion > 0){
        hideQuest(activeQuestion);
        $("#question_"+activeQuestion).removeClass("active");
    }
    showQuest(id);
    switch(id){
        case 1:
            $("#prevButton").hide();
            $("#nextButton").show();
            break;
        case totalQ:
            $("#prevButton").show();
            $("#nextButton").hide();
            break;
        default:
            $("#prevButton").show();
            $("#nextButton").show();
    }
    $("#questionNumber").html( id + " de " + totalQ );
    var totalProgress = (answered / totalQ) * 100;
    setProgress( totalProgress );
    activeQuestion = id;
    var allHeight=0;
    for(var i=1; i<activeQuestion; i++){
        allHeight+=$("#question_"+activeQuestion).outerHeight()-parseInt($("#question_"+activeQuestion).css('borderWidth'));
    }
    $(".list-group").animate({scrollTop:allHeight}, '500', 'swing');
    $("#question_"+activeQuestion).addClass("active");
    $("button[data-x='"+activeQuestion+"']").each(function() {
        if ($(this).attr('data-selected') == 'true'){
            $(this).addClass("active");
        }
    });
}
/**
 * Obtenemos las preguntas y sus respuestas.
 * @returns {Array}
 */
function getAnswers(){
    var results=[];
    for(var i=1; i<=totalQ; i++){
        var obj = {"surveyid": 0, "optionxquestionId": 0,  "questionId": 0,  "Answer": "", "Comment": ""};
        $("button[data-x='"+i+"']").each(function() {
            if ($(this).attr('data-selected') == 'true'){
                obj.optionxquestionId = parseInt($(this).attr("id").replace("option_",""));
                obj.Answer = $(this).attr('value');
            }
        });
        obj.questionId = parseInt($('#questionId_' + i).attr('data-questionId'));
        obj.Comment = $('#mainComment_' + i).val();
        obj.surveyid = survId;
        if(obj.optionxquestionId !== 0){
            results.push(obj);
        }
    }
    return results;
}
/**
 * Salvamos la evaluación
 */
function saveEvaluacion(){

    $("div.modal-body").html("<p><b>Se están enviando tus respuestas, por favor no recargues esta página</b></p>");
    $(".modal-footer").remove();

    var postData = getAnswers();
    $.ajax({
        url: urlSave,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(postData)
    }).fail(function (data) {
        if(typeof data !== 'undefined'){

        }
    }).done(function (data) {

    	try {
    		ga('send', 'event', 'Evaluacion', 'Completada','Fin', 1);
    	}catch(err) {
    		
    	}
        window.location.href=urlRedirect;
    });
}

//Inicializamos las funciones y eventos, mostramos la primera pregunta.
$(document).ready(function() {

    $("*").dblclick(function(e){

        console.log("Did nothing");
        e.stopPropagation();
        e.preventDefault();
    });

    setProgress(0);
    setCurrentQ(1);
    $("#prevButton").click(function(){

        if (activeQuestion > 1 ){
            setCurrentQ( activeQuestion - 1 );
        }
    });
    $("#nextButton").click(function(){
        if (activeQuestion < totalQ){
            setCurrentQ( activeQuestion +1 );
        }
    });
    $(".optionbtn").click(function(e){
        optionClick(e.currentTarget.id);
    });

    $(".questionButton").click(function(e){
        var id = e.currentTarget.id.replace("question_","");
        setCurrentQ(parseInt(id));
    });

    $('#confirmar').click(function(){
        saveEvaluacion();
    });
});