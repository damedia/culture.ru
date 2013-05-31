var autoScrolling = {
    autoScrollInt: null,
    autoScrollIntHor: null,
    state: false,
    init: function(){
        this.initVertical();
        this.initHorizontal();
        this.initHorizontalParent();
    },
    initVertical: function(){
        $(document).on(
            {
                mousemove: function(e) 
                {
                    var autoScroll = $(this),
                    autoScrollHeight = autoScroll.height(),
                    autoScrollWrapHeight = autoScroll.parent().height(),
                    autoScrollTop = autoScroll.position().top,
                    autoScrollTopMax = autoScrollWrapHeight - autoScrollHeight;
                    
                    var parentOffset = autoScroll.offset(); 
                    //or $(this).offset(); if you really just want the current element's offset
                    
                    var relY = e.pageY - parentOffset.top;
                    var state = relY > autoScrollHeight/2;
                    
                    console.log();
                    if (autoScrolling.state != state) {
                        autoScrolling.state = state;
                        if (relY > autoScrollHeight/2) {
                            autoScrolling.autoScrollInt = setInterval(function(){
                                if (autoScrollTop > autoScrollTopMax) {
                                    autoScrollTop -= 2;
                                    autoScroll.css({top:autoScrollTop});
                                } else {
                                    clearInterval(autoScrolling.autoScrollInt);
                                }
                            }, 100);
                        } else {
                            autoScroll.css('top', 0);
                            clearInterval(autoScrolling.autoScrollInt);
                        }
                    }
                    
                    
                    
                    /*
                         */
                },
                mouseleave: function()
                {
                    $(this).unbind('mouseover').css('top',0);
                    clearInterval(autoScrolling.autoScrollInt);
                    autoScrolling.state = false;
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
    },
    initHorizontalParent:function(){
        $(document).on(
            {
                mouseenter: function() 
                {
                    var autoScroll = $(this).find('.autoscrollingHorChild'),
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
                    $(this).find('.autoscrollingHorChild').unbind('mouseover').css({'left':0});
                    clearInterval(autoScrolling.autoScrollIntHor);
                }
            }
            , '.autoscrollingHorParent');
    }
}