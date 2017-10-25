/**
 * Экспорт-Импорт
 *
 */

$module= 'delta_export_import';
$page= '/manager/index.php?a='.$_GET['a'].'&id='.$_GET['id'];
$exp= 'assets/modules/'.$module.'/exp/';
$imp= 'assets/modules/'.$module.'/imp/';

if(isset($_POST['exp']))
{
	$root= intval(trim($_POST['exp_root']));

	$docs= array();
	catalogtree($root, $docs);
	$docs= serialize($docs);

	if( ! file_exists(MODX_BASE_PATH.$exp))
		mkdir(MODX_BASE_PATH.$exp, 0777, true);
	$fo= fopen(MODX_BASE_PATH.$exp.date('Y-m-d-H-i-s').'.zip', 'wb');
	if($fo)
	{
		fwrite($fo, $docs);
		fclose($fo);
	}
}

if(isset($_POST['upld']))
{
	$file= $_FILES['upld_file'];

	if( ! file_exists(MODX_BASE_PATH.$imp))
		mkdir(MODX_BASE_PATH.$imp, 0777, true);

	if(is_uploaded_file($file['tmp_name']))
	{
		move_uploaded_file($file['tmp_name'], MODX_BASE_PATH.$imp.$file['name']);
	}
}

if(isset($_POST['imp']) && isset($_GET['file']) && is_file(MODX_BASE_PATH.$imp.$_GET['file']))
{
	$root= intval(trim($_POST['imp_root']));
	
	$file= $_GET['file'];

	$docs= '';
	$fo= fopen(MODX_BASE_PATH.$imp.$file, 'rb');
	if($fo)
	{
		while( ! feof($fo))
			$docs .= fread($fo, 1024*512);
		fclose($fo);
	}
	$docs= unserialize($docs);

	if(is_array($docs))
	{
		$old_root= false;
		
		foreach($docs AS $doc)
		{
			if( ! $old_root) $old_root= $doc['parent'];
			
			$alias= $modx->db->escape($doc['alias']);
			$i= '';
			do{
				$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_content')."
					WHERE alias='".$alias.$i."' LIMIT 1");
				if($rr && $modx->db->getRecordCount($rr))
				   $i++;
			}while($rr && $modx->db->getRecordCount($rr));
			$doc['alias']= $alias.$i;
			
			$doc['pagetitle']= $modx->db->escape($doc['pagetitle']);
			$doc['longtitle']= $modx->db->escape($doc['longtitle']);
			$doc['description']= $modx->db->escape($doc['description']);
			$doc['link_attributes']= $modx->db->escape($doc['link_attributes']);
			$doc['published']= $modx->db->escape($doc['published']);
			$doc['isfolder']= $modx->db->escape($doc['isfolder']);
			$doc['introtext']= $modx->db->escape($doc['introtext']);
			$doc['content']= $modx->db->escape($doc['content']);
			$doc['menuindex']= $modx->db->escape($doc['menuindex']);
			$doc['menutitle']= $modx->db->escape($doc['menutitle']);
			$doc['hidemenu']= $modx->db->escape($doc['hidemenu']);
			
			$doc['template']= intval($_POST['imp_tpl_'.$doc['template'] ]);
			
			$doc['parent']= $doc['parent']==$old_root ? $root : $parents[$doc['parent'] ];
			
			$docid= false;
			
			$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_content')."
				WHERE parent='{$doc[parent]}' AND pagetitle='{$doc[pagetitle]}' LIMIT 1");
			if($rr && $modx->db->getRecordCount($rr))
			{
				$row= $modx->db->getRow($rr);
				$docid= $row['id'];
				
				$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')." SET
					longtitle='{$doc[longtitle]}',
					description='{$doc[description]}',
					alias='{$doc[alias]}',
					link_attributes='{$doc[link_attributes]}',
					published='{$doc[published]}',
					isfolder='{$doc[isfolder]}',
					introtext='{$doc[introtext]}',
					content='{$doc[content]}',
					template='{$doc[template]}',
					menuindex='{$doc[menuindex]}',
					menutitle='{$doc[menutitle]}',
					hidemenu='{$doc[hidemenu]}'
						WHERE id={$docid} LIMIT 1
				");
				
			}elseif($rr){
				$rr= $modx->db->query("INSERT INTO ".$modx->getFullTableName('site_content')." SET
					pagetitle='{$doc[pagetitle]}',
					longtitle='{$doc[longtitle]}',
					description='{$doc[description]}',
					alias='{$doc[alias]}',
					link_attributes='{$doc[link_attributes]}',
					published='{$doc[published]}',
					parent='{$doc[parent]}',
					isfolder='{$doc[isfolder]}',
					introtext='{$doc[introtext]}',
					content='{$doc[content]}',
					template='{$doc[template]}',
					menuindex='{$doc[menuindex]}',
					menutitle='{$doc[menutitle]}',
					hidemenu='{$doc[hidemenu]}'
					");
				if($rr) $docid= $modx->db->getInsertId();
			}
			if( ! $docid) continue;
			
			$parents[$doc['id'] ]= $docid;
			
			if( ! is_array($doc['tv'])) continue;
			
			foreach($doc['tv'] AS $tv)
			{
				$tv['value']= $modx->db->escape($tv['value']);
				
				$tv['tmplvarid']= intval($_POST['imp_tv_'.$tv['tmplvarid'] ]);
				if( ! $tv['tmplvarid']) continue;
				
				$tv['contentid']= $docid;
				
				$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')."
					WHERE tmplvarid='{$tv[tmplvarid]}' AND contentid='{$tv[contentid]}' LIMIT 1");
				if($rr && $modx->db->getRecordCount($rr))
				{
					$modx->db->query("UPDATE ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
						`value`='{$tv[value]}'
						WHERE tmplvarid='{$tv[tmplvarid]}' AND contentid='{$tv[contentid]}' LIMIT 1");
					
				}elseif($rr){
					$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET
						tmplvarid='{$tv[tmplvarid]}',
						contentid='{$tv[contentid]}',
						`value`='$tv[value]'");
				}
			}
		}
	}
}

?>
<style>
	.modulecontent
	{
		font-family: Arial;
		padding: 10px 40px 50px
	}
</style>

<div class="modulecontent">
<h2>Экспорт</h2>
<form action="<?=$page?>" method="post">
	<p><input type="text" name="exp_root" /> - номер ресурса-родителя</p>
	<p><input type="submit" name="exp" value="Экспортировать" /></p>
</form>

<?php
$files= glob(MODX_BASE_PATH.$exp.'*');
if(is_array($files))
{
	foreach($files AS $file)
	{
		$fn= explode('/', $file);
		$fn= array_pop($fn);
		print '<p style="font-size:90%;"><a target="_blank" href="'.MODX_SITE_URL.$exp.$fn.'">'.$fn.'</a></p>';
	}
}
?>



<br><br>
<h2>Загрузка файла</h2>
<form action="<?=$page?>" method="post" enctype="multipart/form-data">
	<p><input type="file" name="upld_file" /></p>
	<p><input type="submit" name="upld" value="Загрузить" /></p>
</form>



<br><br>
<h2>Выберите файл для импорта</h2>
<?php
$files= glob(MODX_BASE_PATH.$imp.'*');
if(is_array($files))
{
	foreach($files AS $file)
	{
		$fn= explode('/', $file);
		$fn= array_pop($fn);
		print '<p style="font-size:90%;"><a href="'.$page.'&file='.$fn.'">'.$fn.'</a></p>';
	}
}
?>



<br><br>
<h2>Импорт</h2>
<?php
if(isset($_GET['file']) && is_file(MODX_BASE_PATH.$imp.$_GET['file']))
{
	$file= $_GET['file'];
	print '<p>Выбран файл для импорта: <b>'.$file.'</b></p>';

	$docs= '';
	$fo= fopen(MODX_BASE_PATH.$imp.$file, 'rb');
	if($fo)
	{
		while( ! feof($fo))
			$docs .= fread($fo, 1024*512);
		fclose($fo);
	}
	$docs= unserialize($docs);

	if(is_array($docs))
	{
		$rr= $modx->db->query("SELECT id, templatename, description
			FROM ".$modx->getFullTableName('site_templates'));
		if($rr && $modx->db->getRecordCount($rr))
			$tpls_select= $modx->db->makeArray($rr);
				else $tpls_select= false;

		$rr= $modx->db->query("SELECT id, `name`, caption
			FROM ".$modx->getFullTableName('site_tmplvars'));
		if($rr && $modx->db->getRecordCount($rr))
			$tvs_select= $modx->db->makeArray($rr);
				else $tvs_select= false;

		foreach($docs AS $doc)
		{
			$tpl[$doc['template']]= array(
				$doc['template'],
				$doc['tpl_n'],
				$doc['tpl_d'],
			);
			if(is_array($doc['tv']))
			{
				foreach($doc['tv'] AS $tvi)
				{
					$tv[$tvi['tmplvarid']]= array(
						$tvi['tmplvarid'],
						$tvi['name'],
						$tvi['caption'],
					);
				}
			}
		}

		print '<form action="'.$page.'&file='.$file.'" method="post">';

		print '<h3>Соответствия шаблонов</h3>';

		if(is_array($tpl))
		{
			print '<table style="border-collapse:collapse;" border="1">';
			foreach($tpl AS $tpli)
			{
				print '<tr>
					<td style="padding:5px;">'.$tpli[0].'. <b>'.$tpli[1].'</b> - '.$tpli[2].'</td>
					<td style="padding:5px;">
						<select name="imp_tpl_'.$tpli[0].'">';
				if($tpls_select)
				{
					foreach($tpls_select AS $row)
					{
						print '<option '.($tpli[1]==$row['templatename'] ? 'selected="selected"' : '').' value="'.$row['id'].'">'.$row['id'].'. '.$row['templatename'].' - '.$row['description'].'</option>';
					}
				}
				print '</select>
					</td>
				</tr>';
			}
			print '</table>';
		}

		print '<h3>Соответствия TV-параметров</h3>';

		if(is_array($tv))
		{
			print '<table style="border-collapse:collapse;" border="1">';
			foreach($tv AS $tvi)
			{
				print '<tr>
					<td style="padding:5px;">'.$tvi[0].'. <b>'.$tvi[1].'</b> - '.$tvi[2].'</td>
					<td style="padding:5px;">
						<select name="imp_tv_'.$tvi[0].'">
							<option value="0">~ не выгружать ~</option>';
				if($tvs_select)
				{
					foreach($tvs_select AS $row)
					{
						print '<option '.($tvi[1]==$row['name'] ? 'selected="selected"' : '').' value="'.$row['id'].'">'.$row['id'].'. '.$row['name'].' - '.$row['caption'].'</option>';
					}
				}
				print '</select>
					</td>
				</tr>';
			}
			print '</table>';
		}

		print '<p><input type="text" name="imp_root" /> - номер ресурса-родителя</p>
			<p><input type="submit" name="imp" value="Импортировать" /></p>
		</form>';
	}
}
?>



</div>



<?php
function catalogtree($id, &$docs)
{
	global $modx;

	$rr= $modx->db->query("SELECT t1.*, t2.templatename AS tpl_n, t2.description AS tpl_d
		FROM ".$modx->getFullTableName('site_content')." AS t1
			INNER JOIN ".$modx->getFullTableName('site_templates')." AS t2
				ON t2.id=t1.template
		WHERE t1.parent={$id}");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$rr2= $modx->db->query("SELECT t1.*, t2.`name`, t2.caption
				FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." AS t1
					INNER JOIN ".$modx->getFullTableName('site_tmplvars')." AS t2
						ON t2.id=t1.tmplvarid
				WHERE t1.contentid={$row[id]}");
			if($rr2 && $modx->db->getRecordCount($rr2))
			{
				while($row2= $modx->db->getRow($rr2))
				{
					$row['tv'][]= $row2;
				}
			}

			$docs[]= $row;

			catalogtree($row['id'], $docs);
		}
	}
}
