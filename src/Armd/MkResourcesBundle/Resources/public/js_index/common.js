/* Inits */
$(document).ready(function() {

   $('#nav-top td').hover(function(){
		$(this).prev().addClass('prevhover');
	},function(){
		$(this).prev().removeClass('prevhover');
	});
    
    var links = $('#nav-top a');

    links.each(function(){
        if ($(this).attr('href') == document.location.pathname) {
            links.parent().removeClass('current').removeClass('prevhover')
            $(this).parent().addClass('current').prev().addClass('prevhover')
        }
    
    })
    
	$('.main-menu-item').hover(
		function() {
		$(this).css('z-index','1').find('.main-menu-item-border').show();
		$(this).find('.main-menu-item-info').show();
	
	},
	function() {
		$(this).css('z-index','0').find('.main-menu-item-border').hide();
		$(this).find('.main-menu-item-info').hide();
	});

	var divLinks = $('.main-menu-item-info');
	divLinks.eq(4).addClass('main-menu-item-info-left')
	divLinks.eq(9).addClass('main-menu-item-info-left')
	
	
	window.i2=0;	
	window.countCycle = 0;
	window.interval = setInterval(slide, 10000);
    $('.tab-wrapper').hover( 
		function(){
		clearInterval(window.interval);
		},
		function() {
		window.interval = setInterval(slide, 10000);
		});

	var slideContainers = $('.tab-wrapper > div');
	slideContainers.hide().filter(':first').show();
    
        $('.pagination li').click(function () {
            var indexElem = $(this).index();
            slideContainers.hide();
			i2=indexElem;
            slideContainers.eq(indexElem).fadeIn(1000)
            $('.pagination li').removeClass('current');
            $(this).addClass('current');
            return false;
        });
	
	$('.trans-wrapper-header').click(function() {
        $('.trans-wrapper-body').toggle()
    });

    $('.trans-wrapper').hover(function() {
       
       }, 
    function(){
      $('.trans-wrapper-body').fadeOut(2000);  
    });
    
    if ($('.trans-wrapper').hasClass('trans-wrapper-event')) {
        $('.trans-wrapper-body-text').hide()
        $('.trans-item').show()
    }
    else {
        $('.trans-wrapper-body-text').show()
        $('.trans-item').hide()
    }
	
   $('.nav-header-enter').click(function() {
	$('.header-top-login').toggle();
	return false;
	});
   
   
});

function slide() {
    i2++;
    if($('.tab-wrapper > div:last').is(':visible')){
				$('.pagination li').eq(0).click();
				i2=0; 
			} else {
				var test = $('.pagination li').index()
				$('.pagination li').eq(i2).click()
			}

		return;

}

function iGadget(){
		return (
        (navigator.platform.indexOf('iPad') != -1) ||
        (navigator.platform.indexOf('iPhone') != -1)
           );
};

$(function(){
    if(iGadget()){
       $('.gadget').show(); 
    };
    if(isAndroid) {
        $('.gadget').show();
    };
    
    $('.gadget-clouse').click(function(){
        $(this).parent().hide();
    });
    
});


var userag = navigator.userAgent.toLowerCase();
var isAndroid = userag.indexOf("android") > -1;

 
/* Functions */

