<?php
/**
 * 模块：图片验证码
 * @copyright (C)2011 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package module
 * @name seccode.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject
{
	
	public function ModuleObject($config)
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);

		$this->$runCode();
	}
	
	public function Main()
	{
		logic('seccode')->display();
	}

	public function check() 
	{
		exit(logic('seccode')->check(post('seccode')) ? 'true' : 'false');
	}

}