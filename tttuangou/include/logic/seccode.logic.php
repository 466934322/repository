<?php

/**
 * 逻辑区：验证码相关
 * @copyright (C)2011 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package logic
 * @name seccode.logic.php
 * @version 1.0
 */

class SeccodeLogic
{

	private $time = 300; 

	
	public function html($id="") {

		include template('@html/seccode_inputer');
	}
	
	
	public function display($ssid = '') {
		$seccode = $this->make($ssid);

		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");

		$code = driver('seccode');
		$code->code = $seccode;
		$code->ttf = 1;
		$code->fontpath =  ROOT_PATH.'/data/seccode/font/';
		$code->datapath =  ROOT_PATH.'/data/seccode/';
		$code->display();
		exit;
	}

	
	public function make($ssid = '') {
		$seccode = mt_rand(100000, 999999);
		$seccodeunits = '';

		$s = sprintf('%04s', base_convert($seccode, 10, 24));
		$seccodeunits = 'BCEFGHJKMPQRTVWXY2346789';
		if($seccodeunits) {
			$seccode = '';
			for($i = 0; $i < 4; $i++) {
				$unit = ord($s{$i});
				$seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
			}
		}

		if(empty($ssid)) {
			$ssid = handler('cookie')->GetVar('sid');
			$ssid = $ssid . "." . substr(md5($ssid . client_ip() . MEMBER_ID . ini('settings.auth_key')), -25);
			handler('cookie')->SetVar('seccode', $ssid, $this->time);
		}
				
		if($ssid) {
			dbc()->query("REPLACE INTO ".table('seccheck')." (`ssid`, `dateline`, `code`, `ip`) VALUES ('$ssid', '".time()."', '$seccode', '".client_ip()."')");
		} else {
			exit('seccode ssid is empty');
					}
		return $seccode;
	}

	
	public function check($seccode, $ssid = '', $update_seccode = false) {
		if(empty($ssid)) {
			$ssid = handler('cookie')->GetVar('seccode');
			list($sid, $sign) = explode(".", $ssid);
			if($sign != substr(md5($sid . client_ip() . MEMBER_ID . ini('settings.auth_key')), -25)) {
				return false;
			}
		}

		if($ssid) {
			$seccheck = dbc()->Query("SELECT * FROM ".table('seccheck')." WHERE `ssid`='$ssid'")->GetRow();
		} else {
			exit('seccode ssid is empty');
		}
		
		$ret = ($seccode && time() - $seccheck['dateline'] <= $this->time && $seccheck['verified'] < 6 && strtolower($seccode) == strtolower($seccheck['code']));

		if($seccheck) {
			$ups = array();
			if($ret) {
				$ups[] = "`succeed`=`succeed`+1";
			} else {
				$ups[] = "`verified`=`verified`+1";
			}

			if($update_seccode) {
				if($ret) {
					$ups[] = "`code`=''";				
				} else {
									}
			}

			if($ups) {
				dbc()->Query("UPDATE ".table('seccheck')." SET ".implode(" , ", $ups)." WHERE `ssid`='$ssid'");
			}
		}

		return $ret;
	}

	
	public function verify($seccode, $ssid = '') {
		return $this->check($seccode, $ssid, true);
	}
	
}