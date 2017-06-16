(function($){


	var shop_basket_timeout;

	
	$(document).ready(function(){
		$('.shop_basket_form .chbf_inp').each(function(){
			if($('textarea',this).length) $('textarea',this).textareaAutoSize();
			var elem= $(this);
			var input= $('input,textarea',this);
			if(input.val()!='') elem.addClass('chbf_inp_s');
			input.focus(function(){
				if( ! elem.hasClass('chbf_inp_s')) elem.addClass('chbf_inp_s');
				elem.addClass('chbf_inp_f');
			});
			input.focusout(function(){
				if(input.val()=='') elem.removeClass('chbf_inp_s');
				elem.removeClass('chbf_inp_f');
			});
		});
		$('.shop_basket_form .chbf_inp .chbf_lab').on('click',function(){
			$('input,textarea',$(this).parent()).focus();
		});

		$('.addtobasket').on('click',function(){
			var box= $(this).parent();
			$('.svgloading', box).show();
			var itemid= $(this).data('itemid');
			$.ajax('/ajax/?ajax&a1=shop&a2=add&id='+itemid)
			.done(function(data){
				$('.svgloading', box).hide();
				$('.addtobasket', box).hide();
				$('.cib_ok', box).show();
				var res= $.parseJSON(data);
				if(res.cc>=1) $('.shopbasket .shb_cc').show().text(res.cc);
					else $('.shopbasket .shb_cc').hide().text('0');
			});
		});

		$('.shop_basket_form input, .shop_basket_form textarea').on('change',function(){
			var nm= $(this).attr('name');
			var vl= $(this).val();
			$.ajax("/ajax/?a1=shop&a2=data&nm="+nm+"&vl="+vl);
		});
	});


	$(document).on('click','.shop_basket_items .shbi_prms_but',function(){
		var elem= $('.shbi_prms_box',$(this).parent());
		$(this).remove();
		elem.stop().animate({ opacity:1, height: $('.shbi_prms',elem).height() }, 500);
	});


	$(document).on('click','.shop_basket_items .shbi_cc .shbi_plus_minus, .shop_basket_items .shbi_del',function(){
		clearTimeout(shop_basket_timeout);
		$('.shop_basket_checkout').addClass('chbch_disabled');
		
		var elem= $(this).parent();
		while( ! elem.hasClass('item')) elem= elem.parent();
		var u= elem.data('u');
		
		$('.shbi_itogosum .svgloading').show();
		
		var a2= 'count';
		if($(this).hasClass('shbi_del')) a2= 'delete';

		if(a2=='count')
		{
			$('.shbi_sum .svgloading',elem).show();

			var count= parseInt($('.shbi_ccval',elem).text());
			if($(this).hasClass('shbi_plus')) count++; else count--;
			if(count<=0) count= 1;
			$('.shbi_ccval',elem).text(count);

		}else if(a2=='delete'){
			$('>.svgloading',elem.parent()).show();
		}
		
		shop_basket_timeout= setTimeout(function(){
			$.ajax('/ajax/?a1=shop&a2='+a2+'&u='+u+'&count='+count)
			.done(function(data){
				$('.shop_basket_items').html(data);

				$.ajax('/ajax/?a1=shop&a2=itogo')
				.done(function(data){
					$('.shop_basket_itogo').html(data);
				});
			});
		}, (a2=='count' ?700 :0));
	});


	$(document).on('click','.shop_basket_itogo .shbi_checkout .shop_basket_checkout',function(){
		if($('.shop_basket_checkout').hasClass('chbch_disabled')) return;
		$('.shop_basket_checkout').addClass('chbch_disabled');
		$('.shbi_checkout .svgloading').show();
		
		var code= $(this).data('code');
		var itogo= $(this).data('itogo');
		$.post('/ajax/?a1=shop&a2=checkout&itogo='+itogo, $('.shop_basket_form form').serialize())
			.done(function(data){
				$('.shop_basket_checkout').removeClass('chbch_disabled');
				$('.shbi_checkout .svgloading').hide();
				data= $.parseJSON(data);
				if(data.result=='ok')
				{
					$('.shbi_errors').hide();
					window.location= $('.shop_basket_form form').attr('action') +'?c='+code+'&s='+data.s;
				}else{
					$('.shbi_errors').show().html(data.errors);
				}
			});
	});

})(jQuery);