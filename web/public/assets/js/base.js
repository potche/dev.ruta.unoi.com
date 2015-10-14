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
    $('#privilege').html(link);

    //-----------------------------------------------

    $.each(privilegeArray, function(i, item) {
        menu += "<li>"+
                    "<a href='" + privilegeArray[i].ruteOptionApplication + "'><i class='" + privilegeArray[i].iconOptionApplication + "'></i> " + privilegeArray[i].nameOptionApplication + "</a>"+
                "</li>"
            ;
    });
    $('#menuInicio').html(menu);

    //-----------------------------------------------

    var profileArray = $.parseJSON(profile);
    var profileStr = '';
    //console.log(profileArray);
    $.each(profileArray, function(i, item) {
        profileStr +=   "<a href=''>"+
                            "<i class='fa fa-user fa-fw pull-right'></i>"+
                                profileArray[i].profile+
                        "</a>";
    });
    console.log(profileStr);
    //var profileStrF = profileStr.replace(/ \| +$/g, '');
    $('#profile').html(profileStr);
}