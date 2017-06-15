(function($){

	$(document).ready(function(){
		$('.cf_box .cf_itm').click(function(){
			if($(this).hasClass('cf_itm_s'))
			{
				$(this).removeClass('cf_itm_s');
				$(this).data('sel', 'n');
			}else{
				$(this).addClass('cf_itm_s');
				$(this).data('sel', 'y');
			}
		});
	});

	$(document).on('click', '.catalogfilter .catalogfilter__val', filter);



	function filter()
	{
		// $('html,body').animate({ scrollTop: $('.catalog').offset().top-200 }, 'fast');
		
		var elem= $(this).parent();
		while( ! elem.hasClass('catalogfilter')) elem= elem.parent();

		var pageid= elem.data('pageid');
		var pageurl= elem.data('url');

		var postparam= get_filter_params();
		$.ajax('/ajax/?act=catalogfilter_sort_params&prms='+postparam)
		.done(function(data){
			history.replaceState(null, null, pageurl+(data ? 'x/'+data+'/' :''));
			get_catalog(data, pageid);
		});
	}

	function get_catalog(postparam, pageid)
	{
		$('.catalog_ajax').animate({ opacity:0.2 }, 300, function(){
			$.post('/ajax/&act=catalogfilter_get_items&id='+pageid, {"_x":postparam})
			.done(function(data){
				$('.catalog_ajax').html(data).animate({ opacity:1 }, 300);
			});
		});
		return false;
	}

	function get_filter_params()
	{
		var params= [];
		var params_url= '';
		var param_val;
		
		$('.catalogfilter__val').each(function(){
			var param= $(this).data('nm');
			// var paramtype= $(this).data('tp');

			param_val= $(this).data('vl');
			if($(this).data('sel') != 'y') param_val= '';

			if(param_val !== '')
			{
				if(params[param] == undefined) params[param]= [];
				params[param].push(param_val);
			}
		});

		for(var i in params)
			if(params[i])
				params_url += (params_url?'/':'') +i +','+ params[i];

		return params_url;
	}



})(jQuery);

