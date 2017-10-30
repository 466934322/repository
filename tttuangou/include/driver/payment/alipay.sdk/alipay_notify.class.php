<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name alipay_notify.class.php
 * @date 2016-09-19 17:35:14
 */




require_once("alipay_core.function.php");
require_once("alipay_rsa.function.php");
require_once("alipay_md5.function.php");

class AlipayNotify {
    
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
	}
    function AlipayNotify($alipay_config) {
    	$this->__construct($alipay_config);
    }
    
	function verifyNotify(){
		if(empty($_POST)) {			return false;
		}
		else {
						unset($_POST['mod']);
			$decrypt_post_para = $_POST;
			if ($this->alipay_config['sign_type'] == '0001') {
				$decrypt_post_para['notify_data'] = rsaDecrypt($decrypt_post_para['notify_data'], $this->alipay_config['private_key_path']);
			}

																					$responseTxt = 'true';
						
						$responseTxt = 'true';
			if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}


						$isSign = $this->getSignVeryfy($decrypt_post_para, $_POST["sign"], true);

																														
												if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}

    
	function verifyReturn(){
		if(empty($_GET)) {			return false;
		}
		else {
						unset($_GET['mod'],$_GET['pid']);
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"], true);
						$responseTxt = 'true';
			if (! empty($_GET["notify_id"])) {$responseTxt = $this->getResponse($_GET["notify_id"]);}

																														
												if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}

	
	function decrypt($prestr) {
		return rsaDecrypt($prestr, trim($this->alipay_config['private_key_path']));
	}

	
	function sortNotifyPara($para) {
		$para_sort['service'] = $para['service'];
		$para_sort['v'] = $para['v'];
		$para_sort['sec_id'] = $para['sec_id'];
		$para_sort['notify_data'] = $para['notify_data'];
		return $para_sort;
	}

    
	function getSignVeryfy($para_temp, $sign, $isSort) {
    	$logData = "\r\n";
    	$logData .= '$this->alipay_config ' . "\r\n" . var_export($this->alipay_config, true) . "\r\n";
    	$logData .= '$para_temp ' . "\r\n" . var_export($para_temp, true) . "\r\n";
    	$logData .= '$sign ' . "\r\n" . var_export($sign, true) . "\r\n";
    	$logData .= '$isSort ' . "\r\n" . var_export($isSort, true) . "\r\n";
				$para = paraFilter($para_temp);
    	$logData .= '$para ' . "\r\n" . var_export($para, true) . "\r\n";

				if($isSort) {
			$para = argSort($para);
		} else {
			$para = $this->sortNotifyPara($para);
		}
    	$logData .= '$para ' . "\r\n" . var_export($para, true) . "\r\n";

				$prestr = createLinkstring($para);
    	$logData .= '$prestr ' . "\r\n" . var_export($prestr, true) . "\r\n";

		$isSgin = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
				break;
			case "RSA" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			case "0001" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			default :
				$isSgin = false;
		}
    	$logData .= '$isSgin ' . "\r\n" . var_export($isSgin, true) . "\r\n";
    	
    	
		return $isSgin;
	}

    
	function getResponse($notify_id) {
		$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);

		return $responseTxt;
	}
}
?>
