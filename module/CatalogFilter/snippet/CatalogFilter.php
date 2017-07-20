<?php
/**
 * CatalogFilter
 *
 * Левый блок фильтра
 *
 * @version 7.1
 * @date    20.07.2017
 *
 *
 *
 *
 *
 *
 *
 *
 */
$id= $modx->documentIdentifier;

$tv_price= 7;
$price_prm_id= 1;



$propsvalues= array();

$cats= $modx->runSnippet('DocLister',array('api'=>true, 'idType'=>'parents', 'parents'=>$id, 'selectFields'=>'id', 'depth'=>5, 'showParent'=>'1', 'addWhereList'=>"c.isfolder='1'"));
$cats= json_decode($cats, true);
if(is_array($cats) && count($cats))
{
	foreach($cats AS $row)
	{
		$catsqq .= ( ! empty($catsqq) ? ' OR ' : '') ."folderid={$row[id]}";
		$catsqq2 .= ( ! empty($catsqq2) ? ' OR ' : '') ."sc.parent={$row[id]}";
	}
}
if($catsqq)
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')." WHERE ( {$catsqq} ) AND e='y' GROUP BY cf_id, `value`,dop ORDER BY cf_id, `value`,dop");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$propsvalues[$row['cf_id'] ][]= $row;
			
			if( ! $min[$row['cf_id'] ]) $min[$row['cf_id'] ]= 9999999;
			
			if($row['value'] < $min[$row['cf_id'] ]) $min[$row['cf_id'] ]= $row['value'];
			if($row['dop'] > $max[$row['cf_id'] ]) $max[$row['cf_id'] ]= $row['dop'];
			if($row['value'] > $max[$row['cf_id'] ]) $max[$row['cf_id'] ]= $row['value'];
		}
	}
	
	
	
	
	$min[$price_prm_id]= 9999999;
	$max[$price_prm_id]= 0;
	
	$rr= $modx->db->query("SELECT tv.`value` FROM ".$modx->getFullTableName('site_content')." AS sc
		INNER JOIN ".$modx->getFullTableName('site_tmplvar_contentvalues')." AS tv ON tv.contentid=sc.id AND tv.tmplvarid={$tv_price}
			WHERE ( {$catsqq2} )");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			if($row['value'] < $min[$price_prm_id]) $min[$price_prm_id]= $row['value'];
			if($row['value'] > $max[$price_prm_id]) $max[$price_prm_id]= $row['value'];
		}
	}
}



/*$docs= $modx->runSnippet('DocLister',array('api'=>true, 'idType'=>'parents', 'parents'=>$id, 'selectFields'=>'id', 'depth'=>5));
$docs= json_decode($docs, true);
if(is_array($docs) && count($docs))
{
	foreach($docs AS $row)
	{
		$docsqq .= ( ! empty($docsqq) ? ' OR ' : '') ."itemid={$row[id]}";
	}
}*/




$props= $modx->runSnippet('CatalogFilter_Props', array('id'=>$id));



if(is_array($props) && count($props))
{
	foreach($props AS $row)
	{
		if($row['type'] == 'price' || $row['type'] == 'interval')
		{
			$interval[$row['cfid'] ]= $row['type'];
		}
	}
}



