<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name refund.mod.php
 * @date 2016-08-16 17:36:22
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
		$order_id= get('oid','number');
		$appcode= get('appcode');
		$token= get('token');
		if($appcode && $token){			$uid = logic('refund')->getuid($appcode, $token);
			$tempfile = 'apply_3g';
			$from = '3g';
		}else{
			$uid = MEMBER_ID;
			$tempfile = 'apply';
			$from = 'web';
		}
		if(!$uid || $uid < 1){
			$this->__message($from, '请先登录！', '?mod=account&code=login');
		}
        $info = logic('order')->GetOne($order_id, $uid);
                if ($order_id < 1 || empty($info)) {
            $this->__message($from, '订单信息错误!');
        }
		if (logic('refund')->GetOne($order_id)){
			$this->__message($from, '请勿重复提交退款申请!', '?mod=me&code=order');
		}
		if(meta('p_nar_'.$info['productid'])) {
			$this->__message($from, '该产品不支持退款！', '?mod=me&code=order');
		}

                $info['paymoney'] = $info['totalprice'];
        $price_cut = logic('cut')->order_calc($info['orderid'], $info['paymoney']);

        		$promo_cut_price = logic('promo_code')->order_calc($info, $info['paymoney']);

		if($info['product']['type'] == 'ticket'){
			$coupons = logic('coupon')->SrcList($uid, $order_id);
			if (count($coupons) === 0) {
				$this->__message($from, '该订单所有' . TUANGOU_STR . '券都已消费，不能申请退款。如有疑问，请联系客服电话：'.ini('data.cservice.phone'), 'index.php?mod=me&code=order', 10);
			}
			if($info['productnum'] != count($coupons) && $coupons[0]['mutis'] == 1){
				$info['tmsg'] = array(
					'money' => $info['totalprice'],
					'tnum' => $info['productnum'],
					'num' => $info['productnum']-count($coupons)
				);
				$info['paymoney'] = round(count($coupons)*$info['productprice'],2);
			}
		}

		include handler('template')->file('@m/refund_'.$tempfile);
    }

    
    function refundsave()
    {
		$order_id= post('orderid', 'number');
        $money   = post('money', 'float');
        $reason  = post('reason');
		$appcode = post('appcode');
		$token = post('token');
		$cash_type = post('cash_type', 'string');
		$cash_data = post('cash_data');
		if($appcode && $token){			$uid = logic('refund')->getuid($appcode, $token);
			$from = '3g';
		}else{
			$uid = MEMBER_ID;
			$from = 'web';
		}
		if(!$uid || $uid < 1){
			$this->__message($from, '请先登录！', '?mod=account&code=login');
		}
		$reasonstr = '';
		if($reason){
			foreach($reason as $k => $v){
				if($v){
					$reasonstr .= '【'.$v.'】';
				}
			}
		}
		if ($order_id == 0) {
            $this->__message($from, '错误的参数!');
        }
		if (logic('refund')->GetOne($order_id)){
			$this->__message($from, '请勿重复提交退款申请!', '?mod=me&code=order');
		}
        if ($money == 0) {
            $this->__message($from, '请填写退款金额!');
        }
		if (trim($reasonstr) == '') {
            $this->__message($from, '请填写退款理由!');
        }
        $rs = logic('refund')->save($order_id, $money, $reasonstr, $uid, $cash_type, $cash_data);
        if ($rs == -1) {
            $this->__message($from, '请勿重复提交退款申请!', '?mod=me&code=order');
        }elseif ($rs == -2) {
            $this->__message($from, '订单信息错误!', '?mod=me&code=order');
        }elseif($rs == -3) {
            $this->__message($from, '该订单团购券已经全部消费，不支持退款!', '?mod=me&code=order');
        }elseif(-4 == $rs) {
        	$this->__message($from, '该产品不支持退款!', '?mod=me&code=order');
        }
        $this->__message($from, '退款申请提交成功!', '?mod=me&code=order');
    }

	function __message($to = 'web', $message = '', $url = '')
	{
		if($to == 'web'){
			$this->Messager($message, $url);
		}else{
			include handler('template')->file('@mobile_message');exit;
		}
	}
}
?>