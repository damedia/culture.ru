$(function(){
		$('.choice img').click(function(){
                var image = $(this),
                    otherImages = $(this).parent().siblings('.choice'),
                    choiseRes = image.closest('.choose-panel').next('.choice-result');
                
                if(image.data('clicked')) {
                    image.data('clicked', false);
                    deHilightImage(image);
                    choiseRes.find('div').hide();
                    choiseRes.find('.choice-no-result').show();
                
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
                    
                    choiseRes.find('div').hide();
                    choiseRes.find('.'+$(this).attr('id')+'-result').show();
                    
                }
                
            })
            
            
            function hilightImage(image) {
                var imageSrc = image.attr('src'),
                    imageSrcLength = imageSrc.length;
                    console.log();
                
                image.attr('src', imageSrc.substr(0, imageSrcLength-4)+'-active.png');
            }
            function deHilightImage(image) {
                var imageSrc = image.attr('src'),
                    imageSrcLength = imageSrc.length;
                    console.log();
                
                image.attr('src', imageSrc.substr(0, imageSrcLength-11)+'.png');
            }
            
            
            //$('.hidden-parts div').css({'visibility':'hidden'}).show();
            $('.hidden-parts div').click(function(){
                $(this).removeClass('inv');
            })
            
            if($('.scroll-pane').length > 0) {
                $('.scroll-pane').jScrollPane();
            }
            
            $('.close').click(function(){
                $(this).parent('.window').hide();
                $('.overlay').hide();
                return false;
            })
            
            $('.learn-more-link').click(function(){
                $('.window').show();
                $('.overlay').show();
                return false;
            })
            $('.window').css('visibility','visible').hide();
            
		})