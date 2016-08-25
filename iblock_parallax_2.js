



(function($){
	var window_scroll, block, wwpos, interval, proc, tt;
	
	
	$(document).ready(function(){
		$('.iblock_parallax').css({
			position: 'absolute',
			zIndex: 1,
			top: 0,
			left: 0,
			opacity: 0,
			maxWidth: 'none'
		});
	});
	
	
	$(window).on('load',function(){
		serv_img_sz();
		serv_img_pos();
		$('.iblock_parallax').stop().animate({ opacity: 1 }, 500);
	});
	
	
	$(window).scroll(function(){
		serv_img_pos();
	});
	
	
	$(window).resize(function(){
		serv_img_sz();
		serv_img_pos();
	});


	function serv_img_pos()
	{
		window_scroll= $(window).scrollTop();
		wwpos= ($(window).height()+window_scroll);
		$('.iblock_parallax').each(function(){
			block= $(this).parent();
			if(wwpos>block.offset().top-50 && window_scroll<block.offset().top+block.outerHeight()+50)
			{
				interval= ($(window).height()+block.outerHeight()) + 100;
				proc= wwpos-block.offset().top + 50;
				proc= proc * 100 / interval; //console.log(proc);
				
				tt= $(this).height()-block.outerHeight();
				tt= tt * proc / 100 * (-1); //console.log(tt);
				$(this).css({ top: tt });
			}
		});
	}

	function serv_img_sz()
	{
		$('.iblock_parallax').each(function(){
			$(this).css({ width:'auto', height:$(window).height() });
			$(this).css({ left:(-1)*($(this).parent().outerWidth()-$(this).width())/2 });
			if($(this).width()<$(this).parent().outerWidth())
				$(this).css({ width:$(this).parent().outerWidth(), height:'auto', left:0 });
		});
	}
	
})(jQuery);
