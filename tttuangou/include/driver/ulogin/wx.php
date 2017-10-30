<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wx.php
 * @date 2015-08-31 16:32:13
 */




session_start();

class wxUnionLoginDriver extends UnionLoginDriver {

	private $wx;

	
	public function __construct() {
		if ( ini_get("allow_url_fopen") == '' )	{
			exit('请在php.ini里开启allow_url_fopen选项');
		}

		if ( function_exists('openssl_open') === false ) {
			exit('请确认开启了openssl组件');
		}

		require_once(dirname(__FILE__).'/wx/WxPayPubHelper/WxPayPubHelper.php');

			$this -> wx = new LoginApi_pub();
	}

	public function login() {

		$_SESSION['wx_state'] = md5(uniqid(rand(), true));

		$redirectUrl = ini('settings.site_url').'?mod=account&from=WeChat';

		global $_SERVER;
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		$isWX = strpos($ua, 'micromessenger/');
		if ($isWX !== false) {				$wxVer = (float)substr($ua, ($isWX + 15), 3);
			$_SESSION['wx_ver'] = $wxVer;

					$url = $this -> wx -> createOauthUrlForCode2($redirectUrl);
				}
		else {
			unset($_SESSION['wx_ver']);
			$url = $this -> wx -> createOauthUrlForCode1($redirectUrl);
		}
		Header ('Location: '. $url);
		exit;
	}

	public function linker() {
		$this -> login();
		exit;
	}

	public function callback() {

		if (empty($_GET['state']) || empty($_GET['code'])) {
			tglog('TG Fatal Error: Illegal WX Callback Without Paras [state] And [code]!');
			$this -> login();
		}

		session_start();
			if (!empty($_SESSION['wx_state']) && ($_GET['state'] != $_SESSION['wx_state'])) {
			die('非法请求 — State 不匹配！|'. $_SESSION['wx_state'].'|');
		}
		unset($_SESSION['wx_state']);

		$this -> wx -> setCode($_GET['code']);
		$this -> wx -> getAT();
    }

	
	public function get_user_info() {
		return $this -> wx -> getUserInfo();
	}

	public function get_openid() {
		$this -> callback();				header('Location: '. rewrite('?mod=account&code=wxgetuserinfo'));
		exit;
	}
}

define('DEBUG_MODE', false);

function tglog ($str='') {
	if (defined('DEBUG_MODE') && DEBUG_MODE) {
		if (!empty($str)) {
			$data  = date('Y/m/d H:i:s')."\n";
			$data .= $str ."\n\n";
					$file = DATA_PATH .'tg_'. date('Ymd').'.log';
			file_put_contents($file, $data, FILE_APPEND);
		}
	}
	return;
}
