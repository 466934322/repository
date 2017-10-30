<?php

/**
 * Master Object
 */

class MasterObject
{
	/**
	 * 系统的全局配置
	 *
	 * @var array
	 */
	var $Config=array();//系统配置

	/**
	 * 操作指令，执行动作
	 *
	 * @var string
	 */
	public $Code='index';

	public $OPC = '';

	public function MasterObject(&$config)
	{
		$config['v'] = SYS_VERSION.SYS_RELEASE;
		//配置文件
		$this->Config=$config;//读取配置文件
		Obj::register('config', $this->Config);

		$this->Code   = trim($_POST['code']?$_POST['code']:$_GET['code']);
		$this->OPC   = trim($_POST['op']?$_POST['op']:$_GET['op']);

		$loader = INCLUDE_PATH.'api/func/loader.php';
		if (is_file($loader))
		{
			require $loader;
		}
 	}
}