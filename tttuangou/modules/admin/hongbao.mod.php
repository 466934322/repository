<?php
/**
 * 模块：红包功能
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package hongbao.mod.php
 * @version $Id$
 */
class ModuleObject extends MasterObject {

	public function ModuleObject( $config ) {
		$this->MasterObject($config);

		if($config['api_version'] < 1 || false == file_exists(INCLUDE_PATH . 'api/func/loader.php')) {
			$this->Messager('该功能仅对企业永久版（企业金牌套餐、企业至尊套餐）用户开放，如需使用，可联系客服咨询升级套餐版本事宜。天天微信客服，关注微信账号“tttuangou”', null);
		}

		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function Main() {
		$this->CheckAdminPrivs('hongbao');

		$data = logic('hongbao')->get();

		include handler('template')->file('@admin/hongbao');
	}

	public function save() {
		$this->CheckAdminPrivs('hongbao');
		
		if(false != ($ret = logic('hongbao')->save(post('data')))) {
			$this->Messager($ret);
		} else {
			$this->Messager('设置成功', 'admin.php?mod=hongbao');
		}
	}

	
	public function log() {
		;
	}

}