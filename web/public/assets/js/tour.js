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
                        element: "#page-content",
                        container: "#page-content",
                        title: "Contenido de la plataforma",
                        content: "Este es el contenido de la plataforma",
                        placement: "left"
                    },
                    {
                        animation: "true",
                        element: "#panel-usuario",
                        container: "#panel-usuario",
                        title: "Menu de usuario",
                        content: "Este es tu menu de usuario",
                        placement: "left"
                    }
                ],
                template: "<div class='popover tour'>"+
                "<div class='arrow'></div>"+
                "<h3 class='popover-title'></h3>"+
                "<div class='popover-content'></div>"+
                "<div class='popover-navigation'>"+
                "<nav>"+
                "<button class='btn btn-info' data-role='prev'><i class='fa fa-chevron-left'></i> </button>"+
                "<span data-role='separator'> </span>"+
                "<button class='btn btn-info' data-role='next'><i class='fa fa-chevron-right'></i></button>"+
                "<button class='btn btn-info' data-role='end'>Terminar</button>"+
                "</nav>"+
                "</div>"
            });

            tour.init();
            tour.start();
        }
    };
}();