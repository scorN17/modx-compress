<?php
/**
 * data_to_modx
 *
 * Загрузка данных в базу данных сайта
 *
 * 1C:Exchange
 *
 * @version 2.8
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
$tv_in_stock      = 22;

$sklad      = 'b226f195-dd01-11e2-87df-00224d500ddd';
$pricetype  = '4c82d87b-5028-11e7-8db0-00224d500ddd';


$session_name= session_name();
$session_id= session_id();


$itms= $modx->db->query("SELECT * FROM ". $modx->getFullTableName('_1c_') ." WHERE status='new' OR status='to_db' ORDER BY IF(type='import', 0, 1), dt DESC LIMIT 1");
if($itms && $modx->db->getRecordCount($itms))
{
	$itm= $modx->db->getRow($itms);
	
	$filetype= $itm['type'];
	$step= $itm['step'];
	$point= $itm['point'];
	
	
	if($itm['status'] == 'new')
	{
		$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET status='to_db', status_dth='".date('Y-m-d-H-i-s')."', status_dt=".time()."
				WHERE id={$itm[id]} LIMIT 1");

		$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET status='cancel', status_dth='".date('Y-m-d-H-i-s')."', status_dt=".time().",
				cancel_dth='".date('Y-m-d-H-i-s')."', cancel_dt=".time()."
				WHERE status='new' AND type='{$filetype}' LIMIT 1");
	}
	
	
	
	$xml= new SimpleXmlIterator(MODX_BASE_PATH.'1c_exchange/xml/'.$itm['file'], null,true);


	

	if($filetype == 'import')
	{
		if($step == 1)
		{
			if( ! $point)
				$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')." SET published=0 WHERE template={$catalog_template} AND id<>{$catalog_root}");

			$groups= array();
			$childs= $xml->{'Классификатор'}->{'Группы'}->{'Группа'}->{'Группы'}->{'Группа'};
			groups($childs, $groups, false);

			
			if(true)
			{
				$oo= 0;
				foreach($groups AS $row)
				{
					$time= microtime(true) - $start;
					if($time > $maxtime)
					{
						$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET point={$oo} WHERE id={$itm[id]} LIMIT 1");
						file_get_contents($modx->makeUrl(78,'','','full'));
						exit();
					}
					
					$oo++;
					
					if($oo <= $point) continue;
					
					
					$id1c= $modx->db->escape(trim($row[0]));
					
					$parent= $row[2] ? $ids[$row[2] ] : $catalog_root;
					$parent= intval($parent);
					if( ! $parent) continue;
					$menuindex[$parent]++;

					$pagetitle= $modx->db->escape(trim($row[1]));
					$pagetitle= preg_replace("/([ ]){2,}/", '\1', $pagetitle);

					$id= 0;
					$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content')."
						WHERE pagetitle='{$pagetitle}' AND parent={$parent} LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						$alias= false;
						if(false)
						{
							$alias= $modx->runSnippet('GenerAlias',array('alias'=>$pagetitle));
							$alias= $modx->db->escape($alias);
						}

						$id= $modx->db->getValue($rr);
						
						if(true)
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')."
								SET isfolder=1, ".($alias?"alias='{$alias}',":"")." template={$catalog_template}, menuindex={$menuindex[$parent]}, published=1, deleted=0
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


					if($id && $id1c)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$id1c}' WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_1cid}, contentid={$id}, `value`='{$id1c}'");
						}
					}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");
					
					
					$ids[$id1c]= $id;
				}
			}
			
			
			$step++;
			$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET step={$step}, point=0 WHERE id={$itm[id]} LIMIT 1");
			file_get_contents($modx->makeUrl(78,'','','full'));
			exit();
		}






		if($step == 2)
		{
			$rows= $xml->{'Каталог'}->{'Товары'}->{'Товар'};
			if($rows->count())
			{
				$rows->rewind();
				$oo= 0;
				while($row= $rows->current())
				{
					$time= microtime(true) - $start;
					if($time > $maxtime)
					{
						$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET point={$oo} WHERE id={$itm[id]} LIMIT 1");
						file_get_contents($modx->makeUrl(78,'','','full'));
						exit();
					}
					
					$oo++;
					
					if($oo <= $point)
					{
						$rows->next();
						continue;
					}
					
					
					
					$parent= (string)$row->{'Группы'}->{'Ид'};
					if( ! $ids[$parent])
					{
						$rr= $modx->db->query("SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." 
							WHERE tmplvarid={$tv_1cid} AND `value`='{$parent}' LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$ids[$parent]= $modx->db->getValue($rr);
						}
					}
					$parent= $ids[$parent];
					if( ! $parent) $parent= $catalog_root;
					$menuindex[$parent]++;
					
					
					
					$content= '';

					$options= $row->{'ЗначенияРеквизитов'}->{'ЗначениеРеквизита'};
					$prms= array();
					if($options->count())
					{
						$options->rewind();
						while($option= $options->current())
						{
							if(stripos((string)$option->{'Наименование'}, 'HTML') !== false)
								$content .= (string)$option->{'Значение'};
							else
								$prms[(string)$option->{'Наименование'}]= (string)$option->{'Значение'};
							
							$options->next();
						}
					}
					
					$content= str_replace("\r",'',$content);
					$content= trim($content);
					$content= '<p>'. str_replace("\n",'</p><p>',$content) .'</p>';
					$content= $modx->db->escape($content);
					

					$id1c      = $modx->db->escape((string)$row->{'Ид'});
					$pagetitle = $modx->db->escape(trim((string)$row->{'Наименование'}));
					$art       = $modx->db->escape(trim((string)$row->{'Артикул'}));
					$ed        = $modx->db->escape(trim((string)$row->{'БазоваяЕдиница'}['НаименованиеПолное']));
					$brand     = $modx->db->escape(trim((string)$row->{'Изготовитель'}->{'Наименование'}));

					
					$fps= trim((string)$row->{'Описание'});
					$fps= str_replace("\r",'',$fps);
					$fps= explode('Параметры фильтра:', $fps);
					$fps= trim($fps[1]);
					
					
					if($brand)
					{
						$fps= "Изготовитель:\n". $brand ."\n\n" . $fps;
						$fps= trim($fps);
					}

					
					if($ed == 'Штука') $ed= 'шт.';

					$imgs= $row->{'Картинка'};
					$imgs_txt= '';
					if($imgs->count())
					{
						$imgs->rewind();
						while($img= (string)$imgs->current())
						{
							$rassh= substr($img, strrpos($img,'.'));
							$img= md5($img) .$rassh;
							$imgfolder= 'x'.substr($img,0,2);
							$img= 'assets/images/1c/'.$imgfolder.'/'.$img;
							$imgs_txt .= ( ! empty($imgs_txt) ? '||' : '') .$img;
							$imgs->next();
						}
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
					
					
					$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value')." SET e='n' WHERE itemid='{$id}'");
					if($fps)
					{
						$fps= explode("\n\n", $fps);
						foreach($fps AS $fp)
						{
							$fp= explode("\n", $fp);
							$fpname= array_shift($fp);
							$fpname= $modx->db->escape(trim($fpname, ':'));
							$fpname= trim($fpname);
							
							$fp_type= $fp[0] && $fp[1] ? 'values' : 'value';
							
							if( ! $fps_db[$fpname])
							{
								$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('_catfilter')." 
										WHERE name='{$fpname}' AND type<>'price' LIMIT 1");
								if($rr && $modx->db->getRecordCount($rr))
								{
									if( ! $fp_a[$fpname])
									{
										$fp_ii++;
										$fp_a[$fpname]= true;
										$fps_db[$fpname]= $modx->db->getValue($rr);
										$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter')." SET type='{$fp_type}', folders=',{$catalog_root},', i={$fp_ii}, e='y'
											WHERE name='{$fpname}' LIMIT 1");
									}
									
								}else{
									$fp_ii++;
									$fp_a[$fpname]= true;
									$modx->db->query("INSERT INTO ".$modx->getFullTableName('_catfilter')." SET
										type='{$fp_type}', name='{$fpname}', folders=',{$catalog_root},', i={$fp_ii}, e='y'");
									$fps_db[$fpname]= $modx->db->getInsertId();
								}
							}
							$fpid= $fps_db[$fpname];
							
							if(is_array($fp) && count($fp))
							{
								foreach($fp AS $fp_i)
								{
									$fp_i= $modx->db->escape(trim($fp_i));
									
									$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('_catfilter_value')." 
											WHERE cf_id='{$fpid}' AND itemid='{$id}' AND `value`='{$fp_i}' LIMIT 1");
									if($rr && $modx->db->getRecordCount($rr))
									{
										$modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value')." SET e='y'
											WHERE cf_id='{$fpid}' AND itemid='{$id}' AND `value`='{$fp_i}' LIMIT 1");
									}else{
										$modx->db->query("INSERT INTO ".$modx->getFullTableName('_catfilter_value')." SET
											cf_id='{$fpid}', itemid='{$id}', `value`='{$fp_i}'");
									}
								}
							}
						}
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

					if($imgs_txt)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_images} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$imgs_txt}' WHERE tmplvarid={$tv_images} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_images}, contentid={$id}, `value`='{$imgs_txt}'");
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

					if($id1c)
					{
						$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");
						if($rr && $modx->db->getRecordCount($rr))
						{
							$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							`value`='{$id1c}' WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");
						}elseif($rr){
							$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
							tmplvarid={$tv_1cid}, contentid={$id}, `value`='{$id1c}'");
						}
					}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_1cid} AND contentid={$id} LIMIT 1");

					
					$ids[$id1c]= $id;
					
					$rows->next();
				}
			}
			
			
			$step++;
			$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET status='complete', status_dth='".date('Y-m-d-H-i-s')."', status_dt=".time().",
				step={$step}, point=0 WHERE id={$itm[id]} LIMIT 1");
			file_get_contents($modx->makeUrl(78,'','','full'));
			exit();
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
				$time= microtime(true) - $start;
				if($time > $maxtime)
				{
					$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET point={$oo} WHERE id={$itm[id]} LIMIT 1");
					file_get_contents($modx->makeUrl(78,'','','full'));
					exit();
				}

				$oo++;

				if($oo <= $point)
				{
					$rows->next();
					continue;
				}
				
				

				$id1c= $modx->db->escape((string)$row->{'Ид'});
				if( ! $ids[$id1c])
				{
					$rr= $modx->db->query("SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." 
							WHERE tmplvarid={$tv_1cid} AND `value`='{$id1c}' LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						$ids[$id1c]= $modx->db->getValue($rr);
					}
				}
				$id= $ids[$id1c];
				if( ! $id) continue;
				
				$instock= intval($row->{'Количество'});

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

				if($price)
				{
					$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_price} AND contentid={$id} LIMIT 1");
					if($rr && $modx->db->getRecordCount($rr))
					{
						$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
								`value`='{$price}' WHERE tmplvarid={$tv_price} AND contentid={$id} LIMIT 1");
					}elseif($rr){
						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
								tmplvarid={$tv_price}, contentid={$id}, `value`='{$price}'");
					}
				}else $modx->db->query("DELETE FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
						WHERE tmplvarid={$tv_price} AND contentid={$id} LIMIT 1");

				$rr= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
					WHERE tmplvarid={$tv_in_stock} AND contentid={$id} LIMIT 1");
				if($rr && $modx->db->getRecordCount($rr))
				{
					$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
								`value`='{$instock}' WHERE tmplvarid={$tv_in_stock} AND contentid={$id} LIMIT 1");
				}elseif($rr){
					$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
								tmplvarid={$tv_in_stock}, contentid={$id}, `value`='{$instock}'");
				}

				$rows->next();
			}
		}
			
			
		$step++;
		$modx->db->query("UPDATE ". $modx->getFullTableName('_1c_') ." SET status='complete', status_dth='".date('Y-m-d-H-i-s')."', status_dt=".time().",
				step={$step}, point=0 WHERE id={$itm[id]} LIMIT 1");
		file_get_contents($modx->makeUrl(78,'','','full'));
		exit();
	}
}




print "success\n";

$modx->db->query("INSERT INTO ".$modx->getFullTableName('_1c_log')." SET dth='".date('Y-m-d-H-i-s')."', log='data_to_modx::finish'"); // LOG




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
