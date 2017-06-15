if($_GET['act'] == 'catalogfilter_get_items')
{
	return $modx->runSnippet('CATALOG', array('id'=>$id, 'type'=>'content', '_x'=>$_REQUEST['_x']));
}

if($_GET['act'] == 'catalogfilter_sort_params')
{
	$prms= trim($_GET['prms']);
	if( ! $prms) return;
	$prms= explode('/',$prms);
	if(is_array($prms) && count($prms))
	{
		foreach($prms AS $row)
		{
			$row= explode(',',$row);
			$id= intval(array_shift($row));
			sort($row);
			
			$prms_a[$id]= implode('-',$row);
		}
		ksort($prms_a);
		foreach($prms_a AS $key => $row)
		{
			$result .= (!empty($result)?'/':'') .$key.'-'.$row;
		}
	}
	return $result;
}
