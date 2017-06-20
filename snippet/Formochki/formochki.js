
(function($){

	$(document).ready(function(){
	});

	$(window).on('load',function(){
		$('.formochki .frm_submit').each(function(){
			if( ! $(this).hasClass('frm_submit_load'))
			{
				$(this).addClass('frm_submit_load');
				var elem= $(this).parent();
				while( ! elem.hasClass('formochki')) elem= elem.parent();

				$('.frm_result',elem).hide();

				$('form',elem).submit(function(){ return false; });

				$(this).click(function(){
					$('form .setdefaultvalue input',elem).each(function(){
						if($(this).val()==$(this).data('default')) $(this).val('');
					});
					$.post($('form',elem).attr('action')+"?ajax&act=formochki_send", $('form',elem).serialize()).done(function(data){
							var result= $.parseJSON(data);
							$('.frm_result',elem).show().removeClass('frm_result_error').removeClass('frm_result_ok').addClass('frm_result_'+result.result).html(result.text);
							if(result.result=='ok') $('form',elem).remove(); else{
								$('form .setdefaultvalue input',elem).each(function(){
									if($(this).val()=='') $(this).val($(this).data('default'));
								});
							}
						});
					return;
				});
			}
		});
	});
})(jQuery);