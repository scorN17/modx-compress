<?php
$id= $modx->documentIdentifier;

$propsvalues= array();

$cats= $modx->runSnippet('DocLister',array('api'=>true, 'idType'=>'parents', 'parents'=>$id, 'selectFields'=>'id', 'depth'=>5, 'showParent'=>'1', 'addWhereList'=>"c.isfolder='1'"));
$cats= json_decode($cats, true);
if(is_array($cats) && count($cats))
{
	foreach($cats AS $row) $catsqq .= ( ! empty($catsqq) ? ' OR ' : '') ."folderid={$row[id]}";
}
if($catsqq)
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value_cache')." WHERE ( {$catsqq} ) AND e='y' GROUP BY cf_id, `value` ORDER BY cf_id, `value`");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$propsvalues[$row['cf_id'] ][]= $row;
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



$znachs= array();
if(is_array($props) && count($props))
{
	foreach($props AS $row)
	{
		if($row['type'] == 'price' || $row['type'] == 'interval')
		{
			$znachs[$row['cf_id'] ]= true;
		}
	}
}



if(is_array($props) && count($props))
{
	$_x= explode('/', $modx->urlXParams );
	if($_x)
	{
		foreach($_x AS $val)
		{
			$foo= explode('-', $val);
			$prid= intval(array_shift($foo));
			if(is_array($foo) && count($foo))
			{
				if($znachs[$prid])
				{
					$propssel[$prid][0]= intval($foo[0]);
					$propssel[$prid][1]= intval($foo[1]);
				}else{
					foreach($foo AS $prval) $propssel[$prid][$prval]= true;
				}
			}
		}
	}
	
	
	
	
	
	foreach($props AS $row)
	{
		if(false && $row['type'] == 'price')
		{
			$print .= '<div class="blf_prm catalogfilter__param">
					<div class="blfp_tit"><span class="famicon">&nbsp;&nbsp;</span><span class="nm">'. $row[ 'name' ] .''.( $row[ 'ed' ] ? '<span><nobr>, '.$row[ 'ed' ].'</nobr></span>' : '' ).'<span></div>';
			$print .= '<div class="blfp_itms">';
				$print .= '<div class="blfp_cena blfp_cenaot">
					<div class="blfp_cena_tit">от</div>
					<div class="blfp_cena_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][0] .'" /></div>
				</div>';
				$print .= '<div class="blfp_cena blfp_cenado">
					<div class="blfp_cena_tit">до</div>
					<div class="blfp_cena_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][1] .'" /></div>
				</div>';
			$print .= '<div class="clr">&nbsp;</div></div></div>';
			
			
		}elseif( $row[ 'type' ] == 4 ){
			$print .= '<div class="blf_prm catalogfilter__param">
					<div class="blfp_tit"><span class="famicon">&nbsp;&nbsp;</span><span class="nm">'. $row[ 'name' ] .''.( $row[ 'ed' ] ? '<span><nobr>, '.$row[ 'ed' ].'</nobr></span>' : '' ).'<span></div>';
			$print .= '<div class="blfp_itms">';
				$print .= '<div class="blfp_znach blfp_znachot">
					<div class="blfp_znach_tit">от</div>
					<div class="blfp_znach_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][0] .'" /></div>
				</div>';
				$print .= '<div class="blfp_znach blfp_znachdo">
					<div class="blfp_znach_tit">до</div>
					<div class="blfp_znach_val"><input type="text" class="catalogfilter__val catalogfilter__val_'. $row[ 'cfid' ] .'" data-tp="'. $row[ 'type' ] .'" data-nm="'. $row[ 'cfid' ] .'" value="'. $propssel[ $row[ 'cfid' ] ][1] .'" /></div>
				</div>';
			$print .= '<div class="clr">&nbsp;</div></div></div>';
		
			
			
			
			
			
		}else{
			if(count($propsvalues[$row['cfid'] ]) >= 2)
			{
				$print .= '<div class="cf_box catalogfilter__param">
					<div class="cf_tit font2">'.$row['name'] . ($row['ed'] ? ', '.$row['ed'] : '').'</div>
					<div class="cf_itms">';
				foreach($propsvalues[$row['cfid'] ] AS $propid => $propval)
				{
					$print .= '<div class="cf_itm '.($propssel[$row['cfid' ] ][$propval['cfv_id' ] ] ? 'cf_itm_s' : '').' catalogfilter__val catalogfilter__val_'.$row['cfid'].'" data-tp="'.$row['type'].'" data-nm="'.$row['cfid'].'" data-vl="'.$propval['cfv_id'].'" data-sel="'.($propssel[$row['cfid'] ][$propval['cfv_id'] ] ? 'y' : 'n').'">[[ico?&n=`checkbox`]][[ico?&n=`checkbox2`]]<span>'.$propval['value'].'</span></div>';
				}
				$print .= '</div></div><!--.cf_box-->' ."\n\n";
			}
		}
	}
	
	
	
	if( ! empty($print))
	{
		$p= '<div class="catalogfilter" data-pageid="'.$id.'" data-url="'.$modx->makeUrl($id).'"><div class="cftit font2">Задайте параметры</div>';
		
		$cats= $modx->runSnippet('DocLister',array('tpl'=>'catalogfilter_catilink', 'idType'=>'parents', 'parents'=>$id, 'depth'=>1, 'selectFields'=>'id,pagetitle', 'showParent'=>'1', 'addWhereList'=>"c.isfolder='1' AND c.id!={$id}"));
		if($cats) $p .= '<div class="cf_box"><div class="cf_tit font2">Категория</div><div class="cf_itms">'. $cats .'</div></div><!--.cf_box-->';
		
		$p .= $print;
		
		$p .= '<br></div><!--.catalogfilter-->';
	}
}



return $p;
