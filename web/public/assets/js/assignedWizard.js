/**
 * Created by isra on 20/04/16.
 */

var grado = $('#val_grado');
var programa = $('#val_programa');
var grupo = $('#val_grupo');
var gradoOk = false;
var grupoOk = false;
var programaOk = false;

grado.change(function(){
    if(grado.val() !== '0'){
        gradoOk = true;
    }else{
        gradoOk = false;
    }
    activeButton();
});

programa.change(function(){
    if(programa.val() !== '0'){
        programaOk = true;
    }else{
        programaOk = false;
    }
    activeButton();
});

grupo.change(function(){
    if($.trim(grupo.val()) !== ''){
        grupoOk = true;
    }else{
        grupoOk = false;
    }
    activeButton();
});

function activeButton(){
    if(gradoOk && programaOk && grupoOk){
        $('#add').removeClass('disabled');
    }else{
        $('#add').addClass('disabled');
    }
}

function addAssigned(personIdS) {

    var gradoArr = grado.val().split('|');

    $.post( "/api/v0/assigned/add", { schoolLevelId: gradoArr[0], gradeId: gradoArr[1], groupId: grupo.val(), programId: programa.val(), personId: personIdS })
        .done(function( data ) {
            if(data.status == 'ok' ){
                grado.val('');
                grupo.val('');
                programa.val('');
                $('#add').addClass('disabled');
                getAssigned(personIdS);
            }
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            console.log("addAssigned finished")
        });
}

function deleteAssigned($personAssignedId, personIdS) {
    console.log($personAssignedId);

    $.post( "/api/v0/assigned/remove", { personAssignedId: $personAssignedId })
        .done(function( data ) {
            if(data.status == 'ok' ){
                grupo.val('');
                grado.val('');
                programa.val('');
                $('#add').addClass('disabled');
                getAssigned(personIdS);
            }
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            console.log("addAssigned finished")
        });

}

$('#closeAssigned').click(function() {
    $('#divContentAssigned').toggle();
});

function valGroup(personIdS){

    var $groupId = grupo.val().toUpperCase();
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/groups",
        success: function (resp) {
            if (resp.length !== 0) {
                if($.inArray($groupId, resp) !== -1){
                }else{
                    addGroup($groupId);
                }

                addAssigned(personIdS);
                assignedOk(personIdS);
            }
        }
    });

}

function addGroup($groupId){
    $.post( "/api/v0/assigned/addGroup", { groupId: $groupId })
        .done(function( data ) {
            console.log(data.status);
            if(data.status === 'ok'){
                $.ajax({
                    type: "JSON",
                    url: "/api/v0/assigned/groups",
                    success: function (resp) {
                        if (resp.length !== 0) {
                            $('.input-typeaheadGroup').typeahead('destroy');
                            $('.input-typeaheadGroup').typeahead({source: resp});
                        }
                    }
                });
            }
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            console.log("addGroup finished")
        });
}

function assignedOk(personIdS){
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/personProfile/" + personIdS,
        success: function (res) {
            if (res.length !== 0) {
                var ok = false;
                $.each(res, function (j, item) {
                    if (item['Ok'] === '0') {
                        ok = false;
                        return false;
                    }else{
                        ok = true;
                    }
                });

                if(ok === true){
                    $.post( "/api/v0/assigned/ok", { personId: personIdS })
                        .done(function( data ) {
                            if (data.status === 'ok') {
                                $('#assigned').modal('hide');
                            }
                        });
                }
            }
        }
    });
}

function assigned(personIdS) {

    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/personProfile/" + personIdS,
        success: function (res) {
            if (res.length !== 0) {
                var level = '';
                var levelF = '';
                var schoollevelid = '';
                $.each(res, function (j, item) {
                    level += item['schoollevel'] + ", ";
                    schoollevelid += item['schoollevelid'] + ",";
                    if(item['Ok'] === '0'){
                        levelF += item['schoollevel'] + ", ";
                    }
                });

                gradesHTML(schoollevelid.slice(0, -1));
                groupsHTML();
                programsHTML();

                $('#titleAssigned').html(level.slice(0, -2));
                $('#titleAssignedF').html(levelF.slice(0, -2));
                getAssigned(personIdS);
                $('#assigned').modal();
            }
        }
    });

}

function gradesHTML(schoollevelid){
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/grades/"+schoollevelid,
        success: function (resp) {
            if (resp.length !== 0) {
                var grade = '<option value="0">Selecciona un Grado...</option>';
                $.each(resp, function (j, item) {
                    grade += "<option value = '" + item['schoolLevelId'] +"|" + item['gradeId'] +"'>" +
                        item['nameGrade'] +
                        "</option>";
                });
                $('#val_grado').html(grade);
            }
        }
    });
}

function groupsHTML(){
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/groups",
        success: function (resp) {

            if (resp.length !== 0) {
                $('.input-typeaheadGroup').typeahead({source: resp});
            }
        }
    });
}

function programsHTML(){
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/programs",
        success: function (respu) {
            if (respu.length !== 0) {
                var option = '<option value="0">Selecciona un Programa...</option>';
                $.each(respu, function (j, item) {
                    option += "<option value = '" + item['programId'] +"'>" +
                        item['nameProgram'] +
                        "</option>";
                });

                $('#val_programa').html(option);
            }
        }
    });
}

function getAssigned(personIdS){
    $.ajax({
        type: "JSON",
        url: "/api/v0/assigned/personById/" + personIdS,
        success: function (res) {
            var row = '';
            $.each(res, function (j, item) {
                row += "<tr><td>"+item['nameGrade'] + "</td><td>"+item['groupId'] + "</td><td>"+item['nameProgram'] + "</td><td><button class='btn btn-danger' onclick='deleteAssigned("+item['personAssignedId'] + ",\""+ personIdS +"\")'><i class='fa fa-times'></i></button></td></tr>";
            });
            $('#rowAssigned').html(row);
        }
    });
}

function validAssigned($personIdS){
    $.post( "/api/v0/assigned/validAssigned", { personId: $personIdS })
        .done(function( data ) {
            if(data.status === false){
                assigned($personIdS);
            }
        })
        .fail(function(error) {
            console.log( error );
        })
        .always(function() {
            console.log("valid Assigned finished")
        });
}