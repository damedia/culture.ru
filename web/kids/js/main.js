$(function(){
   //$('#wrapper').hide();
    
    $(window).load(function(){
      //$('#wrapper').show();
    
         
    })


    if(navigator.userAgent.match(/Android/i)){
                $("html").addClass("android"); 
      }
      else if(navigator.userAgent.match(/webOS/i)){
                      $("html").addClass("webos");  
      }
      else if(navigator.userAgent.match(/iPhone/i)){
                      $("html").addClass("iphone"); 
      }
      else if(navigator.userAgent.match(/iPod/i)){
                      $("html").addClass("ipod"); 
      }
      else if(navigator.userAgent.match(/iPad/i)){
                      $("html").addClass("ipad"); 
      }
      else if(navigator.userAgent.match(/BlackBerry/i)){
                      $("html").addClass("blackberry"); 
      }

      if(navigator.userAgent.match(/(iPhone|iPad|iPod)/i)){
                                $("html").addClass("ios");  /*jQuery*/
      }


    var ua = navigator.userAgent, 
        click_event = (ua.match(/iPad/i)) ? "touchstart" : "click";


    

    $('.show-right').click(function(){
       $(this).toggleClass("active");
      /* $('#item-count-input').val($('#item-count-input').attr('rel')) ;*/
      

    })



//$("#theElement").bind(event, function() {
     // jquery code
//}

            $("#item-count").submit(function () {
                 $(".show-right a").click();
                 $('#item-count-input').blur();
                 return false;
             });
             


         
/*
            $('#item-count-input').on('input',function(e){
               
            });

*/
		$('.choice img').click(function(evt){
                
                var image = $(this),
                    otherImages =image.parent().siblings('.choice'),
                    choiseRes = image.closest('article').find('.choice-result');
                
                if(image.data('clicked')) {
                    image.data('clicked', false);
                    deHilightImage(image);
                    //choiseRes.find('div').hide();
                    if (choiseRes.find('.choice-no-result').hasClass('invisible'))  {
                      
                        choiseRes.find('.choice-no-result').removeClass('invisible').addClass('show').siblings().addClass('invisible');
                       
                    }
                       
                  //  choiseRes
                      //      .find('.choice-no-result').hasClass('invisible').addClass('invisible');//.siblings().addClass('show');
                        //.find('.choice-no-result').css('visibility', 'visible')
                        //.siblings().css('visibility', 'hidden');
                
                } else {
                    image.data('clicked', true);
                    hilightImage(image);
                   
                    otherImages.each(function(){
                        
                        var otherImage = $(this).find('img');
                        
                        if(otherImage.data('clicked')) {
                            otherImage.data('clicked', false);
                            deHilightImage(otherImage);
                        }
                    })
                    
                    //choiseRes.find('div').hide();

                    if (choiseRes.find('.'+$(this).attr('id')+'-result').hasClass('invisible'))  {
                      
                        choiseRes.find('.'+$(this).attr('id')+'-result').removeClass('invisible').addClass('show').siblings().addClass('invisible');
                        console.log ($(this))
                    }
                   // choiseRes
                           // .find('.'+$(this).attr('id')+'-result').hasClass('invisible').removeClass('invisible').siblings().addClass('show');
                        
                        //.find('.'+$(this).attr('id')+'-result').css('visibility', 'visible');
                        //.siblings().css('visibility', 'hidden');
                        
                    
                }
                
                //evt.stopPropagation();
                return false;
                
            })
            
            
            function hilightImage(image) {
                var imageSrc = image.attr('src'),
                    imageSrcLength = imageSrc.length;
                    //console.log();
               // image.siblings('.visuallyhidden').removeClass('visuallyhidden')
               // image.addClass('visuallyhidden');

                image.attr('src', imageSrc.substr(0, imageSrcLength-4)+'-active.png');
            }
            function deHilightImage(image) {
                var imageSrc = image.attr('src'),
                    imageSrcLength = imageSrc.length;
                    //console.log();
                //image.siblings('.visuallyhidden').removeClass('visuallyhidden')
               // image.addClass('visuallyhidden');
               image.attr('src', imageSrc.substr(0, imageSrcLength-11)+'.png');
            }
            
            
            //$('.hidden-parts div').css({'visibility':'hidden'}).show();
            var compareTotal = $('.compare-images .inv').length;
          
            $('.hidden-parts').on(click_event, '.inv', function(){
                
                var foundTotal;
                $(this).removeClass('inv');
                
                foundTotal = compareTotal - $('.compare-images .inv').length;
                $('#compare-found').html(foundTotal);
                
                if($('.compare-images .inv').length == 0) {
                    $('#compare-found').parent().addClass('all-found');
                }
                
                if($.inArray(foundTotal, [2,3,4]) != -1) {
                    $('.compare-otl').html('отличия');
                } else if ($.inArray(foundTotal, [1]) != -1){
                    $('.compare-otl').html('отличие');
                } else {
                     $('.compare-otl').html('отличий');
                }
               
                return false;
                
            })
            
           // if($('.scroll-pane').length > 0) {
                $('.scroll-pane').jScrollPane();
           // }

            $('a.fancybox').fancybox();


                $('a.fancybox-inframe').fancybox({
                    type: 'iframe',
                    autoSize : false,
                    fitToView : false,
                    scrolling : 'no',
                    beforeLoad : function() {         
                    this.width  = parseInt(this.element.data('fancybox-width'));  
                    this.height = parseInt(this.element.data('fancybox-height'));
                    }
                });
            
            
           /* window.print();*/
            
            $('.to-print').click(function(){
               var printContent = $('.winner-name').text();
               Cufon.replace('.winner-name');
             
    

                 window.print();
                
                return false;
            })
           
            
            
            /*$('.close').click(function(){
                $(this).parent('.window').hide();
                $('.overlay').hide();
                return false;
            })*/
            
           /* $('.learn-more-link').click(function(){
                $('.window').show();
                $('.overlay').show();
                return false;
            })
            
            */
            $('.fancy-hide').css('visibility','visible').hide();
		})
