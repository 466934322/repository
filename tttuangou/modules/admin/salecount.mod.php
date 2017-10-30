<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name salecount.mod.php
 * @date 2015-09-11 16:54:02
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
		$this->CheckAdminPrivs('salecount');
		include handler('template')->file('@admin/salecount_main');
	}
	public function product()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_product();
		include handler('template')->file('@admin/salecount_product');
	}
	public function payment()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_payment();
		include handler('template')->file('@admin/salecount_payment');
	}
	public function user()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_user();
		
		include handler('template')->file('@admin/salecount_user');
	}
	public function fund()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_fund('1', get('data_fix'));
		$data_need_fix = (logic('salecount')->data_need_fix);
		include handler('template')->file('@admin/salecount_fund');
	}
}
?>