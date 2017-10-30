<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ad.ui.php
 * @date 2015-08-31 16:32:13
 */





class AdDisplayerUI
{
    
    public function load($block)
    {
        $masterFile = handler('template')->TemplateRootPath.'html/ad/'.$block.'.html';
        if (!is_file($masterFile)) return;
        if (!ini('ad.'.$block.'.enabled')) return;
        $cfg = ini('ad.'.$block.'.config');
        $cityid = logic('misc')->City('id');
        foreach($cfg['list'] as $k=>$v){
            if($v['city'] != 1 && $v['city'] != 0 && $cityid != $v['city']){
                unset($cfg['list'][$k]);
            }
        }
        include handler('template')->file('@html/ad/'.$block);
    }

    
    public function enabled($block) {
        $masterFile = handler('template')->TemplateRootPath.'html/ad/'.$block.'.html';
        if (!is_file($masterFile)) return false;
        if (!ini('ad.'.$block.'.enabled')) return false;
        $cfg = ini('ad.'.$block.'.config');
        if(false == $cfg) return false;

        return true;
    }
}

?>