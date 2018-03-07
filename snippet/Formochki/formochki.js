
(function($){

	$(document).ready(function(){
		$('.formochki .frm_itm').each(function(){
			var elem = $(this);
			var input = $('input[type="text"]',this);
			input.focus(function(){
				elem.addClass('focustext');
			});
			input.focusout(function(){
				if (input.val() == '') {
					elem.removeClass('focustext');
				}
			});
		});
	});

	$(window).on('load',function(){
		$('.formochki .frm_submit button').each(function(){
			var elem= $(this).parent();
			while( ! elem.hasClass('formochki'))
				elem= elem.parent();

			$('.frm_result',elem).hide();

			$('form',elem).submit(function(){
				return false;
			});

			$(this).click(function(){
				if(elem.hasClass('frm_submit_load')) return;
				elem.addClass('frm_submit_load');
				var formData= new FormData($('form',elem)[0]);
				var ajaxresult = $.ajax({
					url: $('form',elem).attr('action')+'?ajax&act=formochki_send',
					data: formData,
					processData: false,
					contentType: false,
					type: 'POST',
					dataType: 'JSON',
					cache: false
				});
				ajaxresult.done(function(result){
					elem.removeClass('frm_submit_load');
					if (result.result == 'ok') {
						$('form',elem).remove();
						$('.frm_result_ok',elem)
							.html(result.text)
							.show();
					} else {
						$('.frm_result_error',elem)
							.html(result.text)
							.show();
					}
				});
				return;
			});
		});
	});
})(jQuery);
