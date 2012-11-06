var armdMainPage = {
    init:function() {
        $('#rusObrTab_tab .reload_btn').bind('click', function(event){
            event.preventDefault();
            armdMainPage.loadRandomRussiaImages();
        })
    },

    loadRandomRussiaImages:function() {
        $.ajax({
            url: Routing.generate('armd_main_random_russia_images'),
            cache: false,
            dataType:'html',
            type:'POST',
            data:{
                searchString:$('#search_text').val()
            },
            success:function (data) {
                $('#rusObrTab_tab .rusObr-block').html(data);
            }
        });
    }
};