<?php
// 6.1 ver.
//==========================================================================
	
	// idslist - только эти документы
	// slice - срез итогового массива документов - функция array_slice();
	
// id = 1,2,5,255
	// type = this | childs | all
	// depth = 0 | 3 | 1,2,4 : 4-макс.уровень, а 1 и 2 игнорируются
	// fields = 'pagetitle,content'
	// tvfields = 'image,price'
	// sort = 'field DESC, field_2 ASC'
	// tpl = 0 | 10 | 6,-7,8
	// isf
	// param
	// query
	// limit
	// clone - Клонировать в категории (ID TV параметра) значение параметра: ,345,123,56,
	// publ
	// del

// ВЫЧЛЕНЕНИЕ ДОКУМЕНТОВ
	//function getdoc50( $ids='0,1', $type='childs', $depth=0, $fields='', $tvfields='', $sort='isfolder DESC, menuindex', $tpl=0, $isf='all', $param='', $query='', $limit=0, $clone='' )
	//{
	
	$table1= 'site_content';
	$table2= 'site_tmplvars';
	$table3= 'site_tmplvar_contentvalues';
	
	$docs= explode( ',', $ids );
	
	if( count( $docs ) > 0 )
	{
		$type= ( $type && ( $type == 'this' || $type == 'all' ) ? $type : 'childs' );
		
		if( ! empty( $fields ) )
		{
			$arr_fields= explode( ',', $fields );
		}
		
		if( ! empty( $tvfields ) )
		{
			$tvfields= explode( ',', $tvfields );
			$tvfields_flag= true;
			foreach( $tvfields AS $val )
			{
				if( $qq_tvfields != "" ) $qq_tvfields .= " OR ";
				$qq_tvfields .= "tv.`name`='{$val}'";
			}
			if( $qq_tvfields ) $qq_tvfields= "AND ( {$qq_tvfields} )";
		}
			
		if( $tpl )
		{
			$flag_tpl= ( strstr( $tpl, "-" ) ? false : true );
			$tpl= trim( $tpl, "-" );
			$arr_tpl= explode( ',', $tpl );
			foreach( $arr_tpl AS $key => $val )
			{
				if( $val == 0 )
				{
					$qq_tpl= "";
					break 1;
				}else{
					if( $qq_tpl != '' ) $qq_tpl .= ( $flag_tpl ? " OR " : " AND " );
					$qq_tpl .= "template ".( $flag_tpl ? "=" : "<>" )." {$val}";
				}
			}
			if( $qq_tpl ) $qq_tpl= "AND ( {$qq_tpl} )";
		}
		
		if( empty( $isf ) ) $isf= "0";
		if( $isf != 'all' )
		{
			$qq_isf= "AND isfolder=". ( $isf ? "1" : "0" );
		}
		
		$qq_published= "AND published=";
		if( $publ == '0' ) $qq_published .= "0"; else $qq_published .= "1";
		if( $publ == 'all' ) $qq_published= "";
		
		$qq_deleted= "AND deleted=";
		if( $del == '1' ) $qq_deleted .= "1"; else $qq_deleted .= "0";
		if( $del == 'all' ) $qq_deleted= "";
		
		$query= ( ! empty( $query ) ? "AND ". $query : "" );
		
		if( $type != 'this' )
		{
			if( $depth )
			{
				$depths= explode( ',', $depth );
				
				if( count( $depths ) >= 2 )
				{
					$maxlvl= $depths[ count( $depths )-1 ];
					foreach( $depths AS $key => $val ) if( $key != count( $depths )-1 ) $ignore_lvl[ $val ]= true;
					
				}elseif( count( $depths ) == 1 ){
					$maxlvl= $depth;
				}
			}
			if( ! $maxlvl ) $maxlvl= 999;
			
			$qq_sort= ( ! empty( $sort ) ? "ORDER BY ". $sort : "" );
			
			$qq_limit= ( ! empty( $limit ) ? "LIMIT ". $limit : "" );
			
			if( $slice ) $slice= explode( ",", $slice );
		}
		
		
//======================================================
		
		foreach( $docs AS $row )
		{
			if( ! $ignore_lvl[ 0 ] )
			{
				$ids_for_result[ $row ]= array( $row, 0, true );
			}
			$ids_for_check[ $row ]= array( $row, 0, true );
			
			if( $type != 'this' )
			{
				if( ! empty( $clone ) )
				{
					$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( $table3 ) ." WHERE tmplvarid={$clone} AND `value` LIKE '%,{$row},%'" );
					if( $rr && mysql_num_rows( $rr ) > 0 )
					{
						while( $cln= mysql_fetch_assoc( $rr ) )
						{
							$clones[ $cln[ 'contentid' ] ]= $cln[ 'contentid' ];
						}
					}
				}
			}
		}
		
		$idslist= explode( ',', $idslist );
		$qq_onlyids= "";
		if( ! empty( $idslist ) )
		{
			foreach( $idslist AS $val )
			{
				if( $val ) $qq_onlyids .= ( $qq_onlyids ? " OR " : "AND ( " ) ."id={$val}";
			}
			if( ! empty( $qq_onlyids ) ) $qq_onlyids .= " )";
		}
		
		if( $type != 'this' )
		{
			while( count( $ids_for_check ) > 0 )
			{
				$row= array_shift( $ids_for_check );
				$rr= mysql_query( "SELECT id FROM ". $modx->getFullTableName( $table1 ) ." WHERE parent={$row[0]} AND isfolder=1 {$qq_published} {$qq_deleted}" );
				if( $rr && mysql_num_rows( $rr ) > 0 )
				{
					$lvlii= $row[ 1 ] + 1;
					for( $kk=0; $kk<mysql_num_rows( $rr ); $kk++ )
					{
						if( $lvlii <= $maxlvl )
						{
							$ids_for_result[ mysql_result( $rr, $kk, 'id' ) ]= array( mysql_result( $rr, $kk, 'id' ), $lvlii );
							$ids_for_check[ mysql_result( $rr, $kk, 'id' ) ]= array( mysql_result( $rr, $kk, 'id' ), $lvlii );
						}
					}
				}
			}
		}
		
		if( count( $ids_for_result ) > 0 )
		{
			foreach( $ids_for_result AS $key => $val )
			{
				$lvlii= $val[ 1 ];
				$lvliii= $lvlii + 1;
				
				$tmp1= ( ! $ignore_lvl[ $lvlii ] && $lvlii <= $maxlvl && ( $type != 'childs' || ! $val[ 2 ] ) ? "id={$key}" : "" );
				$tmp2= ( ! $ignore_lvl[ $lvliii ] && $lvliii <= $maxlvl && ( ! $isf || $isf == 'all' ) ? ( $tmp1 ? " OR " : "" ) ."parent={$key}" : "" );
				
				if( $tmp1 || $tmp2 )
				{
					if( $qq_ids != '' ) $qq_ids .= " OR ";
					$qq_ids .= "( {$tmp1}{$tmp2} )";
				}
			}
		}
		
		if( ! empty( $clones ) )
		{
			foreach( $clones AS $cln )
			{
				$qq_ids .= ( ! empty( $qq_ids ) ? " OR " : "" ) ."( id={$cln} )";
			}
		}
		
		$qq= "SELECT id".( $fields ? ",".$fields : "" )." FROM ". $modx->getFullTableName( $table1 ) ."
			WHERE ( {$qq_ids} ) {$qq_onlyids} {$qq_tpl} {$qq_isf} {$query} {$qq_published} {$qq_deleted}
				{$qq_sort} {$qq_limit}";
		
		$rr= mysql_query( $qq );
		$qq_ids= "";
		if( $rr )
		{
			while( $row= mysql_fetch_assoc( $rr ) )
			{
				$itogo[ $row[ 'id' ] ]= $row;
				
				if( $tvfields_flag )
				{
					if( $qq_ids != "" ) $qq_ids .= " OR ";
					$qq_ids .= "tvc.contentid={$row[id]}";
				}
			}
		}
		
		if( $tvfields_flag && ! empty( $itogo ) )
		{
			if( $qq_ids ) $qq_ids= "AND ( {$qq_ids} )";
			
			$rr= mysql_query( "SELECT id, name, default_text FROM ". $modx->getFullTableName( $table2 ) ." AS tv WHERE 1=1 {$qq_tvfields}" );
			if( $rr )
			{
				while( $row= mysql_fetch_assoc( $rr ) )
				{
					$tvdefault[ $row[ 'name' ] ]= $row[ 'default_text' ];
				}
			}
			
			$rr= mysql_query( "SELECT tv.default_text,tv.`name`,tvc.contentid,tvc.`value` FROM ". $modx->getFullTableName( $table2 ) ." AS tv, ". $modx->getFullTableName( $table3 ) ." AS tvc
				WHERE tv.id=tvc.tmplvarid {$qq_tvfields} {$qq_ids} ORDER BY tv.id" );
			if( $rr )
			{
				while( $row= mysql_fetch_assoc( $rr ) )
				{
					$itogo[ $row[ 'contentid' ] ][ $row[ 'name' ] ]= $row[ 'value' ];
				}
			}
			
			foreach( $itogo AS $key => $val )
			{
				foreach( $tvfields AS $key2 => $val2 )
				{
					if( ! isset( $val[ $val2 ] ) || empty( $val[ $val2 ] ) )
						$itogo[ $key ][ $val2 ]= $tvdefault[ $val2 ];
				}
			}
		}
		
		if( $slice )
		{
			$itogo= array_slice( $itogo, $slice[ 0 ], ( $slice[ 1 ] ? $slice[ 1 ] : null ) );
		}
	}
	
	return $itogo;
//}


	// СОРТИРОВКА ПО TV-параметрам
	/*if( $docs2 )
	{
		foreach( $docs2 AS $row )
		{
			$tmp= intval( $row[ 'price' ] );
			$tmp= str_pad( $tmp, 10, '0', STR_PAD_LEFT );
			$sortirovka[ $tmp .'__'. $row[ 'pagetitle' ] .'__'. $row[ 'id' ] ]= $row;
		}
		ksort( $sortirovka );
		$docs2= array_slice( $sortirovka, $page_s, $MaxItemsInPage );
	}*/
	// СОРТИРОВКА ПО TV-параметрам
?>
