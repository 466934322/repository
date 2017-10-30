<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name print.mod.php
 * @date 2016-09-19 17:35:14
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		exit('hello');
	}
	function Delivery()
	{
		$this->CheckAdminPrivs('print');
		$oid = get('oid', 'number');
		$senderID = get('sender', 'int');
				$lastADDR = meta('cdp_service_lastADDR');
		if ($lastADDR != $senderID)
		{
			meta('cdp_service_lastADDR', $senderID);
		}
				$cdpResult = logic('express')->cdp()->CreatePrinterConfig($oid, $senderID);
		if ($cdpResult['__error__'])
		{
			$this->Messager('您还没有设置打印模板，系统正在跳转到模板编辑页面，请稍候...', '?mod=express&code=corp&op=delivery&id='.$cdpResult['corpID'], 3);
		}
				logic('express')->cdp()->Printed($oid, $senderID);
				$cdpCFG = $cdpResult['config'];
		$cdpDATA = $cdpResult['cdp'];
				$background = logic('upload')->GetOne($cdpDATA['bgid']);
		$background['extra'] = unserialize($background['extra']);
		if (!$background['extra']['width'] || !$background['extra']['height'])
		{
			$background['extra'] = handler('image')->Info($background['path']);
			logic('upload')->Field($background['id'], 'extra', serialize(array('width'=>$background['extra']['width'],'height'=>$background['extra']['height'])));
		}
				include handler('template')->file('@admin/print_delivery');
	}
	function Delivery_queue()
	{
		$this->CheckAdminPrivs('print');
		$where = ' 1 ';
        if('admin'== MEMBER_ROLE_TYPE  ){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                if($smember['city_place_region']){
                    $where =  " p.`city`='{$smember['area']}' AND p.`city_place_region`='{$smember['city_place_region']}' ";
                }else{
                    $where = " p.`city`='{$smember['area']}' ";
                }
            }
        }
		$list = logic('delivery')->GetList(DELIV_PROCESS_IN, $where);
		include handler('template')->file('@admin/print_delivery_queue');
	}
}

?>