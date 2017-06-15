<?php
$modx->runSnippet('ShopAction',array('act'=>'check_items_in_catalog'));
$items= $modx->runSnippet('ShopAction',array('act'=>'items'));
if(is_array($items) && count($items))
{
	foreach($items AS $key => $row)
	{
		$img= $modx->getTemplateVar('images','*',$row['id_item']);
		$img= explode('||', $img['value'])[0];
		$img= $modx->runSnippet('ImgCrop72',array('img'=>$img, 'w'=>70, 'h'=>70, 'fill'=>true));
		
		$prms_p= '';
		$prms= $modx->runSnippet('CatalogFilter_Props', array('id'=>$row['id_item'], 'values'=>true));
		if(is_array($prms) && count($prms))
		{
			$prms_p= '<div class="shbi_prms_but"><span>Параметры</span></div>
				<div class="shbi_prms_box">
					<div class="shbi_prms">';
		 	$ii= 0;
			foreach($prms AS $prm)
			{
				$cc= count($prm['values']);
				if(is_array($prm['values']) && $cc)
				{
					$ii++;
					$prms_p.= '<div class="shbip_row '.($ii%2==0?'shbip_row_alt':'').'">
						<div class="shbip_lb">'.$prm['name'].'</div>
						<div class="shbip_vl">';

					foreach($prm['values'] AS $vls)
						$prms_p.= '<div>'.($cc>=2?'&mdash; ':'') .$vls.'</div>';

					$prms_p.= '</div><br></div>';
				}
			}
			$prms_p.= '</div></div>';
		}
		
		$pp .= '<div class="itembox '.($key%2==0 ? 'itembox_alt' : '').'">
			<div class="item" data-u="'.$row['uniq'].'">';
		
		$pp .= '<div class="shbi_img">
					<img src="'.$img.'" />
				</div>
				
				<div class="shbi_tit">
					<div><a class="as2 font2" href="'.$modx->makeUrl($row['id_item']).'">'.$row['name'].'</a></div>';
		if($row['art']) $pp .= '<div>&mdash; Арт. <span>'.$row['art'].'</span></div>';
		if($prms_p) $pp .= $prms_p;
		$pp .= '</div>';

		$sum= $row['price'] * $row['count'];

		$pp .= '<div class="shbi_cntl shbi_del">[[ico?&n=`delete`]]</div>

				<div class="shbi_cntl shbi_sum">
					<div>Сумма</div>
					<div><span class="price">'.$modx->runSnippet('Price',array('price'=>$sum)).'</span> <span class="ruble">руб.</span></div>
					<div class="svgloading"></div>
				</div>

				<div class="shbi_cntl shbi_cc">
					<div>Кол-во, '.$row['ed'].'</div>
					<div>
						<span class="shbi_minus shbi_plus_minus">[[ico?&n=`minus`]]</span>
						<div class="shbi_ccval">'.$row['count'].'</div>
						<span class="shbi_plus shbi_plus_minus">[[ico?&n=`plus`]]</span>
						<br>
					</div>
				</div>

				<div class="shbi_cntl shbi_price">
					<div>Цена</div>
					<div><span class="price">'.$modx->runSnippet('Price',array('price'=>$row['price'])).'</span> <span class="ruble">руб.</span><span class="ed"> /'.$row['ed'].'</span></div>
				</div>

				<br>
			</div>
			<div class="svgloading"></div>
		</div>';
	}
}
return $pp;
