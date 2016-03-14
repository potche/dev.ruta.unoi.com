var TourStatus= function(){

    return{

        init: function(urlPost, person, status){

            $.ajax({

                url: urlPost,
                type: 'POST',
                data: {
                    'personid': person,
                    'status': status
                }

            }).done(function (data){

                if(data.status != 200){

                    $.bootstrapGrowl("<h4>Error</h4><p>"+data.message+"</p>", {
                        type: 'error',
                        delay: 5000,
                        allow_dismiss: true
                    });
                }
            });
        }
    }
}();