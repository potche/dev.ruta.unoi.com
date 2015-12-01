/**
 * Created by isra on 20/10/15.
 */

$( "#schoolId" ).change(function() {
    $("#schoolFrm").submit();
});

var personIdGlobal = '';
var usernameGlobal = '';

function statsUser(personid, username, avance, eval){
    $('#loadingStats').modal();

    $('#surveyUser').html( '' );
    //console.log(eval);
    personIdGlobal = personid;
    usernameGlobal = username;

    var siG = 0;
    var noG = 0;
    var noseG = 0;
    var list = "";
    var evalUser;

    $.post( "ajax/detalleStats", { personId: personid, eval: eval })
        .done(function( data ) {
            evalUser = jQuery.parseJSON(data);

            $.each(evalUser, function(i, item) {
                siG += parseInt(item.si);
                noG += parseInt(item.no);
                noseG += parseInt(item.nose);
                list += "<option value='"+item.si+":"+item.no+":"+item.nose+":"+item.title+"'>"+item.title+"</option>";
            });




            var progress =  '<div class="progress progress-striped active">'+
                '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'+avance+'"aria-valuemin="0" aria-valuemax="100" style="width: '+avance+'%;">'+
                    avance+
                    '%</div>'+
                    '</div>'
                ;

            if(eval == 'Todas las Evaluaciones') {
                createGraph(siG, noG, noseG, 'Global');
                var divList = "<div class='row'>" +
                    "<div class='col-sm-6'>" +
                    "<img src='public/assets/images/login/icon_niño1.png' alt='avatar' class='img-circle'>" +
                    "<h1>" + username + "</h1>" +
                    progress +
                    "<small><em>Porcentaje de avance</em></small><hr/>" +
                    "</div>" +
                    "<div class='col-sm-4 col-sm-offset-2'></div>" +
                    "</div>" +
                    "<div class='well'>" +
                    "<h5>Selecciona alguna evaluación para ver su <b>detalle</b>:</h5>" +
                    "<select id='evaluacioLU' class='form-control' size='1' onchange='(abc(this.value))'>" +
                    "<option value='" + siG + ":" + noG + ":" + noseG + ":0'>Global</option>" + list + "" +
                    "</select>" +
                    "</div>";
            }else{
                createGraph(siG, noG, noseG, eval);
                var divList = "<div class='row'>" +
                    "<div class='col-sm-6'>" +
                    "<img src='public/assets/images/login/icon_niño1.png' alt='avatar' class='img-circle'>" +
                    "<h1>" + username + "</h1>" +
                    progress +
                    "<small><em>Porcentaje de avance</em></small><hr/>" +
                    "</div>" +
                    "<div class='col-sm-4 col-sm-offset-2'></div>" +
                    "</div>" +
                    "<div class='well'>" +
                    "<h5>"+eval+"</h5>" +
                    "</div>";

                $.post( "ajax/stats", { title: eval, personId: personid })
                    .done(function( data ) {
                        $('#surveyUser').html( data );
                        TablesDatatables2.init();
                    });
            }

            $('#loadingStats').modal('hide');

            $('#statsUser').modal();
            $('#bodyStatsUser').html(divList);

            reflowChart();

        });

}

function reflowChart(){
    chart.reflow();
    chartColoumn.reflow();
}

function abc(rs){
    var arr = rs.split(':');
    createGraph(arr[0],arr[1],arr[2], arr[3]);
    if(arr[3] != 0){
        $.post( "ajax/stats", { title: arr[3], personId: personIdGlobal })
            .done(function( data ) {
                $('#surveyUser').html( data );
                TablesDatatables2.init();
            });
    }else{
        $('#surveyUser').html( '' );
    }

}

function createGraph(si, no, nose, title){
    var jsonTotalResponseLUPie= jQuery.parseJSON( '[{"name": "Sí", "y": '+si+', "sliced": true, "selected": true},{"name": "No", "y": '+no+', "sliced": false, "selected": false},{"name": "No sé", "y": '+nose+', "sliced": false, "selected": false}]' );
    var jsonTotalResponseLUColumn = jQuery.parseJSON( '[{"name": "Sí", "y": '+si+'},{"name": "No", "y": '+no+'},{"name": "No sé", "y": '+nose+'}]' );
    pieGrlLU(jsonTotalResponseLUPie, title);
    columnGrlLU(jsonTotalResponseLUColumn, title);
}


var TablesDatatables = function(col) {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#userList-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 0, col ] } ],
                pageLength: 5,
                lengthMenu: [[5,10,15,20,25,30, -1], [5,10,15,20,25,30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');

        }
    };
}();


var TablesDatatables2 = function() {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#example-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 0, 3 ] } ],
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');
        }
    };
}();
