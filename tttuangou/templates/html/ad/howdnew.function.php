<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name howdnew.function.php
 * @date 2015-08-31 16:32:13
 */





function ad_config_save_parser_howdnew(&$data)
{
    if (count($data['list']) < 1) return;
    $orders = array();
    $ic = 0;
    foreach ($data['list'] as $id => $cfg)
    {
        $orders[$id] = $cfg['order'];
        $fid = 'file_'.$id;
        if($cfg['image2']){
            $fid2 = 'file_'.$id.'_2';
        }
        if($cfg['image_rup']){
            $fid3 = 'file_'.$id.'_rup';
        }
        if($cfg['image_rdown']){
            $fid4 = 'file_'.$id.'_rdown';
        }
        if (isset($_FILES[$fid]) && is_array($_FILES[$fid]))
        {
            logic('upload')->Save($fid, ROOT_PATH.$data['list'][$id]['image']);
        }
        if (isset($_FILES[$fid2]) && is_array($_FILES[$fid2]))
        {
            logic('upload')->Save($fid2, ROOT_PATH.$data['list'][$id]['image2']);
        }
        if (isset($_FILES[$fid3]) && is_array($_FILES[$fid3]))
        {
            logic('upload')->Save($fid3, ROOT_PATH.$data['list'][$id]['image_rup']);
        }
        if (isset($_FILES[$fid4]) && is_array($_FILES[$fid4]))
        {
            logic('upload')->Save($fid4, ROOT_PATH.$data['list'][$id]['image_rdown']);
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
