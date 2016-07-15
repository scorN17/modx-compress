(function($){
	$.fn.scrollanimate= function( option ){
		option= $.extend({
			scroll: true,
			effects: {},
			speed: 1000,
			pause: 0,
			huy: 1.1,
			easing: 'swing'
		}, $.fn.scrollanimate.option, option );
		
		var elem= $( this );
		
		var start_flag= false;
		
		var start= function( f_elem, f_option )
		{
			f_elem.delay( f_option.pause ).animate( f_option.effects, { easing: f_option.easing, duration: f_option.speed, complete: f_option.complete } );
		};
		
		if( ! option.scroll )
		{
			if( ! start_flag )
			{
				start_flag= true;
				start( elem, option );
			}
			
		}else{
			if( $(document).scrollTop() > elem.offset().top - ( $(window).height() / option.huy ) - 100 )
			{
				if( ! start_flag )
				{
					start_flag= true;
					start( elem, option );
				}
			}
			
			$(document).scroll(function(){
				if( $(document).scrollTop() > elem.offset().top - ( $(window).height() / option.huy ) )
				{
					if( ! start_flag )
					{
						start_flag= true;
						start( elem, option );
					}
				}
			});
		}
	}
})(jQuery);
