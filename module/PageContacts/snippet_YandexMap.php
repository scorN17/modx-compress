<?php
$points= $modx->runSnippet( 'PageContacts', array( 'array'=>true ) );
//return print_r($points);
?>
<style>#yandexmap{width:100%;height:500px;}</style>
<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<div id="yandexmap"></div>
<script type="text/javascript">
ymaps.ready(init);
var yandexmap;
function init(){
    yandexmap= new ymaps.Map( 'yandexmap',
        {
            center: [47.23107859368434,39.722849411069355],
            zoom: 11,
        }
    );
	
	yandexmap.geoObjects
	<?php
foreach( $points AS $row )
{
	$info= '';
	foreach( $row AS $row2 )
	{
		//if( $row2['left'] == 'Адрес:' ) $address= $row2['left'] .' '. $row2['right'];
		if( $row2['type'] != 7 && $row2['type'] != 8 ) $info .= $row2['left'] .' '. $row2['right'] .'<br />';
		if( $row2['type'] != 8 ) continue;
		print '.add(new ymaps.Placemark('.$row2['right'].',{
			balloonContent: "'.addslashes($info).'"
		},{
			preset: "islands#dotIcon",
			iconColor: "#18277C"
		}))';
	}
}
	?>
	;
}
</script>
<?php
//
?>
