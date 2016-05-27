<?php
//v07
//Compress
/*	&compress=true/false
	&file - компрессит в filename.compress.css один файл
	&files - компрессит в all.compress.css все указанные файлы
	&tofile - файл, в который комперссить все указанные файлы
	&r=true - принудительно пересоздает компресс-файлы
	[!Compress? &file=`css/styles.css`!]
	[!Compress? &files=`css: styles.css, catalog.css; css2: shop.css; css3/dop.css` &tofile=`css/all.compress.css`!]
*/$r=true;
//============================================================================
$strtr[ '.css' ]= array(
	"\r"=>'', "\n"=>'', "\t"=>'',
	//' ('=>'(', '( '=>'(', ' )'=>')', ') '=>')', ' {'=>'{', '{ '=>'{', ' }'=>'}', '} '=>'}', ' ['=>'[', '[ '=>'[', ' ]'=>']', '] '=>']', ' ;'=>';', '; '=>';', ' :'=>':', ': '=>':'
);
$strtr[ '.js' ]= array(
	"\r"=>'', "\n"=>'', "\t"=>'',
	//' ('=>'(', '( '=>'(', ' )'=>')', ') '=>')', ' {'=>'{', '{ '=>'{', ' }'=>'}', '} '=>'}', ' ['=>'[', '[ '=>'[', ' ]'=>']', '] '=>']', ' ;'=>';', '; '=>';', ' :'=>':', ': '=>':'
	//' ='=>'=', '= '=>'=', ' \''=>'\'', '\' '=>'\'', ' "'=>'"', '" '=>'"', ' ,'=>',', ', '=>','
);
$pregreplace[ '.css' ]= array(
	"/\/\*(.*)\*\//sU" => "",
	"/[\s]{2,}/" => " ",
	"/[\s]*([\(\){\}\[\];:])[\s]*/" => '${1}',
	"/[\s]*([,>])[\s]*/" => '${1}',
	"/([^0-9])0px/" => '${1}0',
	"/;}/" => '',
);
$pregreplace[ '.js' ]= array(
	"/\/\/(.*)$/mU" => "",
	"/\/\*(.*)\*\//sU" => "",
	"/[\s]{2,}/" => " ",
	"/[\s]*([\(\){\}\[\];:])[\s]*/" => '${1}',
	"/[\s]*([,>])[\s]*/" => '${1}',
	"/[\s]*([='\"+])[\s]*/" => '${1}',
);
//============================================================================
if( true )
{
	$slash= ( substr( ( $file ? $file : $files ), 0, 1 ) == "/" ? false : true );
	$root= rtrim( MODX_BASE_PATH, "/\\" ) . ( $slash ? '/' : '' );
	if( $file )
	{
		$filetype= substr( $file, strrpos( $file, '.' ) );
		$file_to= substr( $file, 0, strrpos( $file, '.' ) ) .'.compress'. $filetype;
		$filesarray[]= $file;
		if( ! file_exists( $root . $file_to ) || filemtime( $root . $file ) > filemtime( $root . $file_to ) ) $refresh= true;
	}else{
		$filetype= substr( $files, strrpos( $files, '.' ) );
		$file_to= ( $tofile ? $tofile : 'all.compress'.$filetype );
		$tmp1= explode( ';', $files );
		foreach( $tmp1 AS $row1 )
		{
			$tmp2= explode( ':', trim( $row1 ) );
			if( count( $tmp2 ) == 1 )
			{
				$filepath= trim( $row1 );
				$filesarray[]= $filepath;
				if( ! file_exists( $root . $file_to ) || filemtime( $root . $filepath ) > filemtime( $root . $file_to ) ) $refresh= true;
			}else{
				$tmp3= explode( ',', $tmp2[ 1 ] );
				foreach( $tmp3 AS $row3 )
				{
					$filepath= $tmp2[ 0 ] . trim( $row3 );
					$filesarray[]= $tmp2[ 0 ] . trim( $row3 );
					if( ! file_exists( $root . $file_to ) || filemtime( $root . $filepath ) > filemtime( $root . $file_to ) ) $refresh= true;
				}
			}
		}
	}
	if( isset( $strtr[ $filetype ] ) ) $strtr_type= $strtr[ $filetype ];
	if( isset( $pregreplace[ $filetype ] ) ) $pregreplace_type= $pregreplace[ $filetype ];
}
//============================================================================
$refresh= ( $refresh || ! empty( $r ) ? true : false );
if( $refresh && $filesarray )
{
	$file_to_handle= fopen( $root . $file_to, 'w' );
	fwrite( $file_to_handle, "/*" );
	foreach( $filesarray AS $filerow )
	{
		fwrite( $file_to_handle, "\t".$filerow."\n" );
	}
	fwrite( $file_to_handle, "*/\n\n" );
	foreach( $filesarray AS $filerow )
	{
		$filecontent= "";
		$file_handle= fopen( $root . $filerow, 'r' );
		if( $file_handle )
		{
			while( ! feof( $file_handle ) ) $filecontent .= fread( $file_handle, 1024*64 );
			fclose( $file_handle );
			if( $filecontent )
			{
				if( $compress !== 'false' )
				{
					if( $pregreplace_type )
					{
						foreach( $pregreplace_type AS $pattern => $replacement )
							$filecontent= preg_replace( $pattern, $replacement, $filecontent );
					}
					if( $strtr_type ) $filecontent= strtr( $filecontent, $strtr_type );
				}
				fwrite( $file_to_handle, "/* {$filerow} */\n".$filecontent."\n\n" );
			}
		}
	}
	fclose( $file_to_handle );
}
//============================================================================
return $file_to;
?>
