/*
v02
=====================================================================
*/

(function($){ //var $= jQuery.noConflict();


$(window).load(function(){
	
	superslider_init( '.indexslider', 580, 200, false );
	
});



$(document).ready(function(){
	
	
});

})(jQuery);



var _megaslider_timeout= null;
function superslider_init( classs, ww, hh, sliderww )
{
	var elem= $( classs );
	if( ! elem.length ) return;
	
	var ww_flag= ( sliderww == ww ? true : false );
	if( ! ww ) ww= '100%';
	if( ! sliderww ) sliderww= '100%';
	
	elem.data( 'ii', 0 );
	
	$( '>div', elem ).wrapAll( '<div class="indsld_block"><div class="indsld_slides"></div></div>' );
	
	$( '.indsld_block', elem ).append( '<div class="indsld_ltrt"></div>' );
	$( '.indsld_ltrt', elem ).css({ width: ww, height: 0, margin: 'auto' });
//	$( '.indsld_block .indsld_ltrt', elem ).append( '<div class="indsld_act12 indsld_act1">&nbsp;</div>' );
	$( '.indsld_block .indsld_ltrt', elem ).append( '<div class="indsld_act12 indsld_act2">&nbsp;</div>' );
	
	$( '.indsld_block', elem ).append( '<div class="indsld_nav"></div>' );
	
	$( '.indsld_slides', elem ).css({ width: sliderww, height: hh, margin: 'auto' });
	
	var slide_cc= $( '.indsld_slides >div', elem ).length;
	
	var backgr= false;
	$( '.indsld_slides >div', elem ).each(function( index ){
		$( '>a', this ).css({ display: 'block', width: sliderww, height: hh });
		backgr= $( 'div.img img', this ).attr( 'src' );
		dopbackgr= $( 'div.img2 img', this ).attr( 'src' );
		$( 'div.img', this ).remove();
		$( 'div.img2', this ).remove();
		$( this ).wrapInner( '<div class="indsld_itemwrapp"></div>' );
		$( this ).wrapInner( '<div class="indsld_dopbackgr"></div>' );
		$( '.indsld_itemwrapp', this ).css({
			width: ww,
			height: hh,
			margin: 'auto',
		});
		$( '.indsld_dopbackgr', this ).css({
			background: 'url("'+ dopbackgr +'") center bottom no-repeat',
		});
		$( this ).css({
			width: sliderww, height: hh,
			position: 'absolute', top: ( index > 0 ? -hh : 0 ), left: 0,
			opacity: ( index > 0 ? 0 : 1 ),
			display: 'block',
			background: 'url("'+ backgr +'") center top no-repeat',
		});
		$( this ).addClass( 'indsld_item' );
		$( this ).addClass( 'indsld_item_'+ index );
		$( this ).data( 'ii', index );
		
		if( slide_cc >= 2 )
		{
			$( '.indsld_nav', elem ).append( '<div class="indsld_butt indsld_butt_'+ index +' '+( index == 0 ? 'first active' : '' )+' '+( index == slide_cc-1 ? 'last' : '' )+'" data-ii="'+ index +'">&nbsp;</div>' );
			$( '.indsld_nav .indsld_butt_'+ index ).click(function(){
				clearTimeout( _megaslider_timeout );
				if( ! $( this ).hasClass( 'active' ) ) superslider_smena( classs, $( this ).data( 'ii' ), true );
			});
		}
	});
	
	/*$( '.indsld_act1', elem ).click(function(){
		clearTimeout( _megaslider_timeout );
		superslider_smena( classs, -1, true, 'toleft' );
	});*/
	$( '.indsld_act2', elem ).click(function(){
		clearTimeout( _megaslider_timeout );
		superslider_smena( classs, -1, true );
	});
	
	elem.animate({ opacity: 1 }, 1000 );
	
	if( slide_cc >= 2 ) _megaslider_timeout= setTimeout( "superslider_smena( '"+ classs +"', -1 )", 7000 );
}
function superslider_smena( classs, ii, clear, act )
{
	var elem= $( classs );
	
	act= ( act == 'toleft' ? 'toleft' : 'toright' );
	
	var sl0= elem.data( 'ii' );
	if( ii >= 0 )
	{
		var sl1= ii;
	}else{
		var sl1= sl0 + ( act == 'toleft' ? (-1) : 1 );
	}
	if( sl1 < 0 ) sl1= $( '.indsld_slides >div', elem ).length - 1;
	if( sl1 > $( '.indsld_slides >div', elem ).length - 1 ) sl1= 0;
	
	elem.data( 'ii', sl1 );
	
	var img0= $( '.indsld_slides >.indsld_item_'+sl0, elem );
	var img1= $( '.indsld_slides >.indsld_item_'+sl1, elem );
	
	$( '.indsld_nav .active', elem ).removeClass( 'active' );
	$( '.indsld_nav .indsld_butt_'+ sl1, elem ).addClass( 'active' );
	
	img0.css({ zIndex: 10 });
	img0.stop();
	img0.animate({
		top: '+=250',
		opacity: 0
	}, 900, function(){
		$( this ).css({
			top: (-1) * $( this ).height(),
			opacity: 0
		});
	});
	
	img1.css({ opacity: 0, top: -200, zIndex: 20 });
	img1.stop();
	img1.animate({
		top: 0,
		opacity: 1
	}, 900 );
	
	if( ! clear ) _megaslider_timeout= setTimeout( "superslider_smena( '"+ classs +"', -1 )", 7000 );
}





