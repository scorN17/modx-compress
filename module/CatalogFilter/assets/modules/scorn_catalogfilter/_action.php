<?php

$sm_base= '../assets/modules/scorn_catalogfilter/';
$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];


// =======================================================================
mysql_query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_cat_filter')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ed` varchar(32) CHARACTER SET utf8 NOT NULL,
  `type` tinyint(4) NOT NULL,
  `docs` text CHARACTER SET utf8 NOT NULL,
  `ii` tinyint(4) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;");
mysql_query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_cat_filter_value')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idfilter` int(11) NOT NULL,
  `iddoc` int(11) NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 NOT NULL,
  `dopvalue` varchar(255) CHARACTER SET utf8 NOT NULL,
  `enabled` set('y','n') CHARACTER SET utf8 NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `idfilter` (`idfilter`),
  KEY `iddoc` (`iddoc`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;");
mysql_query("CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName('_cat_filter_values_cache')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL,
  `docid` int(11) NOT NULL,
  `cfid` int(11) NOT NULL,
  `cfvid` int(11) NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 NOT NULL,
  `enabled` set('y','n') CHARACTER SET utf8 NOT NULL DEFAULT 'y',
  `dt` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`),
  KEY `cfid` (`cfid`),
  KEY `docid` (`docid`),
  KEY `cfvid` (`cfvid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;");
// =======================================================================

$catalog_koren= 17;



// AJAX =======================================================================
if( isset( $_GET[ 'ajax' ] ) )
{
	
	exit();
}
// AJAX =======================================================================



if( $_GET[ 'act' ] == 'add' )
{
	mysql_query( "INSERT INTO ". $modx->getFullTableName( '_cat_filter' ) ." SET name='Новое св-во', type=1" );
	
	header( 'location: '. $module_url );
	exit();
}

if( $_GET[ 'act' ] == 'save' )
{
	$propid= intval( $_GET[ 'propid' ] );
	$prop_name= addslashes( trim( $_POST[ 'prop_name' ] ) );
	$prop_ed= addslashes( trim( $_POST[ 'prop_ed' ] ) );
	$prop_type= intval( $_POST[ 'prop_type' ] );
	
	if( $propid > 0 && $prop_type >= 1 )
	{
		mysql_query( "UPDATE ". $modx->getFullTableName( '_cat_filter' ) ." SET name='{$prop_name}', ed='{$prop_ed}', type={$prop_type} WHERE id={$propid} LIMIT 1" );
	}
	
	header( 'location: '. $module_url );
	exit();
}

if( $_GET[ 'act' ] == 'disablabl' )
{
	$propid= intval( $_GET[ 'propid' ] );
	
	if( $propid > 0 )
	{
		$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_cat_filter' ) ." WHERE id={$propid} LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) == 1 )
		{
			$val= ( mysql_result( $rr, 0, 'enabled' ) == 1 ? 0 : 1 );
			mysql_query( "UPDATE ". $modx->getFullTableName( '_cat_filter' ) ." SET enabled={$val} WHERE id={$propid} LIMIT 1" );
		}
	}
	
	header( 'location: '. $module_url );
	exit();
}

if( $_GET[ 'act' ] == 'edit' && $_GET[ 'act2' ] == 'savetree' )
{
	$propid= intval( $_GET[ 'propid' ] );
	$galki= $_POST[ 'galka' ];
	
	$docs= '';
	
	if( $propid > 0 && ! empty( $galki ) )
	{
		foreach( $galki AS $key => $val )
		{
			/*$par= $modx->getDocument( $key, 'parent' );
			if( $galki[ $par[ 'parent' ] ] != $par[ 'parent' ] )
			{
				while( $par[ 'parent' ] != $catalog_koren && $par[ 'parent' ] != 0 )
				{
					$par= $modx->getDocument( $par[ 'parent' ], 'parent' );
					if( $galki[ $par[ 'parent' ] ] == $par[ 'parent' ] )
					{
						continue 2;
					}
				}
			}else{
				continue 1;
			}*/
			//$docs .= ','. $key;
			//galki_to_child( $key, $docs );
			
			$tmp= explode( '-', $key );
			$tmp2= '';
			foreach( $tmp AS $row )
			{
				$tmp2 .= ( ! empty( $tmp2 ) ? '-' : '' ) . $row;
				if( $tmp2 != $key && $galki[ $tmp2 ] == $row ) continue 2;
			}
			$docs .= ','. $row;
		}
	}
	if( ! empty( $docs ) ) $docs .= ',';
	
	mysql_query( "UPDATE ". $modx->getFullTableName( '_cat_filter' ) ." SET docs='{$docs}' WHERE id={$propid} LIMIT 1" );
	
	header( 'location: '. $module_url .'&act=edit&propid='. $propid );
	exit();
}





if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';



function galki_to_child( $id, &$docs )
{
	global $modx;
	
	$cats= $modx->getActiveChildren( $id, 'menuindex', 'ASC', 'id,isfolder' );
	if( ! empty( $cats ) )
	{
		foreach( $cats AS $cat )
		{
			if( $cat[ 'isfolder' ] )
			{
				$docs .= ','. $cat[ 'id' ];
				galki_to_child( $cat[ 'id' ], $docs );
			}
		}
	}
}

function catalogtree( $id, $ids, &$print, $docs, $docinfo, $list='', $checkbox=false )
{
	global $modx;
	
	$ids .= ( ! empty( $ids ) ? '-' : '' ) . $id;
	
	$galka= ( $docs[ $id ] == $id ? true : false );
	
	$print .= '<div class="ctr_item"><input class="'. $list .'" '.( $galka || $checkbox ? 'checked' : '' ).' '.( $checkbox ? 'disabled="disabled"' : '' ).' type="checkbox" name="galka['. $ids .']" value="'. $id .'" /> <span>'. $docinfo[ 'pagetitle' ] .'</span> ('. $id .')</div>';
	
	if( $galka ) return;
	
	$checkbox= ( $galka || $checkbox ? true : false );
	
	$cats= $modx->getActiveChildren( $id, 'menuindex', 'ASC', 'id,pagetitle,isfolder' );
	if( ! empty( $cats ) )
	{
		foreach( $cats AS $cat )
		{
			if( $cat[ 'isfolder' ] )
			{
				$print .= '<div class="ctr_sub ctr_sub_open '.( $galka ? 'ctr_sub_disabled' : '' ).'">';
					catalogtree( $cat[ 'id' ], $ids, $print, $docs, $cat, $list .' doc'. $cat[ 'id' ], $checkbox );
				$print .= '</div>';
			}
		}
	}
}

function catalogtree_______( $id, &$print, $docs, $list='' )
{
	global $modx;
	
	$cats= $modx->getActiveChildren( $id, 'menuindex', 'ASC', 'id,pagetitle,isfolder' );
	if( ! empty( $cats ) )
	{
		foreach( $cats AS $cat )
		{
			if( $cat[ 'isfolder' ] )
			{
				$print .= '<div class="ctr_item"><input class="'. $list .'" '.( $docs[ $cat[ 'id' ] ] == $cat[ 'id' ] ? 'checked' : '' ).' type="checkbox" name="galka['. $cat[ 'id' ] .']" value="'. $cat[ 'id' ] .'" /> '. $cat[ 'pagetitle' ] .' ('. $cat[ 'id' ] .')</div>';
				
				$print .= '<div class="ctr_sub">';
					catalogtree( $cat[ 'id' ], $print, $docs, $list .' doc'. $cat[ 'id' ] );
				$print .= '</div>';
			}
		}
	}
}
?>
<div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />
<script type="text/javascript" src="//yandex.st/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/jquery-ui/1.10.4/jquery-ui.min.js"></script>


<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>">Главная</a></li>
		<li><a href="<?= $module_url ?>&act=add">Добавить свойство</a></li>
	</ul>
	<div class="clr">&nbsp;</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	var $= jQuery.noConflict();
	$( '.ctr_item >span' ).click(function(){
		var elem= $( '>.ctr_sub', $( this ).parent().parent() );
		if( elem.hasClass( 'ctr_sub_open' ) )
		{
			elem.removeClass( 'ctr_sub_open' );
		}else{
			elem.addClass( 'ctr_sub_open' );
		}
	});
});
</script>


