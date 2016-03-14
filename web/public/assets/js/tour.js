var tour;
var TourInicio = function(){

    return {
        init: function(configURL,person,steps,tourEnabled){

             tour = new Tour({

                 path: "/inicio",
                 container: "#page-wrapper",
                 keyboard: true,
                 autoscroll: true,
                 steps: steps,
                 onEnd: function(tour){

                     TourStatus.init(configURL,person,'0');

                     $.bootstrapGrowl("<h4>Ha terminado el tour</h4><p>Recuerda que puedes volver a tomar el tour activándolo en tu panel de usuario</p>", {
                         type: 'info',
                         delay: 5000,
                         allow_dismiss: true
                     });

                     $("#aboutM").modal('show');
                 },
                 template:
                    "<div class='popover tour' style='z-index: 1000000;'>"+
                    "<div class='arrow'></div>"+
                    "<h3 class='popover-title'></h3>"+
                    "<div class='popover-content'></div>"+
                    "<div class='popover-navigation'>"+
                    "<nav>"+
                    "<button id='step-prev' class='btn btn-sm btn-info' data-role='prev'><i class='fa fa-chevron-left'></i> </button>"+
                    "<span data-role='separator'> </span>"+
                    "<button id='step-next' class='btn btn-sm btn-info' data-role='next'><i class='fa fa-chevron-right'></i></button>"+
                    "<button id='step-end' class='btn btn-sm btn-info' data-role='end'>Terminar</button>"+
                    "</nav>"+
                    "</div>"
             });

            tour.init();

            if(tourEnabled == '1'){

                tour.restart();
            }

            $("#tour-trigger").click(function() {

                tour.restart();
            });
        }
    };
}();