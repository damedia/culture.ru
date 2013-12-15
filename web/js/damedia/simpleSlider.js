var DamediaSimpleSlider = (function(){
    var slider = {};

    slider.init = function(wrapper, options){
        var items,
            itemsCount,
            visibleItemNum = 0,
            nextItemButton,

            defaults = { next: 'next', toggle: 400 },
            settings = options || defaults,
            buttonTitle = settings.next || defaults.next,
            toggleDuration = settings.toggle || defaults.toggle;

        if (wrapper.length > 0) {
            items = $('.sidebar-item-wrapper', wrapper);
            itemsCount = items.length;

            if (itemsCount > 1) {
                nextItemButton = $('<div class="goto-link-button hoverSensitive fullWidth" data-role="button">' + buttonTitle + '</div>');
                nextItemButton.on('click', function(){
                    $(items[visibleItemNum]).toggle(toggleDuration);
                    if (visibleItemNum !== itemsCount - 1) {
                        visibleItemNum++;
                    }
                    else {
                        visibleItemNum = 0;
                    }
                    $(items[visibleItemNum]).toggle(toggleDuration);
                });
                wrapper.append(nextItemButton);
            }

            $.each(items, function(i, item){
                if (i !== visibleItemNum) {
                    $(item).hide();
                }
            });
        }
    };

    return slider;
}());