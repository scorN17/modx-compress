



(function($){
	var wscroll, block, wwpos, interval, proc, tt;
	
	
	$(document).ready(function(){
		$('.iblock_parallax').css({
			position: 'absolute',
			zIndex: 1,
			top: 0,
			left: 0,
			opacity: 0,
			maxWidth: 'none'
		});
		serv_img_sz();
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
		wscroll= $(window).scrollTop();
		wwpos= ($(window).height()+wscroll);
		$('.iblock_parallax').each(function(){
			block= $(this).parent();
			if(wwpos>block.offset().top-50 && wscroll<block.offset().top+block.outerHeight()+50)
			{
				interval= ($(window).height()+block.outerHeight()) + 100;
				proc= wwpos-block.offset().top + 50;
				proc= proc * 100 / interval; //console.log(proc);
				
				tt= wscroll - (200*proc/100) + 100;
				
				$(this).offset({ top: tt });
			}
		});
	}

	function serv_img_sz()
	{
		$('.iblock_parallax').each(function(){
			$(this).css({ width:'auto', height:$(window).height() });
			$(this).css({ left:(-1)*($(this).width()-$(this).parent().outerWidth())/2 });
			if($(this).width()<$(this).parent().outerWidth())
				$(this).css({ width:$(this).parent().outerWidth(), height:'auto', left:0 });
		});
	}
	
})(jQuery);
