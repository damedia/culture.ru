		(function($){
			var monthNames = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
			
			var rlCalendar = function(options) {
				this.options = $.extend({
					container: null,
					date: new Date(),
					selectDays: [14, 17],
					changingDates: function(year, month) {},
					changedDates: function(year, month) {}
				}, options);
				
				if(!this.options.container || !this.options.container.length)
					throw 'set container object';
				
				if(!this.options.date || Object.prototype.toString.call(this.options.date) !== '[object Date]')
					throw 'set date object';
				
				this._isFirstLaunch = true;
				this._container = this.options.container;
				this._dom;
				
				this.setDate();
				
				this.build();
				
				this._container.rlc = this;
			};
			
			rlCalendar.prototype.setDate = function(date) {
				date = date || new Date();
				
				this.options.date = date;
				this._year = date.getFullYear();
				this._month = date.getMonth();
				this._dayCount = (new Date(this._year, this._month + 1, 0)).getDate();
			};
			
			rlCalendar.prototype.build = function() {
				var _rlc = this;
				
				if(this._dom && this._dom.length)
					this._dom.remove();
				
				this._dom = $('<div class="rlc"><div class="rlc-days-wrap"></div></div>');
				
				// build title
				var prevMonth = this._month - 1 < 0 ? 11 : (this._month - 1);
				var nextMonth = this._month + 1 > 11 ? 0 : (this._month + 1); 
				var domTitle = $('<div class="rlc-months">'
						+ '<div class="rlc-month item-prev"><a href="javascript:void(0)">&larr; ' + monthNames[prevMonth] + '</a></div>'
						+ '<div class="rlc-month item-center">' + monthNames[this._month] + '</div>'
						+ '<div class="rlc-month item-next"><a href="javascript:void(0)">' + monthNames[nextMonth] + ' &rarr;</a></div>'
					+ '</div>');
				
				domTitle.find('.item-prev')
				.bind('click.rlc', function(e) {
					var year = _rlc._year;
					var month = _rlc._month - 1;
					
					if(month < 0) {
						month = 11;
						year--;
					}
					
					_rlc.setDate(new Date(year, month));
					_rlc.selectDays(1, 3);
					
					_rlc.build();
					
					return false;
				});
				
				domTitle.find('.item-next')
				.bind('click.rlc', function(e) {
					var year = _rlc._year;
					var month = _rlc._month + 1;
					
					if(month > 11) {
						month = 0;
						year++;
					}					
					
					_rlc.setDate(new Date(year, month));
					_rlc.selectDays(1, 3);
					
					_rlc.build();
					
					return false;
				});
				
				this._dom.prepend(domTitle);
				
				// build table
				var domTable = $('<table class="rlc-days"><tr></tr></table>');
				
				for(i = 1; i <= this._dayCount; i++) {
					var isWeekend = (new Date(this._year, this._month, i)).getDay() % 6 == 0;						
					domTable.find('tr').append('<td class="rlc-day' + (isWeekend ? ' weekend' : '') + '"><div class="rlc-day-wrap">' + i + '</div></td>');
				}
				
				this._dom.find('.rlc-days-wrap').append(domTable);
				
				// build magnifier
				var domMagnifier = $('<div class="rlc-magnifier">'
						+ '<div class="rlc-magnifier-frame">'
							+ '<div class="item-left"></div>'
							+ '<div class="item-right"></div>'
							+ '<div class="item-center"></div>'
						+ '</div>'
						+ '<div class="item-left rlc-magnifier-drag"></div>'
						+ '<div class="item-right rlc-magnifier-drag"></div>'
						+ '<div class="item-center rlc-magnifier-drag"></div>'
					+ '</div>');
					
				var handleLeft = domMagnifier.find('.item-left.rlc-magnifier-drag');
				var handleRight = domMagnifier.find('.item-right.rlc-magnifier-drag');
				var handleCenter = domMagnifier.find('.item-center.rlc-magnifier-drag');
				
				var handleLastPosLeft = null;
				
				var lastDayCount = this.options.selectDays[1] - this.options.selectDays[0];
				
				domMagnifier.find('.rlc-magnifier-drag')
				.draggable({
					axis: "x",
					stop: function() {
						_rlc._isDrag = false;
						
						if(_rlc.options.changedDate) {
							var year = _rlc._year;
							var month = _rlc._month + 1;
							var from = _rlc.options.selectDays[0];
							var to = _rlc.options.selectDays[1];
							_rlc.options.changedDate.call(_rlc, [year, month, from], [year, month, to]);
						}
						
						_rlc.resetHandlePosition();
					},
					drag: function(event, ui) {
						_rlc._isDrag = true;
					
						var from = _rlc.options.selectDays[0];
						var to = _rlc.options.selectDays[1];
						
						if(handleLastPosLeft === null)
							handleLastPosLeft = ui.offset.left;
						var directionLeft = handleLastPosLeft >= ui.offset.left;
												
						var handlePosLeft = $(event.target).offset().left;						
						var handlePosRight = handlePosLeft + $(event.target).outerWidth(true);
						var handleCenterOffset = _rlc._dom.find('.rlc-magnifier-frame').outerWidth(true)/2 - $(event.target).outerWidth(true)/2;
						
						var newFrom = 0;						
						var newTo = 0;
						
						if(handlePosLeft < _rlc._dom.find('td:first').offset().left) {
							if(event.target === handleCenter[0]) {
								newFrom = 1;
								newTo = to - (from - 1);
							}
							
							if(event.target === handleLeft[0]) {
								newFrom = 1;
							}
						}
						
						_rlc._dom.find('td').each(function(i) {
							var td = $(this);
							
							if((event.target === handleLeft[0] && newFrom) ||
								(event.target === handleRight[0] && newTo) ||
								(event.target === handleCenter[0] && newFrom && newTo))
								return false;
							
							var tdLeft = td.offset().left;
							var tdRight = tdLeft + td.outerWidth(true);
							
							if(event.target === handleLeft[0] || event.target === handleRight[0]) {
								if(directionLeft) {
									if(handlePosLeft >= tdLeft && handlePosLeft < tdRight) {
										if(event.target === handleLeft[0]) {
											newFrom = i + 1;
										}
										
										if(event.target === handleRight[0]) {
											newTo = i + 1;
										}
									}
								} else {
									if(handlePosRight > tdLeft && handlePosRight <= tdRight) {
										if(event.target === handleLeft[0]) {
											newFrom = i + 1;	
										}
										
										if(event.target === handleRight[0]) {
											newTo = i + 1;	
										}
									}
								}
							}
							
							if(event.target === handleCenter[0]) {
								if((handlePosLeft - handleCenterOffset) >= tdLeft && (handlePosLeft - handleCenterOffset) < tdRight) {
									newFrom = i + 1;										
									delta = from - newFrom;										
									newFrom = i + 1;
									newTo = to - delta;
									
									if(newTo > _rlc._dayCount) {
										newFrom = newFrom - (newTo - _rlc._dayCount);
										newTo = _rlc._dayCount;
									}
								}
							}
						});
						
						if(newFrom <= 0)
							newFrom = from;
						
						if(newTo <= 0)
							newTo = to;
						
						if(newFrom != from || newTo != to) {
							_rlc.selectDays(newFrom, newTo);
						}
						
						handleLastPosLeft = ui.offset.left;
						lastDayCount = to - from;
					}
				});
				
				this._dom.find('.rlc-days-wrap').append(domMagnifier);
				
				this._container.append(this._dom);
				
				this.selectDays();

				this.resetHandlePosition();
				
				$(window).bind('resize.rlc', function() {
					_rlc.selectDays(undefined, undefined, true);
					_rlc.resetHandlePosition();
				});
			};
			
			rlCalendar.prototype.resetHandlePosition = function() {
				var magnifierPosLeft = this._dom.find('.rlc-magnifier').offset().left;
				var magnifierFrame = this._dom.find('.rlc-magnifier-frame');
				
				this._dom.find('.item-left.rlc-magnifier-drag').css('left', magnifierFrame.find('.item-left').offset().left - magnifierPosLeft);
				this._dom.find('.item-right.rlc-magnifier-drag').css('left', magnifierFrame.find('.item-right').offset().left - magnifierPosLeft);
				this._dom.find('.item-center.rlc-magnifier-drag').css('left', magnifierFrame.find('.item-center').offset().left - magnifierPosLeft);
			}
			
			rlCalendar.prototype.selectDays = function(from, to, isFullscreenSender) {
				if(from === undefined || to === undefined) {
					from = this.options.selectDays[0];
					to = this.options.selectDays[1];
				}
				
				from = from < 1 ? 1 : from;
				to = to > this._dayCount ? this._dayCount : to;
				
				if(to - from < 0)
					return;
				
				var frameLeftPos = -1;
				var frameWidth = 0;
				
				this._dom.find('td')
				.removeClass('selected')
				.each(function(i) {
					if(i+1 >= from && i+1 <= to) {
						$(this).addClass('selected');
					}
				})
				.each(function(i) {
					if(i+1 >= from && i+1 <= to) {
						if(frameLeftPos < 0)
							frameLeftPos = $(this).position().left;
					}
				});
				
				var toTd = this._dom.find('td:eq(' + to + ')');
				
				if(to >= this._dayCount) {
					var toTd = this._dom.find('td:eq(' + (to - 1) + ')');
					frameWidth = toTd.position().left + toTd.outerWidth(true) - this._dom.find('td:eq(' + (from - 1) + ')').position().left - 1;	
				} else {
					frameWidth = this._dom.find('td:eq(' + to + ')').position().left - this._dom.find('td:eq(' + (from - 1) + ')').position().left - 1;
				}	
				
				this._dom.find('.rlc-magnifier-frame').css({
					'left': frameLeftPos,
					'width': frameWidth
				});
				
				this.options.selectDays = [from, to];
				
				if(to - from == this._dayCount - 1) {
					this._dom.find('.rlc-magnifier .item-center').hide();
				} else {
					this._dom.find('.rlc-magnifier .item-center').show();
				}
				
				var year = this._year;
				var month = this._month + 1;
				
				if(this._isDrag) {
					if(this.options.changingDate)
						this.options.changingDate.call(this, [this._year, this._month, from], [this._year, this._month, to], isFullscreenSender);
				} else {
					if(this.options.changedDate)
						this.options.changedDate.call(this, [this._year, this._month, from], [this._year, this._month, to], isFullscreenSender);
				}
				
				if(this._isFirstLaunch)
					this._isFirstLaunch = false;
			};
			
			$.fn.rlCalendar = function( options ) {
				return this.each(function() {
					options = options || {};
					options.container = $(this);
					new rlCalendar(options);
				});
			};
		})( jQuery );