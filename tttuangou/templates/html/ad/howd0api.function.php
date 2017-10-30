<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name howd0api.function.php
 * @date 2015-04-21 16:43:54
 */
 




function ad_config_save_parser_howd0api(&$data)
{
	if (count($data['list']) < 1) return;
	$orders = array();
	$ic = 0;
	foreach ($data['list'] as $id => $cfg)
	{
		$orders[$id] = $cfg['order'];
		$fid = 'file_'.$id;
		if (isset($_FILES[$fid]) && is_array($_FILES[$fid]) && $_FILES[$fid]['name'])
		{
			if(file_exists(ROOT_PATH.$data['list'][$id]['image'])) {
				@unlink(ROOT_PATH.$data['list'][$id]['image']);
				$orders[$id]['image'] = $data['list'][$id]['image'] = preg_replace('~\/hm\.[\w\d]+\.gif~i', '/hm.'.random(6).'.gif', $data['list'][$id]['image']);
			}
			logic('upload')->Save($fid, ROOT_PATH.$data['list'][$id]['image']);
		}
		$ic++;
	}
	arsort($orders);
	$dn = array();
	foreach ($orders as $id => $order)
	{
		$dn[$id] = $data['list'][$id];
	}
	$data['list'] = $dn;
	$data['fu'] = true;
}

?>
