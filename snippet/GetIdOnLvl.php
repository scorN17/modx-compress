<?php
/**
 * GetIdOnLvl
 *
 * @version   7.0
 * @date      26.09.2019
 *
 * $root, $id, $fields, $lvl, $prm
 *
 */
$fields = 'id,parent'.($fields?',':'').$fields;
$doc = $modx->getDocument($id,$fields);
$list[] = $doc;
while ($id != $root && $doc['parent'] != $root && $doc['parent'] > 0) {
	$doc = $modx->getDocument($doc['parent'],$fields);
	$list[] = $doc;
}
if ($doc['parent'] == 0) {
	$list[] = array('id'=>0);
} elseif ($doc['parent'] == $root) {
	$doc = $modx->getDocument($doc['parent'],$fields);
	$list[] = $doc;
}
$list[] = false;
$list = array_reverse($list);
return ($lvl ? ($prm ? $list[$lvl][$prm] : $list[$lvl]) : $list);
