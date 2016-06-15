<?php
//v004
//BaseAndCanonical
//=========================================================================================
//MODX_SITE_URL = http://domain.ru/
	//$_SERVER[ 'REDIRECT_URL' ] = /sdfsdf/mail.html
	
	//<base href="" />
	//<link rel="canonical" href="http://domain.ru/404.html" />
	//<base_and_canonical>

$html= $modx->documentOutput;

if( true || isset( $_GET[ 'test' ] ) )
{
	$wsite= rtrim( MODX_SITE_URL, "/" );
	$redirurl= ( ! empty( $_SERVER[ 'REDIRECT_URL' ] ) ? $_SERVER[ 'REDIRECT_URL' ] : '/' );
	//$redirurl= $modx->makeUrl( $modx->documentIdentifier );
	
	$uri= strpos( $_SERVER[ 'REQUEST_URI' ], "?" );
	$get= '';
	if( $uri !== false )
	{
		$get= substr( $_SERVER[ 'REQUEST_URI' ], $uri );
	}
	
	$canonical= $wsite . $redirurl .( $get ? $get : '' );
	$base= MODX_SITE_URL;
	
	if( ! empty( $canonical ) && ! empty( $base ) )
	{
		$base_and_canonical .= '<base href="'. $base .'" />';
		$base_and_canonical .= "\r\n\t";
		$base_and_canonical .= '<link rel="canonical" href="'. $canonical .'" />';
	}
	
	$html= str_replace( "<base_and_canonical />", $base_and_canonical, $html );
	
	/*
	//preg_match_all( "/<link (.*)rel=\"canonical\"(.*)href=(\"(.*)\"|'(.*)')(.*)>/imU", $html, $ss );
	preg_match_all( "/<link (.*)rel=\"canonical\"(.*)>/imU", $html, $ss );
	
	if( ! empty( $ss[ 0 ][ 0 ] ) )
	{
		preg_match_all( "/href=(\"(.*)\"|'(.*)')/imU", $ss[ 0 ][ 0 ], $sss );
		
		$redirurl= ( ! empty( $_SERVER[ 'REDIRECT_URL' ] ) ? $_SERVER[ 'REDIRECT_URL' ] : '/' );
		
		print 'Установлен: '. $sss[ 2 ][ 0 ] ."\r\n";
		print 'Нужно:      '. $wsite . $redirurl ."\r\n";
	}
	*/
}

$modx->documentOutput= $html;
?>
