<?php
/**
 * CATALOG
 *
 * Каталог
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
	
$catalogroot= 15; // Корень каталога

$itemid_prefix= 'i_';

$items_in_page= 20; // Пагинация
$paginate_prefix= 'page_';

$selectFields= 'c.id,pagetitle,isfolder,content'; // Для DocLister
$tvList= 'article,descript,new,images,price,discount,ed'; // Для DocLister

//-----------------------------------------------------------------------------------

if( ! $id) $id= $modx->documentIdentifier;
if($id==$catalogroot) return;

$item= $modx->getDocument($id,'isfolder,parent');

if( ! $item['isfolder'])
{
	$pp_items= '<div class="catalog_page">';
	
	$pp_items .= $modx->runSnippet('DocLister',array('idType'=>'documents', 'documents'=>$id, 'selectFields'=>$selectFields, 'tvList'=>$tvList,
											   'tpl'=>'catalog_itemTpl_Page',
											   'prepare'=>'catalogItemPrepare',
											  ));
	
	$pp_items .= '<br></div><!--.catalog_page-->';
	
	
// ---------------------------------------------------------------------------------------------------------------------

}else{
	$idType= 'parents';
	$ids= $id;
	$page= 1;
	
	if($modx->urlXParams) $_x= $modx->urlXParams;
	if($_x)
	{
		$param_id= 0;
		
		$_xe= $_x;
		$_xp= $_x;
		
		if(strpos($_x, $paginate_prefix) !== false)
		{
			$page= false;
			$_xp= substr($_xp, 0, strrpos($_xp, '/'.$paginate_prefix));
		}
		
		$_xe= explode('/', $_x);
		
		foreach($_xe AS $key => $row)
		{
			if( ! $page && $key==count($_xe)-1)
			{
				if(strpos($row, $paginate_prefix) === 0)
					$page= intval(substr($row, strlen($paginate_prefix)));
					else $page404= true;
				break;
			}
			
			$row= explode('-', $row);
			$foo= intval(array_shift($row));
			if($foo <= $param_id)
			{
				$page404= true;
				break;
			}else{
				$param_id= $foo;
				
				$rr= $modx->db->query("SELECT type FROM ".$modx->getFullTableName('_catfilter')." WHERE id={$param_id} LIMIT 1");
				if($rr && $modx->db->getRecordCount($rr))
				{
					$rr= $modx->db->getRow($rr);
					if($rr['type'] == 'price' || $rr['type'] == 'interval') continue;
				}
				
				$foo= 0;
				if(is_array($row) && count($row))
				{
					foreach($row AS $k => $p)
					{
						if( ! $k) continue;
						$p= intval($p);
						if($p <= $foo)
						{
							$page404= true;
							break 2;
						}else{
							$foo= $p;
						}
					}
				}else $page404= true;
			}
		}
	}
	
	if($page404 || ! $page) $modx->sendErrorPage();
	
	if($_xp)
	{
		$ids= $modx->runSnippet('CatalogFilter_Items', array('id'=>$id, '_x'=>$_xp));
		if($ids) $idType= 'documents';
	}
	
	$pp_pages_sort = $modx->makeUrl($id);
	$pp_pages_sort .= ($_xp || $page>=2 ? 'x/' : '');
	$pp_pages_sort .= ($_xp ? $_xp.'/' : '');
	$pp_pages_sort .= ($page>=2 ? 'page_'.$page.'/' : '');	
	
	
	
	
	
	$sort= array(
		'pagetitle' => ['pagetitle',  'BINARY'],
		'article'   => ['article',    'BINARY'],
		'priceDown' => ['price DESC', 'DECIMAL'],
		'priceUp'   => ['price ASC',  'DECIMAL'],
	);
	if(isset($_GET['sort']))
	{
		$orderby= isset($sort[$_GET['sort'] ]) ? $_GET['sort'] : 'pagetitle';
		$_SESSION['catalog_sort']= $orderby;
	}
	if( ! $_SESSION['catalog_sort']) $_SESSION['catalog_sort']= 'pagetitle';
	$orderby= $_SESSION['catalog_sort'];
	
	$pp_sorts = '<div class="orderby">		
					<p class="sort font2">Сортировка
						<span class="pgn_i"><a href="'.$pp_pages_sort.'?sort=article" class="'.($orderby=='article' ? 'pgn_a' : '').'">Артикул</a></span>
						<span class="pgn_i"><a href="'.$pp_pages_sort.'?sort=pagetitle" class="'.($orderby=='pagetitle' ? 'pgn_a' : '').'">Наименование</a></span>
						<span class="pgn_i"><a href="'.$pp_pages_sort.'?sort='.($orderby=='priceUp' ? 'priceDown' : 'priceUp').'" class="'.($orderby=='priceDown' || $orderby=='priceUp' ? 'pgn_a' : '').'">Цена '.($orderby=='priceDown' ? '↓' : '↑').'</a></span>
						</p><br></div>';
	
	$orderby= $sort[$orderby];
	
	
	
	
	
	
		
	$pp_items= $modx->runSnippet('DocLister',array('idType'=>$idType, $idType=>$ids, 'selectFields'=>$selectFields, 'tvList'=>$tvList,
												   'id'         => 'items',
												   'showParent' => 0,
												   'tpl'        => 'catalog_itemTpl',
												   'prepare'    => 'catalogItemPrepare',
												   'depth'      => 5,
												   'sortType'   => 'other',
												   'orderBy'    => $orderby[0],
												   'tvSortType' => $orderby[1],
												   'offset'     => ($page-1)*$items_in_page,
												   'display'    => $items_in_page,
												   'paginate'   => 'pages',
												  ));
	
	$total_pages= $modx->getPlaceholder('items.totalPages');
	
	if($total_pages>=2)
	{
		$pp_pages= '<div class="pagination"><div class="pgn_i pgn_tit font2">Страницы</div>' ."\n";
		
		//$prev= ($page <= 2 ? false : $page-1);
		//if($prev) $pp_pages .= '<div class="pgn_i pgn_prev font2"><a href="'.$modx->makeUrl($id) .'x/' .($_xp?$_xp.'/':'') .$paginate_prefix.$prev.'/"> < </a></div>';
		
		$visible_ot= $page -3;
		$visible_do= $page +3;
		
		for($ii=1; $ii<=$total_pages; $ii++)
		{
			if($ii >= 3 && $ii < $visible_ot)
			{
				if( ! $foo)
				{
					$pp_pages .= '<div class="pgn_i pgn_toch font2">...</div>' ."\n";
					$foo= true;	
				}
				continue;
			}
			if($ii <= $total_pages-2 && $ii > $visible_do)
			{
				if( ! $bar)
				{
					$pp_pages .= '<div class="pgn_i pgn_toch font2">...</div>' ."\n";
					$bar= true;	
				}
				continue;
			}
						
			$pp_pages .= '<div class="pgn_i pgn_p '.($ii==$page?'pgn_a':'').' font2"><a href="'.$modx->makeUrl($id);
			$pp_pages .= ($_xp || $ii>=2 ? 'x/' : '');
			$pp_pages .= ($_xp ? $_xp.'/' : '');
			$pp_pages .= ($ii>=2 ? 'page_'.$ii.'/' : '');	
			$pp_pages .= '">'. $ii .'</a></div>' ."\n";
		}
		
		//$next= ($page+1 >= $total_pages ? false : $page+1);
		//if($next) $pp_pages .= '<div class="pgn_i pgn_next font2"><a href="'.$modx->makeUrl($id) .'x/' .($_xp?$_xp.'/':'') .$paginate_prefix.$next.'/"> > </a></div>';
		
		$pp_pages .= '<br></div>';
	}
}

$pp .= '<div class="catalog">';
$pp .= $pp_sorts;
$pp .= $pp_pages;
$pp .= '<br>';
$pp .= $pp_items;
$pp .= $pp_pages;
$pp .= '<br></div><!--.catalog-->';

return $pp;
