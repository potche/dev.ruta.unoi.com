/**
 * Created by isra on 13/10/15.
 */


function parseInfo(privilege, profile){
    var privilegeArray = $.parseJSON(privilege);
    var link = '';
    var menu = '';
    $.each(privilegeArray, function(i, item) {
        if($.trim(privilegeArray[i].ruteOptionApplication) === "/observacion"){
            link += "<li>"+
                "<a href=" + privilegeArray[i].ruteOptionApplication + ">"+
                "<img src='" + privilegeArray[i].iconOptionApplication + "observacion.svg' align='middle' style='width: 18px; height: 14px; opacity: 0.5; margin-right: 10px;'>"+
                "<span class='sidebar-nav-mini-hide'>" + privilegeArray[i].nameOptionApplication + "</span>"+
                "</a>"+
                "</li>";
        }else {
            link += "<li>"+
                "<a href=" + privilegeArray[i].ruteOptionApplication + ">"+
                "<i class='" + privilegeArray[i].iconOptionApplication + " sidebar-nav-icon'></i>"+
                "<span class='sidebar-nav-mini-hide'>" + privilegeArray[i].nameOptionApplication + "</span>"+
                "</a>"+
                "</li>";
        }

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
        if(privilegeArray[i].nameOptionApplication !== 'Inicio'){
            if($.trim(privilegeArray[i].ruteOptionApplication) === "/observacion") {
                menu +=
                    "<li>" +
                        "<a href='" + privilegeArray[i].ruteOptionApplication + "'>" +
                            "<img class='visible-lg visible-md' src='" + privilegeArray[i].iconOptionApplication + "observacion.svg' align='middle' style='width: 100px; height: 55px; display: block; margin-right: 0; margin-bottom: 10px; font-size: 42px; padding: 10px; color: #dae7e8'> " +
                            "<img class='visible-xs-inline visible-sx-inline' src='" + privilegeArray[i].iconOptionApplication + "observacion.svg' align='middle' style='width: 15px;height: 17px; display: inline; margin-right: 0; margin-bottom: 0px; padding: 0px; color: #dae7e8;'> " +
                            privilegeArray[i].nameOptionApplication +
                        "</a>" +
                    "</li>"
                ;
            }else {
                menu += "<li>"+
                    "<a href='" + privilegeArray[i].ruteOptionApplication + "'><i class='" + privilegeArray[i].iconOptionApplication + "'></i> " + privilegeArray[i].nameOptionApplication + "</a>"+
                    "</li>"
                ;
            }
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
        if(profileArray[i].profileid === 1 || profileArray[i].profileid === 2){
            adminBase = true;
        }
        profileStr +=   "<span>" +
                            "<small>" +
                                "<span style='color: #394263; font-size: 14px;'>&#9679; </span> "+ profileArray[i].profile.toUpperCase() +
                            "</small>"+
                        "</span><br/>";
    });

    if(!adminBase){
        $('.uProfile').removeClass('hidden');
    }

    //console.log(profileStr);
    //var profileStrF = profileStr.replace(/ \| +$/g, '');
    $('#profile').html(profileStr);
}

function version(versionArray){

    var actualV = '';
    var anteriorV = '';
    var i = 0;
    var months = ['Enero','Febrero','Marzo','Abril','Maoy','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];



    $('#version').append(versionArray[0].version);

    $.each(versionArray, function(i, versions) {
        if(i != 0) {
            var d = versions.releaseDate.date.split(' ');

            var dateArray = d[0].split("-");
            var timeArray = d[1].split(":");
            
            var date = new Date(dateArray[0],dateArray[1]-1,dateArray[2],timeArray[0],timeArray[1],timeArray[2]);
            var day = date.getDate();
            var monthIndex = date.getMonth();
            var year = date.getFullYear();
            var hours = date.getHours();
            var minutes = date.getMinutes();

            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;

            var dateTime= day +'/'+ months[monthIndex]+'/'+ year +' @ '+ hours +':'+ minutes+ ' ' + ampm;


            anteriorV += '<div class="text-right"><h3>Version: '+versions.version+'</h3>' +
                '<h4>Fecha: '+ dateTime +'</h4></div>' +
                '<table>';
        }
        $.each(versions.nuevo, function (j, item) {
            if(i == 0) {
                actualV += "<tr>" +
                    "<th width='25%' style='vertical-align: top;'><i class='fa fa-check-circle-o'></i> " + item.title + ": </th>" +
                    "<td width='5%'> </td>"+
                    "<td style='padding-bottom: 2%'>" + item.description + "</td>" +
                    "</tr>"+
                    "<tr><td></td></tr>";
            }else{

                anteriorV += "<tr>" +
                    "<th width='25%' style='vertical-align: top;'><i class='fa fa-check-circle-o'></i> " + item.title + ": </th>" +
                    "<td width='5%'> </td>"+
                    "<td style='padding-bottom: 2%'>" + item.description + "</td>" +
                    "</tr>"+
                    "<tr><td></td></tr>";
            }
        });
        i++;
        if(i != 0) {
            anteriorV += '</table>';
        }
    });

    $('#nuevo').append(actualV);
    $('.last').html(anteriorV);
}

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

function paserDate(date){
    var d = date.split(' ');

    var dateArray = d[0].split("-");
    var timeArray = d[1].split(":");

    var monthNames = [
        "Ene", "Feb", "Mar","Abr", "May", "Jun", "Jul","Ago", "Sep", "Oct", "Nov", "Dic"
    ];

    var date = new Date(dateArray[0],dateArray[1]-1,dateArray[2],timeArray[0],timeArray[1],timeArray[2]);
    var day = date.getDate();
    var monthIndex = date.getMonth();
    var year = date.getFullYear();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    seconds = seconds < 10 ? '0'+seconds : seconds;

    return day +'/'+ monthNames[monthIndex]+'/'+ year +' @ '+ hours +':'+ minutes +':'+ seconds + ' ' + ampm;
}
