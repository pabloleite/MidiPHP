/*
	Input holplacer + focus plugin by midi
	
	> Usage:
		$('anySelector').inputfocus();
		
		Aditional parameters, they are all optional
		$('anySelector').inputfocus({
			focus_class: 'focusClass', 	   // default: 'focus'	 - class added when the input has the focus
			idle_class: 'idleClass', 	   // default: 'idle'	 - class added when the input is showing its idle watermark
			idle_value: 'my value string', // default: ''		 - text showed when the input is idle
			idle_color: '#ACACAC'		   // default: '#A7A7A7' - CSS property when the input is idle, set to false to not change the color
		});
	
	> Based on:
		Copyright (c) 2009 Simone D'Amico (http://www.simonedamico.it/2009/08/jquery-inputfocus-evidenziare-i-campi-input-e-textarea-di-una-form/)
			-- and --
		Copyright (c) 2007 Josh Bush (http://digitalbush.com/projects/watermark-input-plugin/)
*/

(function($) {
	var map = new Array();
	
	$.fn.inputfocus = function(params) {
	
		params = $.extend({
			focus_class: "focus",		//class name of focus event
			idle_class: "idle",			//class name of an idle input
			idle_value: "",				//value of the object on blur event
			idle_color: "#A7A7A7"		//color of the holdplacer value
		}, params);
		
		this.each(function() {
			var input = $(this);
			var default_color = input.css('color');
			
			map[map.length] = {
				text: params.value, 
				obj: input, 
				DefaultColor: default_color, 
				WatermarkColor: params.idle_color
			};
			
			
			input.focus( function() {
				input.addClass(params.focus_class);
				if( this.value == params.idle_value )
				{
					if( params.idle_color )
						input.css('color', default_color);
					this.value = '';
					input.removeClass(params.idle_class);
				}
			})
			.blur( function() {
				input.removeClass(params.focus_class);
				if( this.value == '' )
				{
					if( params.idle_color )
						input.css('color', params.idle_color); //input defined ?
					this.value = params.idle_value;
					input.addClass(params.idle_class);
				}
			})
			.trigger('blur');
			
			input.closest('form').submit(function() {
				if( input.val()==params.idle_value )
					input.val('');
			});
			
		});
		
		return this;
	};
})(jQuery);