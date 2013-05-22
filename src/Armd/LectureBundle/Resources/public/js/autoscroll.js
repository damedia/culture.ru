var autoScrolling = {
    autoScrollInt: null,
    autoScrollIntHor: null,
    init: function(){
        this.initVertical();
        this.initHorizontal();
    },
    initVertical: function(){
        $(document).on(
            {
                mouseenter: function() 
                {
                    var autoScroll = $(this),
                    autoScrollHeight = autoScroll.height(),
                    autoScrollWrapHeight = autoScroll.parent().height(),
                    autoScrollTop = autoScroll.position().top,
                    autoScrollTopMax = autoScrollWrapHeight - autoScrollHeight;
                    
                    autoScrolling.autoScrollInt = setInterval(function(){
                        if (autoScrollTop > autoScrollTopMax) {
                            autoScrollTop -= 2;
                            autoScroll.css({top:autoScrollTop});
                        } else {
                            clearInterval(autoScrolling.autoScrollInt);
                        }
                    }, 100);     
                },
                mouseleave: function()
                {
                    $(this).unbind('mouseover').css('top',0);
                    clearInterval(autoScrolling.autoScrollInt);
                }
            }
            , '.autoscrolling');
    },
    initHorizontal:function(){
        $(document).on(
            {
                mouseenter: function() 
                {
                    var autoScroll = $(this),
                        autoScrollWidth = autoScroll.width(),
                        autoScrollWrapWidth = autoScroll.parent().width(),
                        autoScrollLeft = autoScroll.position().left,
                        autoScrollLeftMax = autoScrollWrapWidth - autoScrollWidth;
                        
                    //autoScroll.parent().css('text-overflow', 'auto');    
                        
                    autoScrolling.autoScrollIntHor = setInterval(function(){
                        if (autoScrollLeft > autoScrollLeftMax) {
                            autoScrollLeft -= 5;
                            autoScroll.css({left:autoScrollLeft});
                        } else {
                            clearInterval(autoScrolling.autoScrollIntHor);
                        }
                    }, 100);    
                },
                mouseleave: function()
                {
                    $(this).unbind('mouseover').css({'left':0});
                    clearInterval(autoScrolling.autoScrollIntHor);
                }
            }
            , '.autoscrollingHor');
    }
}