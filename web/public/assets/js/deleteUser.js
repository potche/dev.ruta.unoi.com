/**
 * Created by indigo on 29/08/16.
 */


function deleteUser(id, user, name, executioner) {

    $(".titleModal").html("Eliminar Usuario");
    $(".bodyModal").html(
        "<div class='modal-body'>"+
        "<div class='row'>"+
        "<div class='col-xs-3 text-center'>" +
        "<img src='/public/assets/images/login/icon_niño1.png' class='img-circle'>"+
        "</div>"+
        "<div class='col-xs-9 text-justify'>" +
        "Realmente desea Eliminar al Usuario <b>"+name+"</b> del colegio <b>"+nameSchool+"</b> y todos los datos relacionados" +
        "</div>"+
        "</div>"+
        "</div>"+
        "<div class='modal-footer'>"+
        "<button type='button' class='btn btn-info' data-dismiss='modal'>Cancelar</button>"+
        "<button onclick='deleteUserOk("+id+", \""+user+"\", \""+name+"\", "+schoolId+", \""+nameSchool+"\", "+executioner+")' type='button' class='btn btn-success' >Aceptar</button>"+
        "</div>"
    );

    $("#confirmModal").modal();
}

function deleteUserOk(personId, user, name, schoolId, school, executioner) {

    $.post(  HttpHost+baseUrl+"/api/v0/admin/deletePerson",{personId: personId, user: user, name: name, schoolId: schoolId, school: school, executioner: executioner}, function() {
        $("#deleteAllSchool").prop('disabled',true);
    }).done(function(data) {
        $(".titleModal").html("Eliminar Usuario");
        $(".bodyModal").html(
            "<div class='modal-body'>" +
            "<h1>El usuario se elimino Correctamente</h1>" +
            "</div>" +
            "<div class='modal-footer'>" +
            "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>" +
            "</div>"
        );

        $('#userList').html("");
        $('#optionSchool').val('').trigger('chosen:updated');
    })
        .fail(function(error) {
            console.log(error);
            $(".titleModal").html("Error - Eliminar Usuario");
            $(".bodyModal").html(
                "<div class='modal-body'>"+
                "<h1>El usuario <b>no</b> se elimino Correctamente, por favor intentelo mas tarde. </h1>"+
                "</div>"+
                "<div class='modal-footer'>"+
                "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>"+
                "</div>"
            );
        });

}

function deleteSchoolOk(schoolId, school, executioner) {

    $.post(  HttpHost+baseUrl+"/api/v0/admin/deleteSchool",{schoolId: schoolId, school: school, executioner: executioner}, function() {
        $("#deleteAllSchool").prop('disabled',true);
    }).done(function(data) {
        $(".titleModal").html("Eliminar Colegio");
        $(".bodyModal").html(
            "<div class='modal-body'>" +
            "<h1>El Colegio se elimino Correctamente</h1>" +
            "</div>" +
            "<div class='modal-footer'>" +
            "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>" +
            "</div>"
        );

        $('#userList').html("");
        $('#optionSchool').val('').trigger('chosen:updated');
    })
        .fail(function(error) {
            console.log(error);
            $(".titleModal").html("Error - Eliminar Colegio");
            $(".bodyModal").html(
                "<div class='modal-body'>"+
                "<h1>El Colegio <b>no</b> se elimino Correctamente, por favor intentelo mas tarde. </h1>"+
                "</div>"+
                "<div class='modal-footer'>"+
                "<button type='button' class='btn btn-info' data-dismiss='modal'>Cerrar</button>"+
                "</div>"
            );
        });

}


function setNameSchool(nameSchool){
    $('.nameSchool').html( nameSchool );
}


var TablesDatatables = function() {
    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
            $('#userList-datatable').dataTable({
                columnDefs: [ { orderable: false, targets: [ 2, 2 ] } ],
                pageLength: 5,
                lengthMenu: [[5,10,15,20,25,30, -1], [5,10,15,20,25,30, 'All']],
                paging: true,
                searching: true
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'buscar');
        }
    };
}();