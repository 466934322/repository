<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name llpay_notify.class.php
 * @date 2016-04-12 18:33:12
 */





require_once ("llpay_core.function.php");
require_once ("llpay_md5.function.php");

class LLpayNotify {
	var $llpay_config;
    var $notifyResp = array();
    var $result = false;
	function __construct($llpay_config) {
		$this->llpay_config = $llpay_config;
	}
	function LLpayNotify($llpay_config) {
		$this->__construct($llpay_config);
	}


	
	function verifyNotify() {
			if(false != ($res_data = $this->get_res_data())) {
				return $this->verify_res_data($res_data);
			}

					$is_notify = true;
			include_once ('llpay_cls_json.php');
			$json = new JSON;
			$str = file_get_contents("php:/" . "/input");
			$val = $json->decode($str);
			$oid_partner = getJsonVal($val,'oid_partner' );
			$sign_type = getJsonVal($val,'sign_type' );
			$sign = getJsonVal($val,'sign' );
			$dt_order = getJsonVal($val,'dt_order' );
			$no_order = getJsonVal($val,'no_order' );
			$oid_paybill = getJsonVal($val,'oid_paybill' );
			$money_order = getJsonVal($val,'money_order' );
			$result_pay = getJsonVal($val,'result_pay' );
			$settle_date = getJsonVal($val,'settle_date' );
			$info_order = getJsonVal($val,'info_order');
			$pay_type = getJsonVal($val,'pay_type' );
			$bank_code = getJsonVal($val,'bank_code' );
			$no_agree = getJsonVal($val,'no_agree' );
			$id_type = getJsonVal($val,'id_type' );
			$id_no = getJsonVal($val,'id_no' );
			$acct_name = getJsonVal($val,'acct_name' );

		
	
		$parameter = array (
			'oid_partner' => $oid_partner,
			'sign_type' => $sign_type,
			'dt_order' => $dt_order,
			'no_order' => $no_order,
			'oid_paybill' => $oid_paybill,
			'money_order' => $money_order,
			'result_pay' => $result_pay,
			'settle_date' => $settle_date,
			'info_order' => $info_order,
			'pay_type' => $pay_type,
			'bank_code' => $bank_code,
			'no_agree' => $no_agree,
			'id_type' => $id_type,
			'id_no' => $id_no,
			'acct_name' => $acct_name
		);
		if (!$this->getSignVeryfy($parameter, $sign)) {
			return;
		}
		$this->notifyResp = $parameter;
		$this->result = true;
		return true;
	}

	
	function verifyReturn() {
		if (empty ($_POST)) { 			return false;
		} else {
			if(false != ($res_data = $this->get_res_data())) {
				return $this->verify_res_data($res_data);
			}

			

						$parameter = array (
				'oid_partner' => $_POST['oid_partner' ],
				'sign_type' => $_POST['sign_type'],
				'dt_order' => $_POST['dt_order' ],
				'no_order' =>  $_POST['no_order' ],
				'oid_paybill' => $_POST['oid_paybill' ],
				'money_order' => $_POST['money_order' ],
				'result_pay' =>  $_POST['result_pay'],
				'settle_date' => $_POST['settle_date'],
				'info_order' =>$_POST['info_order'],
				'pay_type'=>$_POST['pay_type'],
				'bank_code'=>$_POST['bank_code'],
			);

			if (!$this->getSignVeryfy($parameter, trim($_POST['sign' ]))) {
				return false;
			}
			$this->notifyResp = $parameter;
			$this->result = true;
			return true;

		}
	}

	function verify_res_data($res_data = '') {
		$res_data = ($res_data ? $res_data : $this->get_res_data());

		if (empty ($res_data)) { 			return false;
		} else {
		
		include_once ('llpay_cls_json.php');
              $json = new JSON;
			    	    $oid_partner = $json->decode($res_data)-> {'oid_partner' };
		
															
						$parameter = array (
				'oid_partner' =>$oid_partner,
				'sign_type' => $json->decode($res_data)-> {'sign_type' },
				'dt_order' => $json->decode($res_data)-> {'dt_order' },
				'no_order' => $json->decode($res_data)-> {'no_order' },
				'oid_paybill' => $json->decode($res_data)-> {'oid_paybill' },
				'money_order' => $json->decode($res_data)-> {'money_order' },
				'result_pay' => $json->decode($res_data)-> {'result_pay' },
				'settle_date' => $json->decode($res_data)-> {'settle_date' },
				'info_order' => $json->decode($res_data)-> {'info_order' },
				'pay_type'=> $json->decode($res_data)-> {'pay_type' },
				'bank_code'=> $json->decode($res_data)-> {'bank_code' },
			);

			if (!$this->getSignVeryfy($parameter,$json->decode($res_data)-> {'sign' })) {
				return false;
			}
			$this->notifyResp = $parameter;
			$this->result = true;
			return true;

		}
	}

	function get_res_data() {
		return (isset($_POST['res_data']) ? $_POST['res_data'] : (isset($_GET['res_data']) ? $_GET['res_data'] : $_REQUEST['res_data']));
	}

	
	function getSignVeryfy($para_temp, $sign) {
				$para_filter = paraFilter($para_temp);

				$para_sort = argSort($para_filter);

				$prestr = createLinkstring($para_sort);

		true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log", "原串:" . $prestr . "\n", FILE_APPEND);
		true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log", "sign:" . $sign . "\n", FILE_APPEND);
		$isSgin = false;
		switch (strtoupper(trim($this->llpay_config['sign_type']))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->llpay_config['key']);
				break;
			default :
				$isSgin = false;
		}

		return $isSgin;
	}

}
?>
