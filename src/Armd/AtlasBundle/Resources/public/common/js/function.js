$(document).ready(function(){

    var 
        $discusitemTab = $('#discusitem-tab'),
        $discusitemTabs = $discusitemTab.find('li'),
        $discusitemContent = $('.discusitem-content');

        $discusitemTabs.click(function () {
            var indexElem = $(this).index();
            $discusitemContent.hide();
            $discusitemContent.eq(indexElem).fadeIn();
            $discusitemTabs.removeClass('active');
            $(this).addClass('active');
            return false;
        }).eq(0)



    var 
        $EventsitemTab = $('#eventsitem-tab'),
        $EventsitemTabs = $EventsitemTab.find('li'),
        $EventsitemContent = $('.eventsitem-content');

        $EventsitemTabs.click(function () {
            var indexElem = $(this).index();
            $EventsitemContent.hide();
            $EventsitemContent.eq(indexElem).fadeIn();
            $EventsitemTabs.removeClass('active');
            $(this).addClass('active');
            return false;
        }).eq(0)


        $('.filter-checkboxes label').click(function(e){
            if (e.target.nodeName == 'LABEL') {
                $(this).toggleClass('checked');
            }
        })
        
        /*$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
        $( "#calendar" ).datepicker({showOtherMonths: true});*/
        /*
        setTimeout(function(){
            $('.obj-o-foto-one img').each(function(){
                var imgThis = $(this),
                    imgWidth = $(this).width(),
                    imgHeight = $(this).height(),
                    imgParent = $(this).parents('li');
                
                imgParent.css({'width':imgWidth, 'height':imgHeight});  
                imgThis.css({'width':imgWidth, 'height':imgHeight});    
                
                var img = new Image();
                img.src = $(this).parent('a').attr('href');
                img.onload = function() {
                    imgThis.attr('data-width', this.width);
                    imgThis.attr('data-height', this.height);
                }
                imgThis.attr('data-orig-width', imgWidth);
                imgThis.attr('data-orig-height', imgHeight);
                imgThis.attr('data-orig-src', imgThis.attr('src'));
            })
        },100);
        
        $('.obj-o-foto-one img:not(.zoomed)').live('click', function(){
            var imgThis = $(this),
                imgWidth = $(this).width(),
                imgHeight = $(this).height(),
                zoomWidth = $(this).attr('data-width'),
                zoomHeight = $(this).attr('data-height'),
                zoomTop = (zoomHeight-imgHeight)/2,
                zoomLeft = (zoomWidth-imgWidth)/2;
                
            imgThis.attr('src', $(this).parent('a').attr('href'));  
            imgThis.addClass('zoomed').css({'zIndex':2}).animate({'width':zoomWidth,'height':zoomHeight, 'top':-zoomTop,'left':-zoomLeft},100);
            return false;
        });
        
        $('.obj-o-foto-one img.zoomed').live('click', function(){ 
            var imgThis = $(this),
                imageWidth = imgThis.attr('data-orig-width'),
                imageHeight = imgThis.attr('data-orig-height');
            
            imgThis.removeClass('zoomed').css({'zIndex':1}).animate({'width':imageWidth,'height':imageHeight, 'top':0,'left':0},100).attr('src', imgThis.attr('data-orig-src'));
            return false;
        })
        
		*/
		
        $('.collapsable-list li').click(function(){
            $(this).toggleClass('opened');
        })
        
        
        if($('#thumbs').length > 0 ) {
            // Initially set opacity on thumbs and add
            // additional styling for hover effect on thumbs
            var onMouseOutOpacity = 0.67;
            /*$('#thumbs ul.thumbs li').opacityrollover({
                mouseOutOpacity:   onMouseOutOpacity,
                mouseOverOpacity:  1.0,
                fadeSpeed:         'fast',
                exemptionSelector: '.selected'
            });*/
            
            // Initialize Advanced Galleriffic Gallery
            var gallery = $('#thumbs').galleriffic({
                delay:                     2500,
                numThumbs:                 5,
                preloadAhead:              5,
                enableTopPager:            false,
                enableBottomPager:         false,
                imageContainerSel:         '#slideshow',
                controlsContainerSel:      '#controls',
                captionContainerSel:       '#caption',
                loadingContainerSel:       '#loading',
				navControlsContainerSel:   '#nav-slider',
                renderSSControls:          true,
                renderNavControls:         true,
                playLinkText:              'Play Slideshow',
                pauseLinkText:             'Pause Slideshow',
                prevLinkText:              '&lsaquo; Назад',
                nextLinkText:              'Вперед &rsaquo;',
                nextPageLinkText:          'Вперед &rsaquo;',
                prevPageLinkText:          '&lsaquo; Назад',
                enableHistory:             true,
                autoStart:                 false,
                syncTransitions:           true,
                defaultTransitionDuration: 900,
                onSlideChange:             function(prevIndex, nextIndex) {
                    // 'this' refers to the gallery, which is an extension of $('#thumbs')
                    this.find('ul.thumbs').children()
                        .eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
                        .eq(nextIndex).fadeTo('fast', 1.0);

                    // Update the photo index display
                    this.$captionContainer.find('div.photo-index')
                        .html('Photo '+ (nextIndex+1) +' of '+ this.data.length);
                },
                onPageTransitionOut:       function(callback) {
                    this.fadeTo('fast', 0.0, callback);
                },
                onPageTransitionIn:        function() {
                    var prevPageLink = this.find('a.prev span').css('opacity', '0.3');
                    var nextPageLink = this.find('a.next span').css('opacity', '0.3');
                    
                    // Show appropriate next / prev page links
                    if (this.displayedPage > 0)
                        prevPageLink.css('opacity', '1');

                    var lastPage = this.getNumPages() - 1;
                    if (this.displayedPage < lastPage)
                        nextPageLink.css('opacity', '1');

                    this.fadeTo('fast', 1.0);
                },
				enableFancybox:          true

            });

            /**************** Event handlers for custom next / prev page links **********************/

            gallery.find('a.prev').click(function(e) {
                gallery.previousPage();
                e.preventDefault();
            });

            gallery.find('a.next').click(function(e) {
                gallery.nextPage();
                e.preventDefault();
            });

            /****************************************************************************************/
            /**** Functions to support integration of galleriffic with the jquery.history plugin ***

            // PageLoad function
            // This function is called when:
            // 1. after calling $.historyInit();
            // 2. after calling $.historyLoad();
            // 3. after pushing "Go Back" button of a browser
            function pageload(hash) {
                // alert("pageload: " + hash);
                // hash doesn't contain the first # character.
                if(hash) {
                    $.galleriffic.gotoImage(hash);
                } else {
                    gallery.gotoIndex(0);
                }
            }

            // Initialize history plugin.
            // The callback is called at once by present location.hash.
            $.historyInit(pageload, "advanced.html");

            // set onlick event for buttons using the jQuery 1.3 live method
            $("a[rel='history']").live('click', function(e) {
                if (e.button != 0) return true;

                var hash = this.href;
                hash = hash.replace(/^.*#/, '');

                // moves to a new page.
                // pageload is called at once.
                // hash don't contain "#", "?"
                $.historyLoad(hash);

                return false;
            });

            ***************************************************************************************/

        }
        
        /*filter*/
        $('#filter .lvl-1 li:first > label').addClass('fab-opened');
        $('#filter .lvl-1 li:first > ul').css({'display':'block'});
            
        $('#filter .lvl-1 > li').each(function(){
            if($(this).find('ul').length > 0) {
                $(this).find('label').append('<span />');
            }
        })  
        $('#filter .lvl-2 > li').each(function(){
            if($(this).find('ul').length > 0) {
                $(this).addClass('openable');
            }
        })
            
            
        $('#filter .lvl-1 > li').click(function(e){
            if ( $(this).find('ul').length > 0) {
                var thisHidden = $(this).find('ul:first');
                if (!thisHidden.is(":visible")) {
                    $(this).find('label').toggleClass('fab-opened');
                        thisHidden.slideDown();
                        $(this).siblings().find('ul:first').slideUp().end()
                                          .find('label').removeClass('fab-opened');
                        
                        
                }
            }
            
        })
        
        $('#filter .lvl-2 > li.openable').click(function(e){
            if( e.target.nodeName == 'LI' ) {
                $(this).toggleClass('fab-list-opened');
                $(this).find('ul').slideToggle();
            }
        })
        
        $("#filter .lvl-2 input:checkbox").click(function(){
            var thisList = $(this).closest('li').find('ul'),
                thisInputs  = $('li input:checkbox', thisList);
            
            console.log(thisList);
            if ($(this).attr('checked') == 'checked') {
                thisInputs.attr('checked', 'checked');
                if (!thisList.is(":visible")) {
                    $(this).closest('li').addClass('fab-list-opened');
                    thisList.slideDown();
                }
            }   
            else {
                thisInputs.attr('checked', false);
                if (thisList.is(":visible")) {
                    $(this).closest('li').removeClass('fab-list-opened');
                    thisList.slideUp();
                }
            }
        });
        $("#filter .lvl-1 input:checkbox").click(function(){
            var thisList = $(this).closest('li'),
                thisInputs  = $('li input:checkbox', thisList);
            if ($(this).attr('checked') == 'checked') {
                thisInputs.attr('checked', 'checked');
            }   
            else {
                thisInputs.attr('checked', false);
                
            }
        });
		
		
		/*fancybox img video etc */
		$("a.in-fancybox").fancybox();
		
	$(".iframe").fancybox({
		'width' : '100%',
		'height' : '100%',
		'autoScale' : true,
		'transitionIn' : 'none',
		'transitionOut' : 'none',
		'type' : 'iframe'
	});				

		
		
        
    });
    
    
    
$(window).load(function(){ 
				
					$(".sliderkit").sliderkit({
					mousewheel:false,
					shownavitems:2,
					panelbtnshover:false,
					auto:false,
					navscrollatend:false,
					counter:true,
					freeheight:true
				});
				
				
				
$('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 70,
        itemMargin: 0,
        asNavFor: '#slider',
		minItems: 5,   
		maxItems: 10,
      });
      
      $('#slider').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel",
		directionNav: false,
        start: function(slider){
          $('body').removeClass('loading');
		 $("#carousel").hide();
        }
      });
	  
	 
	 $("#slider").mouseenter(function(){
      	$('#carousel').show();
    }).mouseleave(function(){
     	 $('#carousel').hide();
    });
	
	 $("#carousel").mouseenter(function(){
      	$('#carousel').show();
    }).mouseleave(function(){
     	 $('#carousel').hide();
    });
	
	
	$('.btn-more a').click(function(){
		$("div#" + $(this).attr('rel')).slideToggle();
		$(this).toggleClass("opened");
		 return false;
 });
	
			

				
});
