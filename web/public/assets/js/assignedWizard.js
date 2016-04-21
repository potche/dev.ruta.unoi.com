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
        console.log('ok');
        $('#add').removeClass('disabled');
    }else{
        console.log('booo');
        $('#add').addClass('disabled');
    }
}

$('#add').click(function () {
    var gradoArr = grado.val().split('|');
    var grupoArr = programa.val().split('|');
    console.log(gradoArr[1]+' Grupo: '+grupo.val()+' Programa: '+grupoArr[1]);
});

$('#closeAssigned').click(function() {
    $('#divContentAssigned').toggle();
});

function assigned(personIdS) {

    $.ajax({
        type: "JSON",
        url: "/api/v0/catalog/personProfile/" + personIdS,
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

                $.ajax({
                    type: "JSON",
                    url: "/api/v0/catalog/grades/"+schoollevelid.slice(0, -1),
                    success: function (resp) {
                        if (resp.length !== 0) {
                            var grade = '<option value="0">Selecciona un Grado...</option>';
                            $.each(resp, function (j, item) {
                                grade += "<option value = '" + item['gradeId'] + "|" + item['nameGrade'] +"'>" +
                                    item['nameGrade'] +
                                    "</option>";
                            });
                            $('#val_grado').html(grade);
                        }
                    }
                });

                $.ajax({
                    type: "JSON",
                    url: "/api/v0/catalog/groups",
                    success: function (resp) {
                        if (resp.length !== 0) {
                            $('.input-typeaheadGroup').typeahead({source: resp});
                        }
                    }
                });

                $.ajax({
                    type: "JSON",
                    url: "/api/v0/catalog/programs",
                    success: function (respu) {
                        if (respu.length !== 0) {
                            var option = '<option value="0">Selecciona un Programa...</option>';
                            $.each(respu, function (j, item) {
                                option += "<option value = '" + item['programId'] + "|"+item['nameProgram'] +"'>" +
                                    item['nameProgram'] +
                                    "</option>";
                            });

                            $('#val_programa').html(option);
                        }
                    }
                });
                $('#titleAssigned').html(level.slice(0, -2));
                $('#titleAssignedF').html(levelF.slice(0, -2));
                getAssigned(personIdS);
                $('#assigned').modal();
            }
        }
    });

};

function getAssigned(personIdS){
    $.ajax({
        type: "JSON",
        url: "/api/v0/catalog/personAssigned/" + personIdS,
        success: function (res) {
            var row = '';
            $.each(res, function (j, item) {
                row += "<tr><td>"+item['nameGrade'] + "</td><td>"+item['groupId'] + "</td><td>"+item['nameProgram'] + "</td><td><button class='btn btn-danger' value='"+item['personAssignedId'] + "'><i class='fa fa-times'></i></button></td></tr>";
            });
            $('#rowAssigned').html(row);
        }
    });
}