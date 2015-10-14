var TablesDatatables = function() {

    return {
        init: function() {

            /**
             * Implementación de datatable para la tabla de respuestas
             */

            App.datatables();
            $('#tbl-respuestas').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'Ver Todas']]
            });

            $('.dataTables_filter input').attr('placeholder', 'Buscar');
            $('#tbl-respuestas_info').hide();
        }
    };
}();