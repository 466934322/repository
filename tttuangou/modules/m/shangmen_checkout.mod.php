<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name shangmen_checkout.mod.php
 * @date 2015-08-31 16:32:13
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
				$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	
	public function main()
	{
		$this->Title = '上门服务订单详情';

		include handler('template')->file('@m/shangmen_checkout');	
		
	}
	


}
?>