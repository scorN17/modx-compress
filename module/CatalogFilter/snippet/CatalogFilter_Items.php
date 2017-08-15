<?php
/**
 * CatalogFilter_Items
 *
 * Список отфильтрованных ресурсов
 *
 * @version 7.2
 * @date    15.08.2017
 *
 *
 *
 *
 *
 *
 *
 *
 */

$tv_price= 7;
	
	
	
if( ! $_x) return array('',0);


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



$price_vals= false;

$qq= "";
$pr= explode('/', $_x);
foreach($pr AS $val)
{
	$prvals= explode('-', $val);
	$prid= intval(array_shift($prvals));
	
	if(is_array($prvals) && count($prvals))
	{
		if($interval[$prid] == 'price')
		{
			$price_ot= intval($prvals[0]);
			$price_do= intval($prvals[1]);

		}elseif($interval[$prid] == 'interval'){
			$interval_ot= intval($prvals[0]);
			$interval_do= intval($prvals[1]);
			if($interval_ot || $interval_do)
			{
				if($qq) $qq .= " OR ";
				
				$qq .= "( cf_id={$prid} AND ( ";
				
				if($interval_ot) $qq .= "'{$interval_ot}' <= IF(ROUND(dop)>ROUND(`value`), ROUND(dop), ROUND(`value`))";
				
				if($interval_ot && $interval_do) $qq .= " AND ";
				
				if($interval_do) $qq .= "ROUND(`value`) <= '{$interval_do}'";
				
				$qq .= " ) )";
			}

		}else{
			$qqq= "";
			foreach($prvals AS $prval) $qqq .= ($qqq ? " OR " : "") ."id=".$prval;
			$qq .= ($qq ? " OR " : "") ."(cf_id={$prid} AND ( {$qqq} ) )";
		}
	}
}



if($qq)
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('_catfilter_value')." WHERE {$qq}");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$vals[$row['cf_id'] ] .= ($vals[$row['cf_id'] ] ? " OR " : "") ."`value`='".$row['value']."'";
		}
	}
}





$ids= false;
if(is_array($vals) && count($vals))
{
	foreach($vals AS $param => $qqqq)
	{
		$qq= "cf_id={$param} AND ( {$qqqq} )";
		$rr= $modx->db->query("SELECT itemid FROM ".$modx->getFullTableName('_catfilter_value')." WHERE {$qq}");
		if( $rr && $modx->db->getRecordCount($rr))
		{
			while($row= $modx->db->getRow($rr))
			{
				$ids[$param][]= $row['itemid'];
			}
		}
	}
}




$first= true;
$tmparr= false;
if($ids)
{
	foreach($ids AS $row)
	{
		if($first)
		{
			$first= false;
			$tmparr= $row;
			continue;
		}
		$tmparr= array_intersect($tmparr, $row);
	}
}
$ids= $tmparr;



if( ! $first && ( ! is_array($ids) || ! count($ids))) return '';



if($price_ot || $price_do)
{
	if( ! is_array($ids) || ! count($ids))
	{
		$foo= $modx->runSnippet('DocLister',array(
			'idType'           => 'parents',
			'parents'          => $id,
			'selectFields'     => 'c.id',
			'depth'            => 10,
			'tpl'              => '@CODE:[+id+],'
		));
		$foo= trim($foo, ',');
		$ids= explode(',', $foo);
	}
	
	$price_ids= array();
	
	$qq= "SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_price} AND ";
	if($price_ot) $qq.= "ROUND(`value`)>='{$price_ot}'";
	if($price_ot && $price_do) $qq .= " AND ";
	if($price_do) $qq.= "ROUND(`value`)<='{$price_do}'";
	
	$rr= $modx->db->query($qq);
	if($rr && $modx->db->getRecordCount($rr))
		while($row= $modx->db->getRow($rr))
			$price_ids[]= $row['contentid'];
	
	if( ! $price_ot)
	{
		$rr= $modx->db->query("SELECT sc.id FROM ".$modx->getFullTableName('site_content')." AS sc
			LEFT JOIN ".$modx->getFullTableName('site_tmplvar_contentvalues')." AS tv ON tv.contentid=sc.id AND tv.tmplvarid={$tv_price}
				WHERE tv.`value` IS NULL");	
		if($rr && $modx->db->getRecordCount($rr))
			while($row= $modx->db->getRow($rr))
				$price_ids[]= $row['id'];
	}

	if(is_array($ids) && count($ids)) $ids= array_intersect($ids, $price_ids);
	else $ids= $price_ids;
}






if(is_array($ids) && count($ids))
{
	foreach($ids AS $val)
		$result .= ($result ? ',' : '') .$val;
}



return $result;
