//CheckResourceTree
//Проверка дерева ресурсов
//v1.0
//30.09.2016
//Event: OnSiteRefresh
//----------------------------------------------------------------------------
//
//
//
//
//----------------------------------------------------------------------------
$e= &$modx->Event;
if($e->name=='OnSiteRefresh')
{
	if( ! function_exists('checktree'))
	{
		function checktree($id, &$list)
		{
			$rr= mysql_query("SELECT id,isfolder FROM bogem_site_content WHERE parent={$id} ORDER BY id");
			if($rr && mysql_num_rows($rr)>0)
			{
				$list[$id][1]= true;
				while($row= mysql_fetch_assoc($rr))
				{
					$list[$row['id']]= array(($row['isfolder']==='1'?true:false), false);
					checktree($row['id'], $list);
				}
			}
		}
	}
	
	$id= 0;
	$list[$id]= array(true, false);
	checktree($id, $list);
	
	if(is_array($list) && count($list))
	{
		foreach($list AS $docid => $params)
			if( ! $params[0] && $params[1]) $errors[1][$row['id']]= true; //Не контейнерный ресурс имеет дочерние элементы
		
		$rr= mysql_query("SELECT id FROM bogem_site_content ORDER BY id");
		if($rr && mysql_num_rows($rr)>0)
		{
			while($row= mysql_fetch_assoc($rr))
			{
				if( ! isset($list[$row['id']]))
				{
					//mysql_query("DELETE FROM bogem_site_content WHERE id={$row[id]} LIMIT 1");
					//mysql_query("DELETE FROM bogem_site_tmplvar_contentvalues WHERE contentid={$row[id]}");
					$errors[2][$row['id']]= true; //Элемент выпал из дерева ресурсов
				}
			}
		}
	}
	if(is_array($errors[1]) && count($errors[1]))
		$pp .= '<p><b>Не контейнерный ресурс имеет дочерние элементы</b></p><pre>'.print_r($errors[1],1).'</pre>';
	if(is_array($errors[2]) && count($errors[2]))
		$pp .= '<p><b>Элемент выпал из дерева ресурсов</b></p><pre>'.print_r($errors[2],1).'</pre>';
	
	//mysql_query("UPDATE bogem_site_content SET content='==".addslashes($pp)."' WHERE id=19 LIMIT 1");
}
