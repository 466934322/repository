<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name igos.ui.php
 * @date 2016-04-12 18:33:12
 */





class iGOSUI
{
	
	public function load($product,$user='', $tpl_suffix = '')
	{
		$style = ini('ui.igos.style');
		$style || $style = 'lashou';
		if(!in_array($style,array('lashou','meituan','four'))){
			$style = 'lashou';
		}
        if(user()->get('id')){
            $uid = user()->get('id');
        }
		if(INDEX_DEFAULT === true && false == ini('ui.igos.oldindex')){
			 include handler('template')->file('@html/igos/'.$style.'/default' . $tpl_suffix);
		}
		else{
			include handler('template')->file('@html/igos/'.$style.'/index' . $tpl_suffix);
		}
	}
}

?>