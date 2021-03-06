<?php
$data['pagetitle']= $modx->runSnippet('Typograph',array('t'=>$data['pagetitle']));

$images= explode('||',$data['tv.images']);

$data['tv.images']= array_shift($images);
$data['tv.images_mini']= $data['tv.images'];
$data['tv.images']= $modx->runSnippet('ImgCrop72',array('img'=>$data['tv.images'], 'w'=>1111, 'h'=>1111));
$data['tv.images_mini']= $modx->runSnippet('ImgCrop72',array('img'=>$data['tv.images_mini'], 'w'=>222, 'h'=>222, 'fill'=>true));

if(is_array($images) && count($images))
{
	foreach($images AS $img)
	{
		$i= $modx->runSnippet('ImgCrop72',array('img'=>$img, 'w'=>1111, 'h'=>1111));
		$mi= $modx->runSnippet('ImgCrop72',array('img'=>$img, 'w'=>100, 'h'=>100, 'fill'=>true));
		
		$data['mini_images'] .= '<a data-fancybox="catitempage" href="'.$i.'"><img src="'.$mi.'" /></a>';
	}
}

if($data['tv.discount'])
{
	$discount= round($data['tv.price'] * 100 / (100-$data['tv.discount']));
	$data['tv.discount']= '<div class="ci_discount"><span><span class="price font2">'.$modx->runSnippet('Price',array('price'=>$discount)).'</span><img src="template/img/discount.svg" /></span>
			<span class="ruble">руб.</span>
		</div><br>';
}

$data['tv.price']= ( ! $data['tv.price'] ? '&mdash;' : number_format($data['tv.price'], 0,',','&thinsp;'));

$uniq= md5($data['id']);
$inbasket= $modx->runSnippet('ShopAction',array('act'=>'stat', 'uniq'=>$uniq));
$data['inbasket']= ($inbasket=='y' ? 'inbasket' :'');

$prms= $modx->runSnippet('CatalogFilter_Props', array('id'=>$data['id'], 'values'=>true));
if(is_array($prms) && count($prms))
{
	$data['cf.prms']= '<div class="cip_prms">';
	foreach($prms AS $prm)
	{
		$cc= count($prm['values']);
		if(is_array($prm['values']) && $cc)
		{
			$data['cf.prms'].= '<div class="cipp_row">
				<div class="cipp_lb">'.$prm['name'].'</div>
				<div class="cipp_vl">';
				
			foreach($prm['values'] AS $vl)
			{
				$data['cf.prms'].= '<div>'.($cc>=2?'&mdash; ':'') .$vl.'</div>';
			}
			
			$data['cf.prms'].= '</div><br></div>';
		}
	}
	$data['cf.prms'] .= '</div>';
}

return $data;
