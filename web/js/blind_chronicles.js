

	
$(function(){
	
    var specialChronicles = {
        chronNav : $('.chronicles-nav-wrap'),
        chronNavUlSub : $('ul.chronicles-nav ul'),
        chronNavHeight  : 0,
        chronNavWidth  : 0, 
        wrapperMar : $('.wrapper').css('marginBottom'),
        
        
        init: function(){
            
            var self = this;
            
            self.chronNavHeight = self.chronNav.height();
            self.chronNavWidth = self.chronNav.width();
            
            self.resizeChronicals(self.chronNav,self.chronNavUlSub);
            
            
            $('.size-picker span').click(function(){    
                setTimeout(function(){
                    self.resizeChronicals(self.chronNav,self.chronNavUlSub)
                    }, 10);
            })
            
            $('li li a', self.chronNav).click(function(){
                $(this).parent().addClass('active')
                       .siblings().removeClass('active');
            })
            
            $('> li > a', self.chronNavUlSub).click(function(){
                var id = $(this).attr('href'),
                    yearLink = $(id),
                    parentBlock;
                    
                parentBlock = $(id).closest('.cent-tab');
                $('.decade-sector').css({'paddingTop':0});
                console.log($('.chronicles-nav-wrap').height());
                yearLink.css({'paddingTop': $('.chronicles-nav-wrap').height(), 'display':'block'});
            })
            
            
            $('> ul > li > a',self.chronNav).click(function(){
                var chronLink = $(this).attr('href');
                $(this).parent().addClass('active')
                        .siblings().removeClass('active');
                        
                $(chronLink+'_cent').show().siblings('.cent-tab').hide();
                window.location = '#settings';
                self.resizeChronicals(self.chronNav,self.chronNavUlSub);
                return false;
            })
            
            self.winScroll();
            
            
        },
        
        resizeChronicals: function(chronNav,chronNavUlSub) {
            var heightAdd = 0;
            if (chronNav.length > 0) {
                heightAdd = $('.chronicles-nav li.active ul').height();
                
                chronNav.css({'height':'auto'});
                
                this.chronNavUlHeight = chronNav.find('ul.chronicles-nav').height();
                this.chronNavUlSubHeight = chronNavUlSub.height();
                this.chronNavWidth  = chronNav.width();
                
                chronNavUlSub.css({'top':this.chronNavUlHeight+10});
                chronNav.css({'height':this.chronNavUlSubHeight+20+this.chronNavUlHeight+heightAdd,'width':this.chronNavWidth});
			
            }
        },
        
        winScroll: function() {
            var lastScrollTop = 0,
                self  = this,
                chronPos = 0,
                windowTop = 0, 
                windowLeft = 0;
            
            $(window).scroll(function(){
                
                self.chronNav.css({'position':'relative','visibility':'hidden', 'width':'auto', 'left':0, 'top':0 });
                
                chronPos = self.chronNav.position();
                windowTop = $(this).scrollTop();
                windowLeft = $(this).scrollLeft();
                
                if(chronPos.top <= windowTop) {
                    self.chronNav.css({'left':chronPos.left-windowLeft, 'top':0, 'position':'fixed', 'width':self.chronNavWidth});
                    $('.chronicals').css({'position':'relative','top':self.chronNavHeight});
                    $('.wrapper').css({'marginBottom':0});
                } else {
                    self.chronNav.css({'position':'relative', 'width':'auto', 'left':0, 'top':0});
                    $('.chronicals').css({'top':0});
                    $('.wrapper').css({'marginBottom':self.wrapperMar});
                }
                
                if ((windowTop < lastScrollTop) && (Math.abs((lastScrollTop - windowTop)) < 110)) {
                   $('.decade-sector').css({'paddingTop':0});  // scroll up
                }
                lastScrollTop = windowTop;
                
                self.chronNav.css({'visibility':'visible'});
                
            });
        }
        
        
    }
    
    
	

	specialChronicles.init();
	
		

	
})
