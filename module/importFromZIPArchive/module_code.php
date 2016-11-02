<?php

$sm_base= '../assets/modules/scorn_import_from_archive/';
$module_url= MODX_MANAGER_URL.'?a='.$_GET['a'].'&id='.$_GET['id'];


$catalog_koren= 3;
$catalog_template= 2;

$catalog_tv_price= 4;
$catalog_tv_img= 1;
$catalog_tv_art= 17;
$catalog_tv_rnd= 19;
$catalog_tv_krasnodar= 18;

$catalog_prm_brand= 1;
$catalog_prm_coll= 2;
$catalog_prm_sex= 3;
$catalog_prm_material= 4;
$catalog_prm_mehan= 5;
$catalog_prm_steklo= 6;
$catalog_prm_kreplenie= 7;
$catalog_prm_water= 8;

$notfound= 78;
$catalog_images= 'assets/images/catalog/';


function tolog($log)
{
	global $modx;
	//mysql_query("INSERT INTO ".$modx->getFullTableName('_zip_import_log')." SET dth='".date('Y-m-d-H-i-s')."', log='".addslashes($log)."'");
	// print $log."<br />\n";
}



// print md5_file(MODX_BASE_PATH.'box/_tmp');
// print "\n----------";
// print md5_file(MODX_BASE_PATH.'box/original.zip');


if($_GET['act'] == 'ajaxfileupload')
{
	if( ! file_exists(MODX_BASE_PATH.'box/')) mkdir(MODX_BASE_PATH.'box/', 0777, true);
	
	$data_length= intval($_POST['data_length']);
	$pos= intval($_POST['pos']);
	$max_chunk_size= intval($_POST['max_chunk_size']);
	$chunk= $_POST['chunk'];
	$length= strlen($chunk);
	if(strpos($chunk,',') !== false) $chunk= substr($chunk,strpos($chunk,',')+1);
	$chunk= base64_decode($chunk);
	$length2= strlen($chunk);
	$fo= fopen(MODX_BASE_PATH.'box/_tmp', 'a');
	flock($fo, LOCK_EX);
	if($pos<=$max_chunk_size) ftruncate($fo, 0);
	fwrite($fo, $chunk);
	
	sleep(2);
	
	if($pos>=$data_length) $last= true; else $last= false;
	
	tolog('pos: '.$pos);
	tolog('length: '.$length);
	tolog('length2: '.$length2);
	tolog('max_chunk_size: '.$max_chunk_size);
	tolog('data_length: '.$data_length);
	tolog('last: '.($last?'y':'n'));
	
	
	if($last)
	{
		print 'finish';
	}else{
		print 'process';
		// print "\n";
		// print date('H-i-s');
		exit();
	}
	
	fclose($fo);
}

