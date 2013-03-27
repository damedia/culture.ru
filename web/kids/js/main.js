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
        
        /** Показать результат
         *  @param {object} object
         */
        showResult: function(object){
           if (object.hasClass('invisible'))  {
                object.removeClass('invisible')
                      .addClass('show')
                      .siblings().addClass('invisible');
            }
        },
        
        /** Запуск
         *  
         */
        start: function(){
            var self = this;
            
            $('.choice img').click(function(){
                var image = $(this),
                    otherImages = image.parent().siblings('.choice'),
                    choiseRes = image.closest('article').find('.choice-result'),
                    choiceNoRes = choiseRes.find('.choice-no-result'),
                    choiceYesRes = choiseRes.find('.'+$(this).attr('id')+'-result');
                
                if (image.data('clicked')) {
                    image.data('clicked', false);
                    
                    self.deHilightImage(image);
                    self.showResult(choiceNoRes)
                    
                } else {
                    image.data('clicked', true);

                    self.hilightImage(image);
                    self.showResult(choiceYesRes);
                    
                    otherImages.each(function(){
                        var otherImage = $(this).find('img');
                        
                        if (otherImage.data('clicked')) {
                            otherImage.data('clicked', false);
                            self.deHilightImage(otherImage);
                        }
                    })
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
    
    
    // IE fix
    if ($.browser.msie  && parseInt($.browser.version, 10) <= 8) {
        $('.choose-block-horizontal .cbv-choices').append('<span class="ie-after"></span>');
    }
    
    
   // PUZZLE
    var puzzleGame = {
        windowWidth: $(window).width(), //retrieve current window width
        windowHeight: $(window).height(), //retrieve current window height
        docScrollTop : 0,
        docScrollLeft : 0,
        jsaw : {},
        
        init: function(container) {
            if (container.length > 0)
                this.start();
        },
        
         load: function(container) {
            $(window).load(function(){
                $('.puzzle-setka-wrap', container).each(function(){
                    var imageheight = $(this).find('img.puzzle').height();
                    $(this).height(imageheight).css('marginTop', -imageheight/2);
                })
            });
            this.jsaw = new jigsaw.Jigsaw({
                defaultImage: "img/arkaim/puzzle.jpg",
                piecesNumberTmpl: "%d элементов",
                redirect: null,
                shuffled: true,
                rotatePieces: false      
            });
            
            
            $('#chosen-option').click(function(){
                $(this).next().show();
                $(this).hide();
            })
            
            $('#select-options').click(function(){
                $(this).hide();
                $(this).prev().show();
                
                return false;
            })
            
        },
        start: function(container) {
            var self = this;
            self.load(container);
            
            $('img.puzzle-setka', container).click(function(){
                self.clicked($(this));
            });
            
            $(window).resize(function(){
                self.resize();
            })
            
            self.close();
            
        },
        clicked: function(object) {
            var self = this;
            self.docScrollTop = $(document).scrollTop();	
            self.docScrollLeft = $(document).scrollLeft();
            
            self.windowWidth = $(window).width();
            self.windowHeight = $(window).height();
            
            $('body, html').css('overflow', 'hidden');
            
            self.jsaw.set_image(object.prev().attr('src'));
            
            $('#puzzle-block').css({
                'width':self.windowWidth,
                'height':self.windowHeight
            }).show();
            
            document.location.href = '#';
        }, 
        resize: function() {
            var self = this;
            self.windowWidth = $(window).width();
            self.windowHeight = $(window).height();
            $('#puzzle-block').css({
                'width':self.windowWidth - 20,
                'height':self.windowHeight
            })
        },
        close: function() {
             $('a#CLOSE_PUZZLE').click(function(){
                $('#puzzle-block').hide();
                $('body, html').css('overflow', 'visible').scrollTop(self.docScrollTop).scrollLeft(self.docScrollLeft);
                
                return false;
            });
        }
    };

    puzzleGame.init($('.puzzle-init'));	
    
    
    
    // Quiz game. "Image Quiz" (Астрахань)
    var ImageGame = {
        
        /** Выделить элемент
         *  @param {object} image
         */
        hilightImage: function(image){
            image.parent().addClass('selected');
        },
        
        /** Убрать выделение с элемента
         *  @param {object} image
         */
        deHilightImage: function(image){
            
           image.parent().removeClass('selected');
        },
        
        /** Показать результат
         *  @param {object} object
         */
        showResult: function(object){
           if (object.hasClass('invisible'))  {
                object.removeClass('invisible')
                      .addClass('show')
                      .siblings().addClass('invisible');
            }
        },
        
        /** Запуск
         *  
         */
        start: function(){
            var self = this;
           
            $('.choice div').click(function(){
                var block = $(this),
                    otherBlocks = block.parent().siblings('.choice'),
                    choiseRes = block.closest('article').find('.choice-result'),
                    choiceNoRes = choiseRes.find('.choice-no-result'),
                    choiceYesRes = choiseRes.find('.'+$(this).attr('id')+'-result');
                
                if (block.data('clicked')) {
                    block.data('clicked', false);
                    
                    self.deHilightImage(block);
                    self.showResult(choiceNoRes)
                    
                } else {
                    block.data('clicked', true);

                    self.hilightImage(block);
                    self.showResult(choiceYesRes);
                    
                    otherBlocks.each(function(){
                        var otherBlock = $(this).find('div');
                        
                        if (otherBlock.data('clicked')) {
                            otherBlock.data('clicked', false);
                            self.deHilightImage(otherBlock);
                        }
                    })
                }

                return false;
                
            })
            
        },
    }
    ImageGame.start();
    
    
})
