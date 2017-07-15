/**
 * CF_Values_Cache
 *
 * Кэширование значений фильтра каталога
 *
 * @version   10.0
 * @date      17.05.2017
 * @events    OnBeforeCacheUpdate
 *
 * @dependence
 * SNIPPET DocLister
 */

$catalog_root= 15;

//----------------------------------------------------

$cats= $modx->runSnippet('DocLister',array('api'=>true, 'parents'=>$catalog_root, 'selectFields'=>'id,parent,isfolder', 'depth'=>5, 'addWhereList'=>"c.isfolder='1'", 'showParent'=>'1'));
$cats= json_decode($cats, true);

$list= $cats;
if(is_array($cats) && count($cats))
{
	// Не удалять - включите если
	// нужно чтобы кэшировались только позиции конечных категорий
	//foreach($cats AS $row) if(isset($cats[$row['parent'] ])) unset($list[$row['parent'] ]);
}

if(is_array($list) && count($list))
{
	foreach($list AS $row)
	{
		$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')." WHERE folderid={$row[id]} AND e='y' LIMIT 1");
		if($rr && $modx->db->getRecordCount($rr)==0)
		{
			$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')." WHERE folderid={$row[id]} LIMIT 1");
			if($rr && $modx->db->getRecordCount($rr)==0)
			{
				$modx->db->query("INSERT INTO ".$modx->getFullTableName('_catfilter_value_cache')." SET folderid={$row[id]}");
			}elseif($rr){
				$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value_cache')." SET e='y' WHERE folderid={$row[id]} LIMIT 1");
			}
		}
	}	
}


$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')."
	WHERE ".time()."-dt>".(60*2)." AND e='y' GROUP BY folderid LIMIT 100");
if($rr && $modx->db->getRecordCount($rr))
{
	$bar= false;
	$tmparray_2= array();
	while($row= $modx->db->getRow($rr))
	{
		if( ! $bar) $bar= $row['id'];

		if($tmparray_2[$row['folderid'] ]) continue;
		$tmparray_2[$row['folderid'] ]= true;
		
		$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value_cache')." SET e='n' WHERE folderid={$row[folderid]}");
		
		$tmparray= array();
		
		$items= $modx->runSnippet('DocLister',array('api'=>true, 'parents'=>$row['folderid'], 'selectFields'=>'id', 'depth'=>1));
		$items= json_decode($items, true);
		if(is_array($items) && count($items))
		{
			foreach($items AS $item)
			{
				$rr2= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value')." WHERE itemid={$item[id]}");
				if($rr2 && $modx->db->getRecordCount($rr2))
				{
					while($row2= $modx->db->getRow($rr2))
					{
						$row2['value']= $modx->db->escape(trim($row2['value']));
						$row2['dop']= $modx->db->escape(trim($row2['dop']));
						if( ! $row2['value']) continue;
						if( $tmparray[ $row[ 'folderid' ] ][ $row2[ 'cf_id' ] ][ $row2[ 'value' ].'-'.$row2['dop'] ] ) continue;
						$tmparray[ $row[ 'folderid' ] ][ $row2[ 'cf_id' ] ][ $row2[ 'value' ].'-'.$row2['dop'] ]= true;
						
						
						
						$rr3= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')."
							WHERE folderid={$row[folderid]} AND (cf_id=0 OR (cf_id={$row2[cf_id]} AND `value`='{$row2[value]}' AND dop='{$row2[dop]}'))
								ORDER BY cf_id LIMIT 1");
						if($rr3 && $modx->db->getRecordCount($rr3))
						{
							$foo= $modx->db->getRow($rr3);
							
							$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value_cache')."
								SET ".($foo['cf_id']=='0' ? "cf_id={$row2[cf_id]}, `value`='{$row2[value]}', dop='{$row2[dop]}'," : "" )."
									itemid={$row2[itemid]}, cfv_id={$row2[id]}, dt=".time().", e='y'
										WHERE id={$foo['id']} LIMIT 1");
						}elseif($rr3){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('_catfilter_value_cache')." SET
								itemid={$row2[itemid]}
								, dt=".time()."
								, folderid={$row[folderid]}
								, cfv_id={$row2[id]}
								, cf_id={$row2[cf_id]}
								, `value`='{$row2[value]}'
								, dop='{$row2[dop]}'
								");
						}
					}
				}
			}
		}else{
			$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value_cache')." SET dt=".time()." WHERE id={$bar} LIMIT 1");
			continue;
		}
	}
}
