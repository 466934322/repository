<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name shangmen_seller_order.mod.php
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
		$this->Title = '上门订单管理';

		include handler('template')->file('@m/shangmen_seller_order');	
		
	}
	


}
?>