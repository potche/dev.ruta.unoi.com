/**
 * Created by isra on 13/10/15.
 */


function parseInfo(privilege, profile){
    var privilegeArray = $.parseJSON(privilege);
    var link = '';
    var menu = '';
    $.each(privilegeArray, function(i, item) {
        link += "<li>"+
            "<a href=" + privilegeArray[i].ruteOptionApplication + ">"+
            "<i class='" + privilegeArray[i].iconOptionApplication + " sidebar-nav-icon'></i>"+
            "<span class='sidebar-nav-mini-hide'>" + privilegeArray[i].nameOptionApplication + "</span>"+
            "</a>"+
            "</li>";
    });

    link +="<li id='ayuda-link'>"+
        "<a href='/tutorials'>"+
        "<i class=' fa fa-question-circle sidebar-nav-icon'></i>"+
        "<span class='sidebar-nav-mini-hide'> Ayuda</span>"+
        "</a>"+
        "</li>";

    $('#privilege').html(link);

    //-----------------------------------------------

    $.each(privilegeArray, function(i, item) {
        if(privilegeArray[i].nameOptionApplication != 'Inicio'){
            menu += "<li>"+
                "<a href='" + privilegeArray[i].ruteOptionApplication + "'><i class='" + privilegeArray[i].iconOptionApplication + "'></i> " + privilegeArray[i].nameOptionApplication + "</a>"+
                "</li>"
            ;
        }
    });
    menu += "<li id='ayuda-btn'><a href='/tutorials'><i class='fa fa-question-circle'></i> Ayuda</a></li>";

    $('#menuInicio').html(menu);

    //-----------------------------------------------

    var profileArray = $.parseJSON(profile);
    var profileStr = '';
    //console.log(profileArray);
    var adminBase = false;
    $.each(profileArray, function(i, item) {
        if(profileArray[i].profileid === 1){
            adminBase = true;
        }
        profileStr +=   "<a href=''>"+
                            "<i class='fa fa-user fa-fw pull-right'></i>"+
                                profileArray[i].profile+
                        "</a>";
    });

    if(!adminBase){
        $('#uProfile').removeClass('hidden');
    }

    //console.log(profileStr);
    //var profileStrF = profileStr.replace(/ \| +$/g, '');
    $('#profile').html(profileStr);
}


function assigned(personIdS) {

    $.ajax({
        type: "JSON",
        url: "/api/v0/catalog/personAssigned/" + personIdS,
        success: function (res) {
            if (res.length === 0) {
                $.ajax({
                    type: "JSON",
                    url: "/api/v0/catalog/grades",
                    success: function (resp) {
                        if (resp.length !== 0) {
                            var grade = '<option value="0">Selecciona un Grado...</option>';
                            $.each(resp, function (j, item) {
                                grade += "<option value = '" + item['gradeId'] + "'>" +
                                    item['nameGrade'] +
                                    "</option>";
                            });
                            $('#val_gradp').html(grade);
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
                                option += "<option value = '" + item['programId'] + "'>" +
                                    item['nameProgram'] +
                                    "</option>";
                            });

                            $('#val_programa').html(option);
                        }
                    }
                });

                $('#assigned').modal();
            }
        }
    });

};

$('#add').click(function () {
    console.log('hola');
});