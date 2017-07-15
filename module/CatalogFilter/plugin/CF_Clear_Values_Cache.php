/**
 * CF_Clear_Values_Cache
 *
 * Очистка кэша фильтра
 *
 * @version   10.0
 * @date      17.05.2017
 * @events    OnDocFormSave
 *
 * @dependence
 */

$e= &$modx->Event;
$myid= $e->params['id'];
$myparent= $modx->getDocument($myid, 'parent');
$myparent= $myparent['parent'];
if($myparent) $modx->db->query("UPDATE ".$modx->getFullTableName('_catfilter_value_cache')." SET dt=0, e='n' WHERE folderid={$myparent}");
