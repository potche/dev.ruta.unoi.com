var tour;
var TourInicio = function(){

    return {
        init: function(){

         tour = new Tour({
             container: "#page-wrapper",
             keyboard: true,
             storage: false,
             debug: true,
             orphan: true,
                steps: [
                    {
                        animation: "true",
                        element: "#menuInicio",
                        title: "<i class='fa fa-bars'></i> Menú de opciones",
                        content: "Estas son todas las secciones que puedes visitar en tu Plataforma de Diagnóstico",
                        placement: "left"
                    },
                    {
                        animation: "true",
                        element: "#sidebar",
                        title: "<i class='fa fa-bars'></i> Lista de opciones",
                        content: "De igual manera, también puedes ver tus opciones aquí",
                        placement: "right"
                    },
/*                    {
                        animation: "true",
                        element: "#panel-usuario",
                        container: "#panel-usuario",
                        title: "Menu de usuario",
                        content: "Este es tu menu de usuario. Aquí podrás ver los perfiles del LMS con los que cuentas, así como ver información de la plataforma y activar/inactivar notificaciones via mail.",
                        placement: "left"
                    },*/
                    {
                        animation: "true",
                        element: "#page-content",
                        title: "<i class='fa fa-pie-chart'></i> Panel de inicio",
                        content: "Este es tu panel de inicio. Aquí podrás ver las estadísticas más relevantes sobre tu proceso de diagnóstico.",
                        placement: "left"
                    },
                    {
                        animation: "true",
                        element: "#ayuda-btn",
                        title: "<i class='fa fa-question-circle'></i> Centro de ayuda",
                        content: "Ahora puedes apoyarte de videotutoriales diseñados para ayudarte a utilizar esta plataforma.",
                        placement: "left"
                    }
                ],
                template: "<div class='popover tour'>"+
                "<div class='arrow'></div>"+
                "<h3 class='popover-title'></h3>"+
                "<div class='popover-content'></div>"+
                "<div class='popover-navigation'>"+
                "<nav>"+
                "<button class='btn btn-sm btn-info' data-role='prev'><i class='fa fa-chevron-left'></i> </button>"+
                "<span data-role='separator'> </span>"+
                "<button class='btn btn-sm btn-info' data-role='next'><i class='fa fa-chevron-right'></i></button>"+
                "<button class='btn btn-sm btn-info' data-role='end'>Terminar</button>"+
                "</nav>"+
                "</div>"
            });

            tour.init();
            tour.start();
        }
    };
}();