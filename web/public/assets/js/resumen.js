var TablesDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-datatable').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todo']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-datatable_info').hide();
        }
    };
}();