<div class="propslist">
<?php if( $_GET[ 'act' ] == 'edit' ){

	$propid= intval( $_GET[ 'propid' ] );
	
	$prop= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_cat_filter' ) ." WHERE id={$propid} LIMIT 1" );
	if( $prop && mysql_num_rows( $prop ) > 0 )
	{
		$prop= mysql_fetch_assoc( $prop );
	}
	
	$print .= '<h1>'. $prop[ 'id' ] .'. '. $prop[ 'name' ] .'</h1>';
	
	$propdocs= explode( ",", $prop[ 'docs' ] );
	if( ! empty( $propdocs ) )
	{
		foreach( $propdocs AS $val )
		{
			$docs[ $val ]= $val;
		}
	}
	
	$print .= '<form action="'. $module_url .'&act=edit&act2=savetree&propid='. $propid .'" method="post">';
		$print .= '<div class="catalogtree">';
			//catalogtree( $catalog_koren, $print, $docs );
			catalogtree( $catalog_koren, '', $print, $docs, array( 'pagetitle'=>'Весь каталог' ) );
		$print .= '</div>';
		$print .= '<br /><br /><input type="submit" value="Сохранить привязки" />';
	$print .= '</form>';

	print $print;






//=====================================================================================================================
	}else{
	$props= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_cat_filter' ) ."" );
	if( $props && mysql_num_rows( $props ) > 0 )
	{
		while( $prop= mysql_fetch_assoc( $props ) )
		{
			$print .= '<div class="propitem '.( $prop[ 'enabled' ] ? '' : 'disabled' ).'"><form action="'. $module_url .'&act=save&propid='. $prop[ 'id' ] .'" method="post">
				'. $prop[ 'id' ] .'. <input type="text" name="prop_name" value="'. $prop[ 'name' ] .'" /> &nbsp;
				<input type="text" name="prop_ed" value="'. $prop[ 'ed' ] .'" />
				<br /><br />Тип свойства: <select name="prop_type">
					<option '.( $prop[ 'type' ] == 1 ? 'selected' : '' ).' value="1">Значение</option>
					<option '.( $prop[ 'type' ] == 3 ? 'selected' : '' ).' value="3">Несколько значений</option>
					<option '.( $prop[ 'type' ] == 2 ? 'selected' : '' ).' value="2">Цена от до</option>
					<option '.( $prop[ 'type' ] == 4 ? 'selected' : '' ).' value="4">Значение от до</option>
				</select><br /><br />
				<input type="submit" value="Сохранить" /> &nbsp;
				<a style="color:#1f69c6;" href="'. $module_url .'&act=edit&propid='. $prop[ 'id' ] .'">Изменить привязки »</a> &nbsp; &nbsp;
				<a style="color:#1f69c6;" href="'. $module_url .'&act=disablabl&propid='. $prop[ 'id' ] .'">'.( $prop[ 'enabled' ] ? 'Выключить' : 'Включить' ).' свойство</a>
			</form></div>';
		}
		
		$print .= '<div class="clr">&nbsp;</div>';
	}

	print $print;
}?>
</div>



<?php



function clearCache()
{
	global $modx;
	
	$modx->clearCache();
	
	include_once MODX_BASE_PATH . 'manager/processors/cache_sync.class.processor.php';
	$sync= new synccache();
	$sync->setCachepath( MODX_BASE_PATH . "assets/cache/" );
	$sync->setReport( false );
	$sync->emptyCache();
}

/*
*/
?>