if(is_array($props) && count($props))
{
	$_x= explode('/', $modx->urlXParams);
	if($_x)
	{
		foreach($_x AS $val)
		{
			$prvals= explode('-', $val);
			$prid= intval(array_shift($prvals));

			if(is_array($prvals) && count($prvals))
			{
				if($interval[$prid])
				{
					$propssel[$prid][0]= intval($prvals[0]);
					$propssel[$prid][1]= intval($prvals[1]);
					if($propssel[$prid][1] == 999999) $propssel[$prid][1]= '';
					
				}else{
					foreach($prvals AS $prval) $propssel[$prid][$prval]= true;
				}
			}
		}
	}
	
	
	
	
	
	foreach($props AS $row)
	{
		if($row['type'] == 'price' || $row['type'] == 'interval')
		{
			if( ! $propssel[$row['cfid'] ][0]) $propssel[$row['cfid'] ][0]= 0;
			if( ! $propssel[$row['cfid'] ][1]) $propssel[$row['cfid'] ][1]= $max[$row['cfid'] ];
			
			if( ! $max[$row['cfid'] ] || $max[$row['cfid'] ] <= $min[$row['cfid'] ]) continue;
			
			$print .= '<div class="cf_box catalogfilter__param">
					<div class="cf_tit font2">'.$row['name'] . ($row['ed'] ? ', '.$row['ed'] : '').'</div>';
			$print .= '<div class="cf_itms cf_itms_interval">';
			
				$print .= '<div class="cf_interval cf_interval_ot"><input type="text" class="catalogfilter__val catalogfilter__val_'.$row['cfid'].'_ot" data-tp="'.$row['type'].'"
						data-nm="'.$row['cfid'].'" data-vl="ot" value="'.$propssel[$row['cfid'] ][0].'" /></div>';
			
				$print .= '<div class="cf_interval cf_interval_do"><input type="text" class="catalogfilter__val catalogfilter__val_'.$row['cfid'].'_do" data-tp="'.$row['type'].'"
						data-nm="'.$row['cfid'].'" data-vl="do" value="'.$propssel[$row['cfid'] ][1].'" /></div>';
			
				$print .= '<br>';
				
				$print .= '<div class="cf_interval_range cf_interval_range_'.$row['cfid'].'"></div>';
				
				$print .= '<script>
					(function($){
						$(document).ready(function(){
							$(".cf_interval_range_'.$row['cfid'].'").slider({
								range: true,
								min: 0,
								max: '.$max[$row['cfid'] ].',
								values: ['.$propssel[$row['cfid'] ][0].', '.$propssel[$row['cfid'] ][1].'],
								//step: 100,
								animate: "slow",
								slide: function(e,ui){
									$(".catalogfilter__val_'.$row['cfid'].'_ot").val(ui.values[0]);
									$(".catalogfilter__val_'.$row['cfid'].'_do").val(ui.values[1]);
								},
								change: function(e,ui){
									catalogFilter();
								}
							});
						});
					})(jQuery);
				</script>';
			
			$print .= '</div></div><!--.cf_box-->' ."\n\n";
		
			
			
			
		}else{
			if(count($propsvalues[$row['cfid'] ]) >= 2)
			{
				$print .= '<div class="cf_box catalogfilter__param">
					<div class="cf_tit font2">'.$row['name'] . ($row['ed'] ? ', '.$row['ed'] : '').'</div>
					<div class="cf_itms">';
				foreach($propsvalues[$row['cfid'] ] AS $propid => $propval)
				{
					$print .= '<div class="cf_itm '.($propssel[$row['cfid' ] ][$propval['cfv_id' ] ] ? 'cf_itm_s' : '').' catalogfilter__val catalogfilter__val_'.$row['cfid'].'"
						data-tp="'.$row['type'].'" data-nm="'.$row['cfid'].'" data-vl="'.$propval['cfv_id'].'"
						data-sel="'.($propssel[$row['cfid'] ][$propval['cfv_id'] ] ? 'y' : 'n').'">[[ico?&n=`checkbox`]][[ico?&n=`checkbox2`]]<span>'.$propval['value'].'</span></div>';
				}
				$print .= '</div></div><!--.cf_box-->' ."\n\n";
			}
		}
	}
	
	
	
	if( ! empty($print))
	{
		$p= '<div class="catalogfilter" data-pageid="'.$id.'" data-url="'.$modx->makeUrl($id).'"><div class="cftit font2">Задайте параметры</div>';
		
		$cats= $modx->runSnippet('DocLister',array('tpl'=>'catalogfilter_catilink', 'idType'=>'parents', 'parents'=>$id, 'depth'=>1, 'selectFields'=>'id,pagetitle',
												   'showParent'=>'1', 'addWhereList'=>"c.isfolder='1' AND c.id!={$id}"));
		if($cats) $p .= '<div class="cf_box"><div class="cf_tit font2">Категория</div><div class="cf_itms">'. $cats .'</div></div><!--.cf_box-->';
		
		$p .= $print;
		
		$p .= '<br></div><!--.catalogfilter-->';
	}
}



return $p;
