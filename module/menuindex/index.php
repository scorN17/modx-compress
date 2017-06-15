<?php
$module_url= MODX_MANAGER_URL.'?a='.$_GET['a'].'&id='.$_GET['id'];

$docid= intval($_GET['docid']);

if(isset($_POST['save']))
{
	$menuindex= $_POST['menuindex'];
	if(is_array($menuindex) && count($menuindex))
	{
		$ii= 0;
		foreach($menuindex AS $row)
		{
			$ii++;
			$row= intval($row);

			$modx->db->query("UPDATE ".$modx->getFullTableName('site_content')." SET menuindex={$ii} WHERE id={$row} LIMIT 1");
		}
	}
	$modx->clearCache('full');
	header('location: '.$module_url.'&docid='.$docid);
	exit();
}
?>
<div class="module_box">
	<div class="content">
	<?php if($docid){
		$rr= $modx->db->query("SELECT id, pagetitle FROM ".$modx->getFullTableName('site_content')." WHERE parent={$docid} ORDER BY menuindex");
		if($rr)
		{
			print '<form action="" method="post">';
			print '<div class="sortablebox sortable">';
			$ii= 0;
			while($row= $modx->db->getRow($rr,'assoc'))
			{
				$ii++;
				print '<div class="srt_itm srt_itm_'.$row['id'].'">'.$row['id'].'. '.$row['pagetitle'].'<input type="hidden" name="menuindex[]" value="'.$row['id'].'" /></div>';
			}
			print '</div>';
			print '<br /><button type="submit" name="save">Сохранить порядок</button>';
			print '</form>';
		}
	}else print '<p>Выберите ресурс.</p>';
	?>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
	(function($){
		$(document).ready(function(){
			$('.sortable').sortable({
				items: '.srt_itm',
				placeholder: 'srt_itm_s',
				axis: 'y',
				opacity: 0.7
			});
		});
	})(jQuery);


	parent.tree.ca= 'parent';
	function setParent(pId, pName)
	{
		window.location= '<?=$module_url?>&docid='+pId;
	}
</script>

<style>
	body {
		background: #fff;
		font: normal 14px Arial;
	}
	.module_box {
		padding: 20px 30px;
	}
		.module_box br {
			clear: both;
			height: 0;
			font-size: 0;
			line-height: 0;
			overflow: hidden;
		}


	.content {
		padding: 50px 0 0 50px;
	}

	.sortablebox {
		
	}
		.sortablebox .srt_itm {
			padding: 4px 10px;
			background: #eee;
			margin: 5px 0;
			cursor: move;
			height: 17px;
		}
		.sortablebox .srt_itm_s {
			padding: 4px 10px;
			background: #fff5bf;
			margin: 5px 0;
			height: 17px;
		}
		.sortablebox .srt_itm_br {
			height: 20px;
		}

</style>