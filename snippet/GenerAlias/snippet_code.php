<?php
//v03
//GenerAlias
//==================================================================================
$trans = array("а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e",
        "ё"=>"jo", "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"jj", "к"=>"k", "л"=>"l",
        "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u",
        "ф"=>"f", "х"=>"kh", "ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ы"=>"y",
        "э"=>"eh", "ю"=>"yu", "я"=>"ya", "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g",
        "Д"=>"d", "Е"=>"e", "Ё"=>"jo", "Ж"=>"zh", "З"=>"z", "И"=>"i", "Й"=>"jj",
        "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s",
        "Т"=>"t", "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh",
        "Щ"=>"shh", "Ы"=>"y", "Э"=>"eh", "Ю"=>"yu", "Я"=>"ya", " "=>"-", "."=>"-",
        ","=>"-", "_"=>"-", "+"=>"-", ":"=>"-", ";"=>"-", "!"=>"-", "?"=>"-");
		
	$alias= addslashes( $txt );
	$alias= strip_tags( strtr( $alias, $trans ) );
	$alias= preg_replace( '/&.+?;/', '', $alias );
	$alias= strtolower($alias);
	$alias= preg_replace( "/[^a-zA-Z0-9-]/", "", $alias );
	$alias= preg_replace( '/([-]){2,}/', '\1', $alias );
	$alias= trim( $alias, '-' );
	
	if( strlen( $alias ) > 20 )
	{
		$alias= trim( substr( $alias, 0, 20 ), "-" );
	}
	
	do{
		$rr= mysql_query( "SELECT id FROM ". $modx->getFullTableName( 'site_content' ) ." WHERE alias='{$alias}' LIMIT 1" );
		
		if( $rr && mysql_num_rows( $rr ) == 1 ) $alias .= rand( 1, 9 );
		
	}while( ( $rr && mysql_num_rows( $rr ) == 1 ) || ! $rr );
	
	if( ! $rr ) $alias= false;
	
	return $alias;
?>
