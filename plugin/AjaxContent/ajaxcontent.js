
(function($){
	var ajaxcontent_flag= [];
	
	$(window).scroll(function(){
		get_ajaxcontent();
	});
	
	$(window).on('load',function(){
		get_ajaxcontent();
	});
	
	var get_ajaxcontent= function(){
		$('.ajaxcontent').each(function(){
			var div= $(this);
			var divnm= div.data('ii')+'_'+div.data('kk');
			if($(document).scrollTop() >= div.offset().top - $(window).height() - 1000 && ! ajaxcontent_flag[divnm])
			{
				ajaxcontent_flag[divnm]= true;
				$.ajax({
					url: div.data('page')+'?ajaxcontentii='+div.data('ii')+'&ajaxcontentkk='+div.data('kk')+'&ww='+$(window).width(),
					success:function(data)
					{
						div.html(data).css({ height:'auto' });
					}
				});
			}
		});
	};


})(jQuery);
