
var $= jQuery.noConflict();



$(document).ready(function(){
//	$( '.blfp_itm_active' ).parent().parent().addClass( 'open' );
	
	$( '.blockfilter .blfp_itm' ).click(function(){
		if( $( this ).hasClass( 'blfp_itm_active' ) )
		{
			$( this ).removeClass( 'blfp_itm_active' );
			$( this ).data( 'sel', 'net' );
		}else{
			$( this ).addClass( 'blfp_itm_active' );
			$( this ).data( 'sel', 'da' );
		}
	});
	
	/*$( '.blockfilter .blfp_tit' ).click(function(){
		var elem= $( '.blfp_itms', $( this ).parent() );
		if( $( this ).parent().hasClass( 'open' ) )
		{
			$( this ).parent().removeClass( 'open' );
			elem.hide();
		}else{
			$( this ).parent().addClass( 'open' );
			elem.css({ opacity: 0 });
			elem.show();
			elem.animate({ opacity: 1 }, 500 );
		}
	});*/
	
	/*$(document).on( 'click', '.catalog_vivod_po', function(){
		var po= $( this ).data( 'po' );
		$.ajax( 'ajax.html&act=catalog_vivod_po&po='+ po );
	});
	$(document).on( 'click', '.catalog_vivod_po', filter );*/
});


//$(document).on( 'click', '.catalogfilter .catalogfilter__val', filter );
$(document).on( 'click', '.catalogfilter .blfp_button', filter );


function filter()
{
	$( "html, body" ).animate({ scrollTop: $( '.catalog' ).offset().top - 200 }, "fast" );
	
	var pageid= $( this ).parent().parent().parent().data( 'pageid' );
	var pageurl= $( this ).parent().parent().parent().data( 'url' );
	//var pagenum= $( this ).parent().parent().parent().data( 'pagenum' ); // Помогает оставаться на той же странице
	var pagenum= 0;
	/*var param= $( this ).parent().parent().data( 'param' );
	var val= '';
	var full_flag= true;
	$( '.catalogfilter__val_'+ param ).each(function(){
		if( $( this ).data( 'sel' ) == 'da' ) val += ( val ? '-' : '' ) + $( this ).data( 'val' );
			else full_flag= false;
	});
	if( full_flag ) val= '';
	change_window_location( param, val, pageurl );*/
	var postparam= get_filter_params();
	history.replaceState( null, null, pageurl + ( postparam ? 'x/'+ postparam +'/' : '' ) );
	get_catalog( postparam, pageid, pagenum );
}

function get_catalog( postparam, pageid, pagenum )
{
	$( '#ajax_content' ).animate({ opacity: 0.2 }, 300, function(){
		$.post( 'ajax.html&act=catalogfilter_get_items&id='+ pageid +'&p='+ pagenum, {"filterpr":postparam} ).done(function( data ){
			$( '#ajax_content' ).html( data ).animate({ opacity: 1 }, 300 );
		});
	});
	return false;
}

function get_filter_params()
{
	var postparam= '';
	var arr= [];
	var prmcc= [];
	var param_val= '';
	
	$( '.catalogfilter__val' ).each(function(){
		var param= $( this ).data( 'nm' );
		var paramtype= $( this ).data( 'tp' );
		if( paramtype == '2' )
		{
			param_val= $( this ).val();
			if( ! param_val ) param_val= 0;
		}else if( paramtype == '4' ){
			param_val= $( this ).val();
			if( ! param_val ) param_val= 0;
		}else{
			param_val= $( this ).data( 'val' );
			if( $( this ).data( 'sel' ) != 'da' ) param_val= '';
		}
		if( param_val != '' )
		{
			if( ! prmcc[ param ] ) prmcc[ param ]= 0;
			prmcc[ param ]++;
			arr[ param ]= ( arr[ param ] ? arr[ param ] +'-' : '' ) + param_val;
			if( arr[ param ] == '0-0' ) arr[ param ]= '';
		}
	});
	for( var i in arr )
	{
		if( arr[ i ] && arr[ i ] != '-' /*&& prmcc[ i ] < $( '.catalogfilter__val_'+i ).length*/ )
		{
			postparam += ( postparam ? '/' : '' ) + i +'_'+ arr[ i ];
		}
	}
	return postparam;
}

function change_window_location( param, val, pageurl )
{
	var get_params= window.location.pathname;
	get_params= get_params.split( "x/" );
	if( get_params.length == 2 ) get_params= parseGetParams( get_params[ 1 ] );
		else get_params= {};
	get_params[ param ]= val;
	var newurl= '';
	for( var i in get_params )
	{
		if( get_params[ i ] && get_params[ i ] != '-' && i )
		{
			newurl += ( newurl ? '/' : '' ) + i +'_'+ get_params[ i ];
		}
	}
	history.replaceState( null, null, pageurl + ( newurl ? 'x/'+ newurl +'/' : '' ) );
	return false;
}

function parseGetParams( params )
{
	var $_GET= {};
	var __GET= params.split( "/" );
	for( var i=0; i < __GET.length; i++ )
	{
		if( __GET[ i ] )
		{
			var getVar= __GET[ i ].split( "_" );
			$_GET[ getVar[ 0 ] ]= typeof( getVar[ 1 ] ) == "undefined" ? "" : getVar[ 1 ];
		}
	}
	return $_GET;
}