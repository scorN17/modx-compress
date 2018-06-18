// Запрет переноса в непапку
$isfolder = $modx->getField('isfolder', $new_parent);
if ( ! $isfolder) {
	$e = &$modx->Event;
	$e->setOutput($old_parent);
}
