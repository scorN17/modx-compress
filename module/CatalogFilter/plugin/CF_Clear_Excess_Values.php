/**
 * CF_Clear_Excess_Values
 *
 * Очистка лишних значений фильтра каталога
 *
 * @version   10.0
 * @date      17.05.2017
 * @events    OnBeforeCacheUpdate
 *
 * @dependence
 */

if( ! $_SESSION['CatalogFilterValuesCleared'])
{
	$_SESSION['CatalogFilterValuesCleared']= true;
	
	print '<br /><br />';
	$rr= $modx->db->query("SELECT itemid FROM ".$modx->getFullTableName('_catfilter_value')." GROUP BY itemid");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$rr2= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE id={$row[itemid]} LIMIT 1");
			if($rr2 && $modx->db->getRecordCount($rr2)==0)
			{
				$modx->db->query("DELETE FROM ".$modx->getFullTableName('_catfilter_value')." WHERE itemid={$row[itemid]}");
				print $row['itemid'].'; ';
				continue;
			}

			$rr2= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE id={$row[itemid]} AND isfolder=1 LIMIT 1");
			if($rr2 && $modx->db->getRecordCount($rr2))
			{
				$modx->db->query("DELETE FROM ".$modx->getFullTableName('_catfilter_value')." WHERE itemid={$row[itemid]}");
				print $row['itemid'].'; ';
			}
		}
	}
	print '<br /><br /><br />';
}
