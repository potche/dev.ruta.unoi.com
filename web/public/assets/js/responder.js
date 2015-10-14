/**
 * Created by cgutierrezy on 9/23/15.
 */


$(document).ready(function(){
    index = 0;
    var currentQuestionId = null;

    if( items.length>0 ){
        setMain();
    }

});



/*
* This function sets all inital info for the main question: question, progress bar, options,
* */
function setMain(){
    setCurrentQuestion(items[0].questionid);
    setProgress(0);

    //adding the functionality to the Questions side bar for change the question in the main question
    $(".questionButton").click(function(e){
        setCurrentQuestion( e.currentTarget.id.replace("question_","") );
    });

    $("#prevButton").click(function(){
        if (index>0){
            index--;    
            setCurrentQuestion( items[index].questionid );
        }
    });

    $("#nextButton").click(function(){        
        if (index<items.length-1){
            index++;
            setCurrentQuestion( items[index].questionid );
            
        }        
    });

    $("#mainComment").focusout(function(){
        updateComment();
    });


    $("#sendAnswers").click(function () {
        var allAnswers = JSON.stringify(answersItems);
        $("#answers").attr('value',allAnswers);
        $("#form").submit();
    });
    $("#sendAnswers").hide();
}




/*
 * this function sets the info for the main question
 * receives an id (just the number) from the items object
 * */
function setCurrentQuestion(question_id){
    
    currentQuestionId = question_id;
    currentAnswer = null;

    // get the index of Items array of the current question
    index = items.indexOf( $.grep(items, function(e){ return e.questionid == question_id; })[0] );

    //toogle the visualization of next/previuos buttons
    
    switch(index){
        case 0:
            $("#prevButton").hide();
            break;
        case items.length-1:
            $("#nextButton").hide();
            break;
        default: 
            $("#prevButton").show();
            $("#nextButton").show();            
    }

    // get the current item from Items
    currentItem = $.grep(items, function(e){ return e.questionid == question_id; })[0];


    /* **** Creating andt setting HTML elements ** */
    $(".questionButton").removeClass("active");
    $("#question_"+currentQuestionId).addClass("active");
    $("#mainQuestion").html( (index+1) +".- "+ currentItem.question );
    $("#questionNumer").html( (index+1) + " de " + items.length );

    var allHeight=0;
    for(var i=0; i<index; i++){
        allHeight+=$("#question_"+items[i].questionid).outerHeight()-parseInt($("#question_"+items[i].questionid).css('borderWidth'));
    }

    $(".list-group").animate({scrollTop:allHeight}, '500', 'swing');

    //create the options
    $("#mainOptions").empty();
    for (var i=0; i<currentItem.options.length; i++ ){
        $("#mainOptions").append(' <div class="btn-group" role="group">'+
            '<button id="option_'+currentItem.options[i].optionxquestionid+
            '" type="button" class="btn btn-info optionbtn ">'+ currentItem.options[i].optionText+
            '</button>'+
            '</div>');
    }
    
    // adding the actions for each option
    $(".optionbtn").click(function(e){
        optionClick(e.currentTarget.id);
    });

    $(".optionbtn").focusout(function () {
        $(this).addClass('active');
    } );

    //if this question has been answered before then we set the previous content

    if( isAnswered( question_id ) ) {

        currentAnswer = $.grep(answersItems, function(e){ return e.questionid == question_id; })[0];
        $("#option_"+currentAnswer.OptionXQuestion_id).addClass("active");

        //$("#option_"+currentAnswer.OptionXQuestion_id).prop("checked");
        $("#mainComment").val( currentAnswer.comment );
        $("#mainComment").show("slow");
        //$("#test").html("questionid:"+currentAnswer.questionid+"<br>Person_Id:"+currentAnswer.Person_Id+"<br>OptionXQuestion_id:"+currentAnswer.OptionXQuestion_id+"<br>answer:"+currentAnswer.answer+"<br>comment:"+currentAnswer.comment);
    }else{
        //$("#mainComment").hide("slow");
        $("#mainComment").val('');

    }

}

/*
* this is the action for each option button clicked
* */
function optionClick(buttonid){


    // if the question is already answered then the answer will be deleted
    if( isAnswered( currentItem.questionid ) ) {
        if(answersItems.length==1){
            answersItems = [];
        }else {
            answersItems.splice(index, 1);
        }
        $(".optionbtn").removeClass("active");
    }else{
        $("#question_"+currentItem.questionid+" span").first().html('<i class="fa fa-check"></i>');
    }

    answersItems.push( newAnswer(buttonid) );

    $("#mainComment").show("slow");

    var totalProgress = (answersItems.length / items.length)*100;

    setProgress( totalProgress );

}

/*
* this function updates the value of the textarea for the comment in the current answer
* */
function updateComment(){
    var currentAnswer = $.grep(answersItems, function(e){ return e.questionid == currentItem.questionid; })[0];
    currentAnswer.comment = $("#mainComment").val();
}

/*
* This function creates a new Answer item with the current info
* */
function newAnswer(buttonId){
    var newAnswer={
        'questionid': currentItem.questionid,
        'answer': $("#"+buttonId).html() ,
        'comment': $("#mainComment").val(),
        'OptionXQuestion_id':buttonId.replace("option_","")
    };

    return newAnswer;
}

/*
* This function returns a TRUE in case the question (q_id) has been already answered
* */
function isAnswered(q_id){
    return $.grep(answersItems, function(e){ return e.questionid == q_id ; }).length > 0;
}

/*
* This function sets the value for the progress bar
* receives a number (or string) between 0 and 100
* */
function setProgress(progress){

    $("#progressbar").animate({
        width: progress+'%'
    });
    $("#progressbar").attr("aria-valuenow",progress);

    if(progress==100){
        $("#sendAnswers").show();
    }
}