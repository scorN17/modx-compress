<?php
//v045
//PageContacts
//07.07.2016
//=====================================================================================
$sm_base= '../assets/modules/scorn_page_contacts/';
$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
mysql_query( "CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'page_contacts' )." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `left` varchar(255) NOT NULL,
  `right` text NOT NULL,
  `br` enum('0','1') NOT NULL DEFAULT '0',
  `index` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

$myblock= ( $_GET[ 'block' ] ? intval( $_GET[ 'block' ] ) : 0 );

if( $_GET[ 'act' ] == 'addnewblock' )
{
	$newblock= false;
	$rr= mysql_query( "SELECT block FROM ".$modx->getFullTableName( 'page_contacts' )." ORDER BY block DESC LIMIT 1" );
	if( $rr && mysql_num_rows( $rr ) == 1 )
	{
		$newblock= mysql_result( $rr, 0, 'block' ) + 1;
	}elseif( $rr ){
		$newblock= 0;
	}
	if( $newblock !== false ) mysql_query( "INSERT INTO ".$modx->getFullTableName( 'page_contacts' )." SET block={$newblock}, `right`='Новая строка'" );
	header( 'location: '. $module_url .'&block='. $myblock );
	exit();
}

if( $_GET[ 'act' ] == 'addnewitem' )
{
	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'page_contacts' )." SET block={$myblock}, `right`='Новая строка'" );
	header( 'location: '. $module_url .'&block='. $myblock );
	exit();
}

if( isset( $_POST[ 'save' ] ) )
{
	$type= $_POST[ 'type' ];
	$left= $_POST[ 'left' ];
	$right= $_POST[ 'right' ];
	$br= $_POST[ 'br' ];
	$index= $_POST[ 'index' ];
	$delete= $_POST[ 'delete' ];
	if( $type )
	{
		foreach( $type AS $key => $row )
		{
			if( $delete[ $key ] == 'delete' )
			{
				mysql_query( "DELETE FROM ".$modx->getFullTableName( 'page_contacts' )." WHERE id={$key} AND block={$myblock} LIMIT 1" );
				continue 1;
			}
			
			if( $type[ $key ] != 3 )
			{
				$left[ $key ]= str_replace( "\"", "“", $left[ $key ] );
				$right[ $key ]= str_replace( "\"", "“", $right[ $key ] );
				$left[ $key ]= str_replace( "'", "`", $left[ $key ] );
				$right[ $key ]= str_replace( "'", "`", $right[ $key ] );
			}else{
				
			}
			
			$type[ $key ]= intval( $type[ $key ] );
			$left[ $key ]= addslashes( trim( $left[ $key ] ) );
			$right[ $key ]= addslashes( trim( $right[ $key ] ) );
			$br[ $key ]= ( $br[ $key ] == 'br' ? '1' : '0' );
			$index[ $key ]= intval( $index[ $key ] );
			
			mysql_query( "UPDATE ".$modx->getFullTableName( 'page_contacts' )." SET
				`type`='". $type[ $key ] ."', `left`='". $left[ $key ] ."', `right`='". $right[ $key ] ."', br='". $br[ $key ] ."', `index`='". $index[ $key ] ."'
					WHERE id={$key} AND block={$myblock} LIMIT 1" );
		}
	}
	$modx->clearCache();
	header( 'location: '. $module_url .'&block='. $myblock );
	exit();
}
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( 'page_contacts' )." WHERE block={$myblock} ORDER BY `index`" );
if( $rr && mysql_num_rows( $rr ) > 0 )
{
	while( $row= mysql_fetch_assoc( $rr ) )
	{
		if( $row[ 'type' ] == 3 )
		{
			$row[ 'right' ]= htmlspecialchars( $row[ 'right' ] );
		}
		
		$print .= '<div class="pc_item">
			<div class="pci_type"><select name="type['. $row[ 'id' ] .']">
				<option value="1">Обычная строка</option>
				<option '.( $row[ 'type' ] == 2 ? 'selected="selected"' : '' ).' value="2">Адрес электронной почты</option>
				<option '.( $row[ 'type' ] == 3 ? 'selected="selected"' : '' ).' value="3">Карта</option>
				<option '.( $row[ 'type' ] == 4 ? 'selected="selected"' : '' ).' value="4">Крупный текст</option>
				<option '.( $row[ 'type' ] == 5 ? 'selected="selected"' : '' ).' value="5">Заголовок H3</option>
				<option '.( $row[ 'type' ] == 6 ? 'selected="selected"' : '' ).' value="6">Жирный шрифт</option>
				<option '.( $row[ 'type' ] == 7 ? 'selected="selected"' : '' ).' value="7">Изображение</option>
				<option '.( $row[ 'type' ] == 8 ? 'selected="selected"' : '' ).' value="8">Координаты точки для карты</option>
			</select></div>
			
			<div class="pci_left"><input type="text" name="left['. $row[ 'id' ] .']" value="'. $row[ 'left' ] .'" /></div>
			
			<div class="pci_right">
				<input type="text" name="right['. $row[ 'id' ] .']" value="'. $row[ 'right' ] .'" /><br />
				<label><input type="checkbox" name="br['. $row[ 'id' ] .']" value="br" '.( $row[ 'br' ] == '1' ? 'checked="checked"' : '' ).' /> — отступ после</label>
				'.( $row[ 'br' ] == '1' ? '<p>&nbsp;</p>' : '' ).'
			</div>
			
			<div class="pci_ind"><input type="text" name="index['. $row[ 'id' ] .']" value="'. $row[ 'index' ] .'" /> — порядок</div>
			
			<div class="pci_delete"><input type="checkbox" name="delete['. $row[ 'id' ] .']" value="delete" /> — удалить</div>
			<div class="clr">&nbsp;</div>
		</div>';
	}
}
if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';
?>
<div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />
<script type="text/javascript" src="//yandex.st/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/jquery-ui/1.10.4/jquery-ui.min.js"></script>
	
<div class="topmenu">
	<ul>
		<li class="<?=( $myblock===0 ? 'active' : '' )?>"><a href="<?= $module_url ?>">Основные контакты</a></li>
<?php
	$rr= mysql_query( "SELECT block FROM ".$modx->getFullTableName( 'page_contacts' )." WHERE block>0 GROUP BY block ORDER BY block" );
			   if( $rr && mysql_num_rows( $rr ) > 0 )
			   {
				   while( $row= mysql_fetch_assoc( $rr ) )
				   {
					   print '<li class="'.( $myblock == $row['block'] ? 'active' : '' ).'"><a href="'. $module_url .'&block='. $row['block'] .'">'.( $row['block']+1 ).'</a></li>';
				   }
			   }
?>
		<li><a href="<?= $module_url ?>&act=addnewblock">Добавить блок контактов</a></li>
	</ul>
	<div class="clr">&nbsp;</div>
</div>


<form action="<?= $module_url ?>&block=<?= $myblock ?>" method="post">
<?= $print ?>
<div style="padding:20px 0px;"><button style="font-size:20px;" type="submit" name="save">Сохранить изменения</button></div>
</form>
<div style="padding:20px 0px;"><a style="font-size:20px;" href="<?= $module_url ?>&act=addnewitem&block=<?= $myblock ?>">+ Добавить строку</a></div>

	
<br /><br />
<a target="_blank" href="https://constructor.maps.yandex.ru/location-tool/">Определение координат</a>
<br /><br />
