<?php
//v02
//=====================================================================================
$sm_base= '../assets/modules/scorn_page_contacts/';
$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];

mysql_query( "CREATE TABLE IF NOT EXISTS ".$modx->getFullTableName( 'page_contacts' )." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `left` varchar(255) NOT NULL,
  `right` text NOT NULL,
  `br` enum('0','1') NOT NULL DEFAULT '0',
  `index` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );




if( $_GET[ 'act' ] == 'addnewitem' )
{
	mysql_query( "INSERT INTO ".$modx->getFullTableName( 'page_contacts' )." SET `right`='Новая строка'" );
	header( 'location: '. $module_url );
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
				mysql_query( "DELETE FROM ".$modx->getFullTableName( 'page_contacts' )." WHERE id={$key} LIMIT 1" );
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
					WHERE id={$key} LIMIT 1" );
		}
	}
	header( 'location: '. $module_url );
	exit();
}




$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( 'page_contacts' )." ORDER BY `index`" );
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


<form action="<?= $module_url ?>" method="post">
<?= $print ?>
<div style="padding:20px 0px;"><button style="font-size:20px;" type="submit" name="save">Сохранить изменения</button></div>
</form>
<div style="padding:20px 0px;"><a style="font-size:20px;" href="<?= $module_url ?>&act=addnewitem">+ Добавить строку</a></div>


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
?>