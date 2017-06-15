<?php
/**
 * data_to_modx
 *
 * Загрузка данных в базу данных сайта
 *
 * 1C:Exchange
 *
 * @version 2.3
 * @date    15.06.2017
 *
 *
 *
 *
 *
 *
 *
 *
 */
	
$modx->db->query("INSERT INTO ".$modx->getFullTableName('_1c_log')." SET dth='".date('Y-m-d-H-i-s')."', log='data_to_modx::start'"); // LOG

$start= microtime(true);

$maxtime= 10;

$catalog_root     = 15;
$catalog_template = 5;
$tv_1cid          = 21;
$tv_ed            = 18;
$tv_images        = 4;
$tv_article       = 11;
$tv_price         = 7;

$sklad      = 'b226f195-dd01-11e2-87df-00224d500ddd';
$pricetype  = '4c82d87b-5028-11e7-8db0-00224d500ddd';



$session_name= session_name();
$session_id= session_id();




$session_id= '10e53d862426fd8acd011497a001cdbc';





$time= $_SESSION['1c']['time'];


$itms= $modx->db->query("SELECT * FROM ". $modx->getFullTableName('_1c_') ." WHERE status='new' AND session='{$session_id}' ORDER BY dt");
if($itms && $modx->db->getRecordCount($itms))
{
	while($itm= $modx->db->getRow($itms))
	{
		//$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET status='to_db', status_dth='".date('Y-m-d-H-i-s')."', status_dt=".time()."
		//		WHERE id={$itm[id]} LIMIT 1");

		$xml= new SimpleXmlIterator(MODX_BASE_PATH.'1c_exchange/xml/'.$itm['file'], null,true);

		$filetype= strpos($itm['file'], 'offers')!==false ? 'offers' : 'import';

		
		
		
		
		
		if($filetype == 'import')
		{
			$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')." SET published=0 WHERE template={$catalog_template} AND id<>{$catalog_root}");
			
			
			$groups= array();
			$childs= $xml->{'Классификатор'}->{'Группы'}->{'Группа'}->{'Группы'}->{'Группа'};
			groups($childs, $groups, false);
		
			if(true)
			{
				foreach($groups AS $row)
				{
					$parent= $row[2] ? $ids[$row[2] ] : $catalog_root;
					$parent= intval($parent);
					if( ! $parent) continue;
					$menuindex[$parent]++;

					$pagetitle= trim($modx->db->escape($row[1]));
					$pagetitle= preg_replace("/([ ]){2,}/", '\1', $pagetitle);

					$id= 0;
					$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content')."
						WHERE pagetitle='{$pagetitle}' AND parent={$parent} LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						if(false)
						{
							$alias= $modx->runSnippet('GenerAlias',array('alias'=>$pagetitle));
							$alias= $modx->db->escape($alias);
						}

						$id= $modx->db->getValue($rr);
						if(true)
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')."
								SET ".($alias?",alias='{$alias}'":"")." template={$catalog_template}, isfolder=1, menuindex={$menuindex[$parent]}, published=1, deleted=0
								WHERE id={$id} LIMIT 1");
						}

					}elseif($rr){
						$alias= $modx->runSnippet('GenerAlias',array('alias'=>$pagetitle));
						$alias= $modx->db->escape($alias);

						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_content')." SET
							pagetitle='{$pagetitle}',
							alias='{$alias}',
							parent={$parent},
							isfolder=1,
							template={$catalog_template},
							menuindex={$menuindex[$parent]},
							published=1,
							deleted=0");
						$id= $modx->db->getInsertId();
					}

					$ids[$row[0] ]= $id;
				}
			}
			
			
			
			
			
			
			
			
			$rows= $xml->{'Каталог'}->{'Товары'}->{'Товар'};
			if($rows->count())
			{
				$rows->rewind();
				$oo= 0;
				while($row= $rows->current())
				{
					$oo++;
					//if($oo>200) break;
					
					$parent= (string)$row->{'Группы'}->{'Ид'};
					$parent= $ids[$parent];
					$parent= intval($parent);
					if( ! $parent) $parent= $catalog_root;
					$menuindex[$parent]++;

					$options= $row->{'ЗначенияРеквизитов'}->{'ЗначениеРеквизита'};
					$prms= array();
					if($options->count())
					{
						$options->rewind();
						while($option= $options->current())
						{
							$prms[(string)$option->{'Наименование'}]= (string)$option->{'Значение'};
							$options->next();
						}
					}
					
					$id1c      = $modx->db->escape((string)$row->{'Ид'});
					$pagetitle = $modx->db->escape(trim((string)$row->{'Наименование'}));
					$art       = $modx->db->escape(trim((string)$row->{'Артикул'}));
					$ed        = $modx->db->escape(trim((string)$row->{'БазоваяЕдиница'}['НаименованиеПолное']));
					$content   = $modx->db->escape(trim((string)$row->{'Описание'}));
					$img       = $modx->db->escape(trim((string)$row->{'Картинка'}));
					$brand     = $modx->db->escape(trim((string)$row->{'Изготовитель'}->{'Наименование'}));
					
					if($ed == 'Штука') $ed= 'шт.';
					
					if($img)
					{
						$rassh= substr($img, strrpos($img,'.'));
						$img= md5($img).$rassh;
						$imgfolder= 'x'.substr($img,0,2);
						$img= 'assets/images/1c/'.$imgfolder.'/'.$img;
					}
					
					$id= 0;
					$rr= $modx->db->query("SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." 
						WHERE tmplvarid={$tv_1cid} AND `value`='{$id1c}' LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						$id= $modx->db->getValue($rr);
						$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')." SET
							pagetitle='{$pagetitle}', parent={$parent}, content='{$content}',
							template={$catalog_template}, isfolder=0, menuindex={$menuindex[$parent]}, published=1, deleted=0 
							WHERE id={$id} LIMIT 1");

					}elseif($rr){
						$alias= $modx->runSnippet('GenerAlias',array('alias'=>$pagetitle));
						$alias= $modx->db->escape($alias);
						
						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_content')." SET
							pagetitle='{$pagetitle}',
							alias='{$alias}',
							parent={$parent},
							content='{$content}',
							isfolder=0,
							template={$catalog_template},
							menuindex={$menuindex[$parent]},
							published=1,
							deleted=0");
						$id= $modx->db->getInsertId();

						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_1cid}, contentid={$id}, `value`='{$id1c}'");
					}

					if($art)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_article} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$art}' WHERE tmplvarid={$tv_article} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_article}, contentid={$id}, `value`='{$art}'");
						}
					}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_article} AND contentid={$id} LIMIT 1");

					if($img)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_images} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$img}' WHERE tmplvarid={$tv_images} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_images}, contentid={$id}, `value`='{$img}'");
						}
					}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_images} AND contentid={$id} LIMIT 1");

					if($ed)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_ed} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$ed}' WHERE tmplvarid={$tv_ed} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_ed}, contentid={$id}, `value`='{$ed}'");
						}
					}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_ed} AND contentid={$id} LIMIT 1");
					
					$ids[$id1c]= $id;
					
					$rows->next();
				}
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		if($filetype == 'offers')
		{
			$rows= $xml->{'ПакетПредложений'}->{'Предложения'}->{'Предложение'};
			if($rows->count())
			{
				$rows->rewind();
				$oo= 0;
				while($row= $rows->current())
				{
					$oo++;
					//if($oo>1) break;
					
					$id1c= $modx->db->escape((string)$row->{'Ид'});
					$id= $ids[$id1c];
					if( ! $id) continue;
					
					$price= 0;
					$prices= $row->{'Цены'}->{'Цена'};
					if($prices && $prices->count())
					{
						$prices->rewind();
						while($foo= $prices->current())
						{
							if((string)$foo->{'ИдТипаЦены'} == $pricetype)
								$price= (string)$foo->{'ЦенаЗаЕдиницу'};
								
							$prices->next();
						}
					}
					$price= intval($price);
					
					$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_price} AND contentid={$id} LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$price}' WHERE tmplvarid={$tv_price} AND contentid={$id} LIMIT 1");
					}elseif($rr){
						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_price}, contentid={$id}, `value`='{$price}'");
					}
					
					$rows->next();
				}
			}
		}
	}
}





function groups($rows, &$groups, $parent)
{
	if($rows && $rows->count())
	{
		$rows->rewind();
		while($row= $rows->current())
		{
			$groups[(string)$row->{'Ид'}]= array(
				(string)$row->{'Ид'},
				(string)$row->{'Наименование'},
				$parent
			);
			
			$childs= $row->{'Группы'}->{'Группа'};
			groups($childs, $groups, (string)$row->{'Ид'});
			
			$rows->next();
		}
	}
}
