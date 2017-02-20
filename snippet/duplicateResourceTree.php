<?php
/**
 * Дублирует все дерево ресурсов (вместе с TV-параметрами) заданного родителя в другого родителя.
 */
return;
	
$from= 5;
$to= 28;

print '========================================================================================';


return duplicateResourceTree($modx, $from, $to);

function duplicateResourceTree(&$modx, $from, $to)
{
	$rr= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_content')." WHERE parent={$from} AND deleted=0 ORDER BY pagetitle");
	if($rr && $modx->db->getRecordCount($rr))
	{
		while($row= $modx->db->getRow($rr))
		{
			$doc_from= $row['id'];
			$doc_to= false;
			
			$foo= $modx->db->escape($row['pagetitle']);
			$rr2= $modx->db->query("SELECT id FROM ".$modx->getFullTableName('site_content')." WHERE pagetitle='{$foo}' AND parent={$to} LIMIT 1");
			if($rr2 && $modx->db->getRecordCount($rr2))
			{
				$doc_to= $modx->db->getValue($rr2);
				
			}elseif($rr2){
				$newalias= $modx->runSnippet('GenerAlias',array('txt'=>$row['pagetitle']));

				$qq= "";
				foreach($row AS $foo => $bar)
				{
					$bar= $modx->db->escape($bar);
					if($foo == 'id') continue;
					if($foo == 'parent') $bar= $to;
					if($foo == 'alias') $bar= $newalias;
					$qq .= ( ! empty($qq)?",":"") ."`{$foo}`='{$bar}'";
				}

				$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_content')." SET ".$qq);
				$doc_to= $modx->db->getInsertId();
			}
			
			if($doc_from && $doc_to)
			{
				$rr2= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE contentid={$doc_from}");
				if($rr2 && $modx->db->getRecordCount($rr2))
				{
					while($row2= $modx->db->getRow($rr2))
					{
						$rr3= $modx->db->query("SELECT * FROM ".$modx->getFullTableName('site_tmplvar_contentvalues')." WHERE tmplvarid={$row2['tmplvarid']} AND contentid={$doc_to} LIMIT 1");
						if( ! $rr3 || ($rr3 && $modx->db->getRecordCount($rr3))) continue;

						$foo= $modx->db->escape($row2['value']);

						$modx->db->query("INSERT INTO ".$modx->getFullTableName('site_tmplvar_contentvalues')." SET tmplvarid={$row2['tmplvarid']}, contentid={$doc_to}, `value`='{$foo}'");
					}
				}
				
				duplicateResourceTree($modx, $doc_from, $doc_to);
			}
		}
	}
}
