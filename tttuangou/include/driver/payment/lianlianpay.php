<?php

/**
 * 支付方式：连连键支付
 * @copyright (C)2011 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package payment
 * @name lianlianpay.php
 * @version 1.0
 */
class lianlianpayPaymentDriver extends PaymentDriver
{
	private $lpreturn;
	
	private $is_notify = null;
	
    public function CreateLink($payment, $parameter)
    {
    	
		if (!class_exists('LLpaySubmit')){
			include DRIVER_PATH.'payment/lianlianpay/llpay_submit.class.php';
		}

		$is_wap = in_array(WEB_BASE_ENV_DFS::$APPNAME, array('wap', '3g', 'api', 'sdk', 'm', ));
		if($is_wap) {
			$payment['config']['version'] = '1.2';
			$payment['config']['app_request'] = '3';
		}
				$lianlianpay = new LLpaySubmit($payment['config']);
		$data = $this->getdata($payment, $parameter);
		if($is_wap) {
						unset($data['version'], $data['userreq_ip']);
			$lianlianpay->llpay_gateway_new = 'https:/' . '/yintong.com.cn/llpayh5/payment.htm';
		}
		if('api' == WEB_BASE_ENV_DFS::$APPNAME && 'form_html' != $payment['app_return']) {
			$para_add = array('user_id'=>$data['user_id']);
			unset($data['app_request'], $data['timestamp'], $data['user_id'], $data['url_return']);
			return $lianlianpay->buildRequestPara($data, $para_add);
		} else {
			$bt_n = "确认连连支付付款";
			if('form_html' == $payment['app_return']) {
								$bt_n = '';
			}
			if(true === ENC_IS_GBK) {				
				if('index' == WEB_BASE_ENV_DFS::$APPNAME) {
					header('Content-Type: text/html; charset=utf-8');
					echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';
					$bt_n = ENC_G2U($bt_n);
					exit($lianlianpay->buildRequestForm($data, "post", $bt_n));
				}
			}
			return $lianlianpay->buildRequestForm($data, "post", $bt_n);
		}
    }

    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
		unset($_POST['mod']);
		if(true === DEBUG_LIANLIANPAY || 'POST' == $_SERVER['REQUEST_METHOD'] || $_POST){
			if (!class_exists('LLpayNotify')){
				include DRIVER_PATH.'payment/lianlianpay/llpay_notify.class.php';
				include DRIVER_PATH.'payment/lianlianpay/llpay_cls_json.php';
			}
			$lianlianpay = new LLpayNotify($payment['config']);
			if(true === $this->__Is_Nofity()) {
				$return = $lianlianpay->verifyReturn();
			} else {
								sleep(rand(1, 3));
				$return = $lianlianpay->verifyNotify();
			}
			$this->lpreturn = $lianlianpay->notifyResp;
			if(true === $return && 'SUCCESS' == $this->lpreturn['result_pay']) {
				$order = logic('callback')->Bridge($this->lpreturn['no_order'])->SrcOne($this->lpreturn['no_order'], true);
				if($order && $order['paytype'] == $payment['id']){
					if($order['product']['type'] == 'ticket'){
						return 'TRADE_FINISHED';
					}else{
						return 'WAIT_SELLER_SEND_GOODS';
					}
				}else{
					return 'VERIFY_FAILED';
				}
			}else{
				return 'VERIFY_FAILED';
			}
		}else{
			return 'VERIFY_FAILED';
		}
    }
    
    public function GetTradeData()
    {
        $trade = array();
		$return = $this->lpreturn;
		if($return && is_array($return)){
			$order = logic('callback')->Bridge($return['no_order'])->SrcOne($return['no_order'], true);
			$trade['sign'] = $return['no_order'];
			$trade['trade_no'] = $return['oid_paybill'];
			$trade['price'] = $return['money_order'];
			$trade['money'] = $trade['price'];
			$trade['status'] = (($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS');
			$trade['uid'] = $order['userid'];
		}
		return $trade;
    }
    
    public function StatusProcesser($status)
    {
    	if(true === $this->__Is_Nofity()) {
    		return false;
    	}
		if ($status != 'VERIFY_FAILED')
        {
            echo '{"ret_code":"0000","ret_msg":"success"}';
        }
        else
        {
            echo '{"ret_code":"9999","ret_msg":"failed"}';
        }
        return true;
    }
    
    public function GoodSender($payment, $express, $sign, $type)
    {
        if ($type == 'ticket')
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
        }
        else
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
        }
    }

	private function getdata($payment, $parameter) {
		
		$parameter['name'] = trim($parameter['name']);
		$data = array(
			'version' => $payment['config']['version'],
			'oid_partner' => $payment['config']['oid_partner'],
			'user_id' => $parameter['userid'],
			'timestamp' => local_date('YmdHis', time()),
			'sign_type' => $payment['config']['sign_type'],
			'busi_partner' => $payment['config']['busi_partner'],
			'no_order' => $parameter['sign'],
			'dt_order' => local_date('YmdHis', time()),
			'name_goods' => (true === ENC_IS_GBK ? array_iconv('gbk', 'utf-8', $parameter['name']) : $parameter['name']),
			'money_order' => $parameter['price'],
			'notify_url' => $parameter['notify_url'],

			'acct_name' => '',
						'id_no' => '',
			'valid_order' => $payment['config']['valid_order'],
			'userreq_ip' => client_ip(),
			'url_return' => $parameter['notify_url'],
		);
		if($payment['config']['app_request']) {
			$data['app_request'] = $payment['config']['app_request'];
		}
		if($parameter['is_recharge']) {
			$data['pay_type'] = '1';
		}
		if($payment['url_return']) {
			$data['url_return'] = $payment['url_return'];
		}
		$risk_items = array(
			'frms_ware_category' => ($payment['config']['frms_ware_category'] ? $payment['config']['frms_ware_category'] : '1005'),
			'user_info_dt_register' => (string) date('YmdHis', user($data['user_id'])->get('regdate')),
			'user_info_mercht_userno' => "{$data['user_id']}",
					);
		$user_phone = user($data['user_id'])->get('phone');
		if($user_phone) {
			$risk_items['user_info_bind_phone'] = "{$user_phone}";
		}

		
		if($parameter['addr_address']) {
			$risk_items['delivery_addr_full'] = (true === ENC_IS_GBK ? array_iconv('gbk', 'utf-8', $parameter['addr_address']) : $parameter['addr_address']);
			$risk_items['delivery_phone'] = $parameter['addr_phone'];
			$risk_items['logistics_mode'] = '2';
			$risk_items['delivery_cycle'] = '72h';
		}

				if($payment['config']['key2_oid_partner'] && false != ($bp = $payment['config']['key2_busi_partner'])) {
			$fix = 0;
			if(($parameter['addr_address'] && '109001' == $bp) || (false == $parameter['addr_address'] && '101001' == $bp)) {
				$fix = 1;
			}

			if($fix) {
				$data['oid_partner'] = $payment['config']['key2_oid_partner'];
				$data['busi_partner'] = $payment['config']['key2_busi_partner'];
				$risk_items['frms_ware_category'] = $payment['config']['key2_frms_ware_category'];
			}
		}

		$data['risk_item'] = json_encode($risk_items);
		return $data;
	}
	
	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('result_pay') || post('res_data'))
			{
				$this->is_notify = true;
			}
			else
			{
				$this->is_notify = false;
			}
		}
		return $this->is_notify;
	}
}
?>