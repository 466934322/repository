<?php

/**
 * 模块：手机短信验证
 * @copyright (C)2011 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package module
 * @name phone.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}
	public function main()
	{
		exit('ok');
	}
	
	public function vfsend()
	{
		$phone = post('phone', 'txt');
		if(empty($phone) && get('user_phone') && user()->get('phone_validate')) {
			$phone = user()->get('phone');
		}
		
		$check_exists = post('check_exists', 'txt');		
		if ($check_exists == 'no') {
			$ret = logic('phone')->Check($phone, false, true);
		} else {
			$ret = logic('phone')->Check($phone, true, true);
		}		

		if(false == $ret) {
			if(true == logic('seccode')->verify(post('seccode'))) {
				$ret = logic('phone')->VfSend($phone, false); 			} else {
				$ret = '请输入正确的图片验证码';
			}
		}

		exit($ret);
	}

		
}

?>