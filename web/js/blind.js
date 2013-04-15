var settings = [],
	cookie = null,
	cookieStr = '',
	indexes = [];


	
$(function(){
	
	cookie = $.cookie('settings');

	if (cookie != null) {
		indexes = cookie.split(',');
		for (var i in indexes) {
            $('body').addClass(indexes[i]);
			$('#'+indexes[i]).find('span').addClass('active');
			$('#'+indexes[i]).siblings().find('span').removeClass('active');
        }
	}
	$('#settings li li').on('click', function() {
		var bodyOption = $(this),
			bodyClass = bodyOption.attr('id'),
			otherOptions = $(this).siblings(),
			otherOptionsClasses = [],
            found = false; 
            
		
		$(otherOptions).each(function(){
			otherOptionsClasses.push($.trim($(this).attr('id')));
		})
		
		cookie = $.cookie('settings');
		if (cookie != null) {
			indexes = cookie.split(',');
		}
		
		if (!bodyOption.find('span').hasClass('active')) {
			if (cookie != null) {
				for (var i in indexes) {
					if(indexes[i] == bodyClass) {
						found = true;
					}
				}

				if (otherOptionsClasses.length > 0) {
					for (var i in indexes) {
						for (var j in otherOptionsClasses) {
							if(indexes[i] == otherOptionsClasses[j]) {
								indexes.splice(i,1);
							}
						}
					}
				}
			}
			
			if (!found) {
                indexes.push($.trim(bodyClass));
                $('body').removeClass();
                for (var i in indexes) {
                    $('body').addClass(indexes[i]);
                }
                indexesStr=indexes.join(',');
                $.cookie('settings', indexesStr);
            }	
		}
		
		bodyOption.find('span').addClass('active');
		otherOptions.find('span').removeClass('active');
		
		
	}
	)
	//$.cookie('settings', null);
	
	
	if ($.cookie('settings-hidden') != null) {
		$('.settings-handler').addClass('settings-hidden');
		$('#settings').hide();
	}
	
	$('.settings-handler').click(function(){
		if (!$(this).hasClass('settings-hidden')) {
			$.cookie('settings-hidden', true);
		} else {
			$.cookie('settings-hidden', null);
		}
		$(this).toggleClass('settings-hidden');
		$('#settings').toggle();
	})
	
	
	
	$('.expand-handle a').click(function(){
		var expandHandle = $(this).parent();
		expandHandle.toggleClass('expanded-handle');
		expandHandle.next('.expand-text').slideToggle();
		return false;
	})
	
    // Табы
	$('#tabs-selector a').click(function(){
		var tab = $(this).attr('href');
		$(this).parent().addClass('active')
						.siblings().removeClass('active');
		$(tab).show()
              .siblings('.tab').hide();
		return false;
	})

	$('.btn-more a .more').click(function(){
		var link = $(this).parent();
        $("div#" + link.attr('rel')).slideDown();
		//link.addClass("opened");
        link.remove();
		 return false;
	});
	
    // Комплощадка
    $('.tabs-headers').on('click', 'a', function(){
		var tabId = $(this).attr('href');
		$(this).addClass('active')
			   .parent().siblings().find('a').removeClass('active');
		$(tabId).show()
                .siblings('.tab').hide();
		return false;
	})
    
    new Ya.share({
         element: 'yandex-share-global',
         elementStyle: {
             'type': 'none',
             'border': false,
             'quickServices': ['vkontakte', 'lj', 'twitter', 'facebook', 'odnoklassniki']
         },
         link: 'http://culture.ru/',
         title: 'Портал культурного наследия России',
         description: 'Портал культурного наследия России'
     });
	
    
    $('.search-checkboxes label').click(function(e){
        if (e.target.nodeName == 'INPUT') {
            if ($(this).hasClass('checked')) {
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
        }
    })
   $( "#search-this-section:checked" ).parent().addClass('checked');
		
	
})
