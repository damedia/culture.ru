$(function(){
   //$('#wrapper').hide();
    
    $(window).load(function(){
      //$('#wrapper').show();
    })

    
    if (navigator.userAgent.match(/Android/i)){
        $("html").addClass("android"); 
    } else if (navigator.userAgent.match(/webOS/i)) {
        $("html").addClass("webos");  
    } else if (navigator.userAgent.match(/iPhone/i)) {
        $("html").addClass("iphone"); 
    } else if (navigator.userAgent.match(/iPod/i)) {
        $("html").addClass("ipod"); 
    } else if (navigator.userAgent.match(/iPad/i)){
        $("html").addClass("ipad"); 
    } else if (navigator.userAgent.match(/BlackBerry/i)){
        $("html").addClass("blackberry"); 
    }

    if (navigator.userAgent.match(/(iPhone|iPad|iPod)/i)){
        $("html").addClass("ios");  /*jQuery*/
    }


    var ua = navigator.userAgent, 
        click_event = (ua.match(/iPad/i)) ? "touchstart" : "click";


    
    //Угадай сколько
    $('.show-right').click(function(){
       $(this).toggleClass("active");
    })

    $("#item-count").submit(function () {
         $(".show-right a").click();
         $('#item-count-input').blur();
         return false;
     });
     
  
    
    // Quiz game. "Угадай кто?", "Выбери один из 3-x."
    var chooseGame = {
        
        /** Выделить элемент
         *  @param {object} image
         */
        hilightImage: function(image){
            var imageSrc = image.attr('src'),
                imageSrcLength = imageSrc.length;
                
            image.attr('src', imageSrc.substr(0, imageSrcLength-4)+'-active.png')
                 .parent().addClass('selected');
        },
        
        /** Убрать выделение с элемента
         *  @param {object} image
         */
        deHilightImage: function(image){
            var imageSrc = image.attr('src'),
                imageSrcLength = imageSrc.length;

           image.attr('src', imageSrc.substr(0, imageSrcLength-11)+'.png')
                .parent().removeClass('selected');
        },
        
        /** Запуск
         *  
         */
        start: function(){
            var self = this;
            
            $('.choice img').click(function(){
                var image = $(this),
                    otherImages = image.parent().siblings('.choice'),
                    choiseRes = image.closest('article').find('.choice-result');
                
                if (image.data('clicked')) {
                    image.data('clicked', false);
                    
                    self.deHilightImage(image);
                   
                    if (choiseRes.find('.choice-no-result').hasClass('invisible'))  {
                        choiseRes.find('.choice-no-result')
                                 .removeClass('invisible')
                                 .addClass('show')
                                 .siblings().addClass('invisible');
                    }
                    
                } else {
                    image.data('clicked', true);
                    self.hilightImage(image);
                   
                    otherImages.each(function(){
                        var otherImage = $(this).find('img');
                        
                        if (otherImage.data('clicked')) {
                            otherImage.data('clicked', false);
                            self.deHilightImage(otherImage);
                        }
                    })
                    
                    if (choiseRes.find('.'+$(this).attr('id')+'-result').hasClass('invisible'))  {
                        choiseRes.find('.'+$(this).attr('id')+'-result')
                                 .removeClass('invisible')
                                 .addClass('show')
                                 .siblings()
                                 .addClass('invisible');
                    }
                }

                return false;
                
            })
            
        },
    }
    chooseGame.start();
    
    
    
    //Сравнение. 10 отличий
    var compareGame = {
        container: $('.hidden-parts'),
        compareTotal: $('.compare-images .inv').length,
        foundTotal: 0,
        
        /** Склонение слова в зависимости от количества элементов
         *  @param {int} count
         *  @param {array} valuesArr
         */
        countLabel: function(count, valuesArr) {
            if ($.inArray(count, [2,3,4]) != -1) {
                return valuesArr[0];
            } else if ($.inArray(count, [1]) != -1){
                return valuesArr[1];
            } else {
                return valuesArr[2];
            }
        },
        
        /** Запуск
         *  
         */
        start: function() {
            var self = this;
                
            self.container.on(click_event, '.inv', function(){
                
                self.foundTotal++;
                
                $(this).removeClass('inv');
                if ($('.compare-images .inv').length == 0) {
                    $('#compare-found').parent().addClass('all-found');
                }
                $('#compare-found').html(self.foundTotal);
                $('.compare-otl').html(self.countLabel(self.foundTotal, ['отличия', 'отличие', 'отличий']));
                
                return false;
            })
        },
        
    };
    compareGame.start();
    
    
    //jScrollPane
    $('.scroll-pane').jScrollPane();

   
   // Fancyboxes
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
    
    
    // Print
    $('.to-print').click(function(){
        var printContent = $('.winner-name').text();
        
        Cufon.replace('.winner-name');
        window.print();
        
        return false;
    })
   
    $('.fancy-hide').css('visibility','visible').hide();
})
