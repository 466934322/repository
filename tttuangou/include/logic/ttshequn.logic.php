<?php

/**
 * 逻辑区：专题管理
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package ttshequn.logic.php
 * @version $Id$
 */

if(!function_exists('apim') && is_file(($api_func_loader_file = INCLUDE_PATH.'api/func/loader.php'))) {
	require $api_func_loader_file;
}

class TtshequnLogic {

	public function execute($ttshequnRequest = '') {
		list($method, $requestString) = explode(':', $ttshequnRequest);
		if($method && in_array($method, array('syncLogin'))) {
			$requestParams = unserialize(base64_decode($requestString));
			$requestParams = array_iconv('utf-8', ini('settings.charset'), $requestParams);
			if($requestParams) {
				return $this->$method($requestParams);
			}
		}
		return $this->result('请求参数错误');
	}

	
	private function syncLogin($params = array()) {
		$ret = false;

		$verifyResult = $this->requestVerify($params);
		if($verifyResult['error']) {
			return $verifyResult;
		}

		$result = $verifyResult['result'];

		list($ucuid, $username) = explode(':', $result);

		if($ucuid > 0 && $username) {
			$memberInfo = account()->Search('username', $username, 1);
			if($memberInfo && $ucuid == $memberInfo['ucuid'] && $memberInfo['uid'] > 0) {
				$ret = account()->setLoginStatus($memberInfo);
			}
		}
		return $this->success($ret);
	}

	
	public function requestVerify($params = array()) {
		if(!function_exists('apim')) {
			return $this->result('API 未启用');
		}
		if(true !== UCENTER) {
			return $this->result('UCENTER 未启用');
		}

		if(empty($params) || !is_array($params)) {
			return $this->result('请求参数不能为空');
		}

		$appcode = (string) $params['appcode'];
		if(empty($appcode) || false == preg_match('~^[\w\d\-\_]+$~i', $appcode) || 'ttshequn' != $appcode) {
			return $this->result('请求参数 appcode 错误');
		}

		$request = (string) $params['request'];

		$time = (int) $params['time'];
		if($time < 1 || abs($time - time()) > 864000) {
			return $this->result('请求参数 time 错误');
		}

		$sign = (string) $params['sign'];
		if(empty($sign) || 32 != strlen($sign)) {
			return $this->result('请求参数 sign 错误');
		}

		$appInfo = apim('app')->get($appcode);
		if(empty($appInfo) || false == $appInfo['seckey']) {
			return $this->result('请求参数 appcode 错误！');
		}

		if($sign != $this->sign($appcode, $request, $time, $appInfo['seckey'])) {
			return $this->result('签名校验失败');
		}

		return $this->success($request);
	}

	private function sign($appcode, $request, $time, $seckey) {
		return md5("{$appcode}:{$request}:{$time}/{$seckey}");
	}

	private function result($result, $error = 1) {
		$rets = array('result'=>$result, 'error'=>$error);
		if($error) {
			errorlog($rets, 'logic_ttshequn_result_error');
		}
		return $rets;
	}

	private function success($result = 'OK') {
		return $this->result($result, 0);
	}

}