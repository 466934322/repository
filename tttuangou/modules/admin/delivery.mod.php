<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name delivery.mod.php
 * @date 2015-11-12 16:33:06
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
        $this->CheckAdminPrivs('delivery');
		header('Location: ?mod=delivery&code=vlist');
    }
    function vList()
    {
        $this->CheckAdminPrivs('delivery');
		
		
		        $alsend = get('alsend', 'txt');
		if ($alsend == 'yes') {				$ordPROC = WAIT_BUYER_CONFIRM_GOODS;
		}
		else if ($alsend == 'no') {				$ordPROC = WAIT_SELLER_SEND_GOODS;
		}
		else {				$ordPROC = TRADE_FINISHED; 
		}
		$ordPROC = get('ordproc', 'string');			
		$dlvPROC = 'o.process IN(' . '"' . WAIT_SELLER_SEND_GOODS . '"' .
									 ',"' . WAIT_BUYER_CONFIRM_GOODS . '"' .
									 ',"' . TRADE_FINISHED . '"'. 
							   ')';	
		$processArr = array(WAIT_SELLER_SEND_GOODS, WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED);
		if (in_array($ordPROC, $processArr)) {
			$dlvPROC = 'o.process="' . $ordPROC . '"';			}
		
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$dlvPROC .= ' AND o.productid IN('.$asql.') ';
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $asql = logic('product')->manage_area_product(MEMBER_ID);
                $dlvPROC .= ' AND o.productid IN('.$asql.')';
            }
        }
        $list = logic('delivery')->GetList($ordPROC,$dlvPROC);
        include handler('template')->file('@admin/delivery_list');
    }
    function Process()
    {
        $this->CheckAdminPrivs('delivery');
		$oid = get('oid', 'number');
        $order = logic('order')->GetOne($oid);
        if (!$order)
        {
            $this->Messager(__('找不到相关订单！'));
        }
        $user = user($order['userid'])->get();
        $payment = logic('pay')->SrcOne($order['paytype']);
        $express = logic('express')->SrcOne($order['expresstype']);
        $address = logic('address')->GetOne($order['addressid']);
        include handler('template')->file('@admin/delivery_process');
    }
    function Upload_single()
    {
        $this->CheckAdminPrivs('delivery','ajax');
		if(strlen(get('no','txt')) > 8){
			logic('delivery')->Invoice(get('oid', 'number'), get('no', 'txt')) && exit('ok');
		}else{
			exit('error');
		}
    }
}

?>