if($last || $_GET['act'] == 'test')
{
	tolog('import:: start');
	
	$resultok= '';
	
	$import_folder= 'box/'.date('Y-m').'/'.date('d-H-i-s');
	if( ! file_exists(MODX_BASE_PATH.$import_folder.'/ext/')) mkdir(MODX_BASE_PATH.$import_folder.'/ext/', 0777, true);
	$file_path= $import_folder.'/_'.date('Y-m-d-H-i-s').'.zip';
	
	if($_GET['act'] == 'test')
	{
		$file_path= 'box/ars2.zip';
		
	}else{
		tolog('import:: 01');
		$copyrr= copy(MODX_BASE_PATH.'box/_tmp', MODX_BASE_PATH.$file_path);
		tolog('import:: 02');
	}
	
	if(true)
	{
		tolog('upload:: ok');
		
		$zip= new ZipArchive();
		if($zip)
		{
			tolog('ZIP:: ok');
			$zip_open_result= $zip->open(MODX_BASE_PATH.$file_path);
			if($zip_open_result === true)
			{
				tolog('ZIP:: open:: ok');
				$fileslist= array();
				zip_extract_to(MODX_BASE_PATH.$file_path, MODX_BASE_PATH.$import_folder.'/ext/', $zip, $fileslist);

				if($fileslist)
				{
					tolog( 'ZIP:: extractTo:: ok' );
//==============================================================================================================
if(true)
{
	$fo= fopen(MODX_BASE_PATH.$import_folder.'/ext/'.$fileslist['txt'], 'r');
	if($fo)
	{
		tolog( 'TXT:: open:: ok' );
		while( ! feof($fo)) $content .= fread($fo, 1024*64);
		fclose($fo);
		if($content)
		{
			tolog( 'TXT:: content:: ok' );
			$content= explode("\r\n", $content);
			if(is_array($content) && count($content)>0)
			{
				foreach($content AS $key => $row)
				{
//					$row= iconv('CP1251', 'UTF-8//TRANSLIT//IGNORE', $row);
					$row= explode(';', $row);
					if(is_array($row) && count($row)==13)
					{
						$brandid= false;
						$docid= false;
						$rr= mysql_query("SELECT contentid FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid='{$catalog_tv_art}' AND `value`='".addslashes($row[1])."' LIMIT 1");
//						$rr= mysql_query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE pagetitle='".addslashes($row[2])."' LIMIT 1");
						if($rr && mysql_num_rows($rr)==1)
						{
							$docid= mysql_result($rr, 0, 'contentid');
//							$docid= mysql_result($rr, 0, 'id');
							$rr2= mysql_query("SELECT parent FROM ".$modx->getFullTableName('site_content')." WHERE id={$docid} LIMIT 1");
							if($rr2 && mysql_num_rows($rr2) == 1) $brandid= mysql_result($rr2, 0, 'parent');
							
							if( ! $docslist_unpublished[$brandid])
							{
								$docslist_unpublished[$brandid]= true;
								mysql_query("UPDATE ".$modx->getFullTableName('site_content')." SET published=0 WHERE parent={$brandid}");
							}
							
							mysql_query("UPDATE ".$modx->getFullTableName('site_content')." SET published=1, deleted=0, pagetitle='".addslashes($row[2])."' WHERE id={$docid} LIMIT 1");
							
						}elseif($rr){
							$rr= mysql_query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE pagetitle='".addslashes($row[0])."' AND parent={$catalog_koren} LIMIT 1");
							if($rr && mysql_num_rows($rr) == 1)
							{
								$brandid= mysql_result($rr, 0, 'id');
								mysql_query("UPDATE ".$modx->getFullTableName('site_content')." SET isfolder=1 WHERE id={$docid} LIMIT 1");
								
							}elseif($rr){
								if( ! $menuindex[$catalog_koren])
								{
									$rr2= mysql_query("SELECT MAX(menuindex) AS max FROM ".$modx->getFullTableName('site_content')." WHERE parent={$catalog_koren}");
									if($rr2 && mysql_num_rows($rr2) == 1) $menuindex[$catalog_koren]= mysql_result($rr2, 0, 'max');
								}
								$menuindex[$catalog_koren] ++;
								$alias= $modx->runSnippet('GenerAlias', array('txt'=>$row[0]));
								mysql_query("INSERT INTO ".$modx->getFullTableName('site_content')." SET
									template='{$catalog_template}',
									published='1',
									parent='{$catalog_koren}',
									pagetitle='".addslashes($row[0])."',
									isfolder=1,
									alias='{$alias}',
									menuindex='".$menuindex[$catalog_koren]."'
								");
								$brandid= mysql_insert_id();
							}
							if($brandid)
							{							
								if( ! $docslist_unpublished[$brandid])
								{
									$docslist_unpublished[$brandid]= true;
									mysql_query("UPDATE ".$modx->getFullTableName('site_content')." SET published=0 WHERE parent={$brandid}");
								}
								
								if( ! $menuindex[$brandid])
								{
									$rr2= mysql_query("SELECT MAX(menuindex) AS max FROM ".$modx->getFullTableName('site_content')." WHERE parent={$brandid}");
									if($rr2 && mysql_num_rows($rr2) == 1) $menuindex[$brandid]= mysql_result($rr2, 0, 'max');
								}
								$menuindex[$brandid] ++;
								$alias= $modx->runSnippet('GenerAlias', array('txt'=>$row[1]));
								mysql_query("INSERT INTO ".$modx->getFullTableName('site_content')." SET
									template='{$catalog_template}',
									published='1',
									parent='{$brandid}',
									pagetitle='".addslashes($row[2])."',
									isfolder=0,
									alias='{$alias}',
									menuindex='".$menuindex[$brandid]."'
								");
								$docid= mysql_insert_id();
							}
						}
						
						if($brandid && $docid)
						{
							if( ! file_exists(MODX_BASE_PATH.$catalog_images.'/'.$brandid.'/')) mkdir(MODX_BASE_PATH.$catalog_images.'/'.$brandid.'/', 0777, true);
							
							if($fileslist['img'][$row[1]][1])
							{
								$img= $catalog_images.'/'.$brandid.'/'.$row[1].$fileslist['img'][$row[1]][2];
								$img_flag= copy(MODX_BASE_PATH.$import_folder.'/ext/'.$fileslist['img'][$row[1]][1], MODX_BASE_PATH.$img);
								if($img_flag) tv_insert_update($catalog_tv_img, $docid, addslashes($img));
							}
							
							tv_insert_update($catalog_tv_art, $docid, addslashes($row[1]));
							tv_insert_update($catalog_tv_price, $docid, addslashes($row[10]));
							tv_insert_update($catalog_tv_rnd, $docid, addslashes($row[11]));
							tv_insert_update($catalog_tv_krasnodar, $docid, addslashes($row[12]));
							
							prm_insert_update($catalog_prm_brand, $docid, addslashes($row[0]));
							prm_insert_update($catalog_prm_coll, $docid, addslashes($row[3]));
							prm_insert_update($catalog_prm_sex, $docid, addslashes($row[4]));
							prm_insert_update($catalog_prm_material, $docid, addslashes($row[5]));
							prm_insert_update($catalog_prm_mehan, $docid, addslashes($row[6]));
							prm_insert_update($catalog_prm_steklo, $docid, addslashes($row[7]));
							prm_insert_update($catalog_prm_kreplenie, $docid, addslashes($row[8]));
							prm_insert_update($catalog_prm_water, $docid, addslashes($row[9]));
						}
						
					}else tolog( 'TXT:: item:: '.$key.':: error:: 01' );
				}
			}
		}
	}
}
//==============================================================================================================
//					header('location: '.$module_url.'&ok');
//					exit();
				}
				$zip->close();
			}else tolog('ZIP:: open:: error:: '.$zip_open_result);
		}
		if($_GET['act'] != 'test' && file_exists(MODX_BASE_PATH.$file_path)) unlink(MODX_BASE_PATH.$file_path);
	}
	
	exit();
}

	

function prm_insert_update($prm, $docid, $value)
{
	global $modx;
	$rr2= mysql_query("SELECT id FROM ".$modx->getFullTableName('_cat_filter_value')." WHERE idfilter='{$prm}' AND iddoc='{$docid}' LIMIT 1");
	if( $rr2 && mysql_num_rows( $rr2 ) == 1 )
	{
		mysql_query("UPDATE ".$modx->getFullTableName('_cat_filter_value')." SET `value`='{$value}', enabled='y' WHERE id=".mysql_result($rr2, 0, 'id')." LIMIT 1");
	}elseif( $rr2 ){
		mysql_query("INSERT INTO ".$modx->getFullTableName('_cat_filter_value')." SET idfilter='{$prm}', iddoc='{$docid}', `value`='{$value}', enabled='y'");
	}
}


	

function tv_insert_update( $tmplvarid, $contentid, $value )
{
	global $modx;
	$rr2= mysql_query( "SELECT id FROM ".$modx->getFullTableName( 'site_tmplvar_contentvalues' )." WHERE tmplvarid='{$tmplvarid}' AND contentid='{$contentid}' LIMIT 1" );
	if( $rr2 && mysql_num_rows( $rr2 ) == 1 )
	{
		mysql_query( "UPDATE ".$modx->getFullTableName( 'site_tmplvar_contentvalues' )." SET `value`='{$value}' WHERE id=". mysql_result( $rr2, 0, 'id' ) ." LIMIT 1" );
	}elseif( $rr2 ){
		mysql_query( "INSERT INTO ".$modx->getFullTableName( 'site_tmplvar_contentvalues' )." SET tmplvarid='{$tmplvarid}', contentid='{$contentid}', `value`='{$value}'" );
	}
}
function tv_append( $tmplvarid, $contentid, $value )
{
	global $modx;
	mysql_query( "UPDATE ".$modx->getFullTableName( 'site_tmplvar_contentvalues' )." SET `value`=CONCAT(`value`,'||{$value}') WHERE tmplvarid='{$tmplvarid}' AND contentid='{$contentid}' LIMIT 1" );
}




function zip_extract_to($zip_file, $base, &$zip, &$fileslist)
{
	for($ii=0; $ii<$zip->numFiles; $ii++)
	{
		$zip_path= $zip->getNameIndex($ii);
		$zip_path_i= iconv('CP866', 'UTF-8//TRANSLIT//IGNORE', $zip_path);
		if(substr($zip_path_i, -1) != '/')
		{
			$rassh= substr($zip_path_i, strrpos($zip_path_i, '.'));
			$filename= md5($zip_path_i) . $rassh;
			$art= substr($zip_path_i, strrpos($zip_path_i, '/')+1);
			$art= str_replace($rassh, '', $art);
			copy('zip://'.$zip_file.'#'.$zip_path, $base.'/'.$filename);
			if($rassh == '.txt')
			{
				$fileslist['txt']= $filename;
			}else{
				$fileslist['img'][$art]= array($zip_path_i, $filename, $rassh);
			}
		}
	}
}



if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';

?><div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?= $sm_base;?>_styles.css" />
<script type="text/javascript" src="//yastatic.net/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="//yastatic.net/jquery-ui/1.11.2/jquery-ui.min.js"></script>

<script type="text/javascript">
(function($){
	var max_chunk_size= 1024*1024*8;
	//max_chunk_size= 1024*100;
	
	var ii= 0;
	
	$(document).ready(function(){
		$('#form').submit(function(){
			$('.info').css({ color:'#0044c5' }).text('Загрузка ... Ожидайте!');
			
			var reader= new FileReader();
			var file= $('#file',this).get(0).files.item(0);
			var data_length= file.size;
			var pos= 0;
			
			fileChunkSend();
			
			function fileChunkSend()
			{
				if(pos>=data_length) return;
				var chunk_size= max_chunk_size;
				//if(pos+chunk_size>data_length) chunk_size= data_length-pos;
				blob= file.slice(pos, pos+chunk_size);
				reader.readAsDataURL(blob);
				pos += chunk_size;
			}

			reader.onload= function(e){
				var data= e.target.result;
				var fd= new FormData;
				fd.append('chunk', data);
				fd.append('pos', pos);
				fd.append('data_length', data_length);
				fd.append('max_chunk_size', max_chunk_size);
				//fd.append('last', (pos+max_chunk_size>=data_length?'y':'n'));
				
				$.ajaxSetup({ async:false });
				$.ajax({
					url: '<?= $module_url ?>&act=ajaxfileupload&size='+data_length+'&pos='+pos+'&chunk='+max_chunk_size,
					type: 'POST',
					data: fd,
					processData: false,
					contentType: false
				}).done(function(data){
					fileChunkSend();
					if(data=='finish')
						$('.info').css({ color:'#43ae01' }).text('Загрузка завершена!');
				});
			};
			
			return false;
		});
	});
})(jQuery);
</script>

<?php
if( $_GET[ 'act' ] == 'import' && $error ) print '<div class="importerrors">'. $error .'</div>';
if( isset( $_GET[ 'ok' ] ) ) print '<div class="importoks"><p>Импорт прошел успешно!</p></div>';
if( $resultok ) print '<div class="importoks_2"><table width="100%"><tr><td>Категория</td><td>Артикул</td><td>Наименование</td><td>Цена</td><td>Результат</td></tr>'. $resultok .'</table></div>';
?>

	<form id="form" action="<?= $module_url ?>" method="post" enctype="multipart/form-data">
		<p>Выберите ZIP-архив: <input id="file" type="file" name="file" accept1=".zip" /></p>
		<p><button type="submit">Импортировать</button></p>
	</form>
	
	<br />
	<div class="info"></div>
	
	<!--
	<br />
	<br />
	<div><a href="<?= $module_url ?>&act=test">ТЕСТ</a></div>-->
	
	
	<br />
	<br />
	<br />
	<p>В ZIP-архиве должен быть TXT-файл и папка "images" !<p>





<?php


/*
*/
