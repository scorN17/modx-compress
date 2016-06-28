<?php
$varsion= 'v1.1';
//28.06.2016
//CompressHTML
//Event: OnWebPagePrerender
//============================================================================
if( ! isset( $_GET[ 'test' ] ) )
{
	$filecontent= $modx->documentOutput;

	$filetype= '.html';

	$strtr[ '.html' ]= array(
	);
	$pregreplace[ '.html' ][0]= array(
		"/<!--[^\[](.*)-->/sU" => "",
		"/[\r]/" => "",
		"/[\t]/" => "\n",
		"/[\n]{2,}/" => "\n",
		"/[ ]{2,}/" => " ",
		"/(\n )/" => "\n",
	);

	if( isset( $strtr[ $filetype ] ) ) $strtr_type= $strtr[ $filetype ];
	if( isset( $pregreplace[ $filetype ][0] ) ) $pregreplace_type_0= $pregreplace[ $filetype ][0];
	if( isset( $pregreplace[ $filetype ][1] ) ) $pregreplace_type_1= $pregreplace[ $filetype ][1];

	if( $strtr_type ) $filecontent= strtr( $filecontent, $strtr_type );
	if( $pregreplace_type_0 )
	{
		foreach( $pregreplace_type_0 AS $pattern => $replacement )
			$filecontent= preg_replace( $pattern, $replacement, $filecontent );
	}

	$modx->documentOutput= $filecontent;
}
?>
