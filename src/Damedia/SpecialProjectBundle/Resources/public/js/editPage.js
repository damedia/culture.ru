$(document).ready(function() {
    var button = $('<div class="editPage_confirmButton">Сохранить страницу</div>');

    button.on('click', function(){
        var dataToSend = {},
            blocksTextareas = $('textarea.editPage_blockContent');

        $.each(blocksTextareas, function(){
            var placeholder = $(this).attr('data-placeholder'),
                content = $(this).val();

            dataToSend[placeholder] = content;
        });

        $.post("{{ url('_admin_items_add') }}", dataToSend, function(data){
            alert(data);
        });
    }).appendTo('body');
});