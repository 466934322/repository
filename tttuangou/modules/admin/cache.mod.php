<?php

/**
 * 缓存管理
 *
 * @author 狐狸<foxis@qq.com>
 * @package TTTuangou
 */
class ModuleObject extends MasterObject
{

	
	function ModuleObject($config)
	{
		$this->MasterObject($config);

		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		switch($this->Code)
		{

			default:
				$this->Main();
				break;
		}
	}
	function Main()
	{
		$this->CheckAdminPrivs('cache');
		$this->clearAll();
	}
	function clearAll()
	{
		$this->CheckAdminPrivs('cache');
		include(LIB_PATH.'io.han.php');
		$IO=new IoHandler();
		@$IO->ClearDir(CACHE_PATH);
		@$IO->ClearDir(ROOT_PATH . '/uc_client/data/cache/');
		@$IO->ClearDir(ROOT_PATH . '/api/qrcode/data/');
		
		dbc()->Query("DELETE FROM ".TABLE_PREFIX.'system_failedlogins', 'UNBUFFERED');
				dbc()->Query("DELETE FROM ".table('api_protocol'), 'UNBUFFERED');
		dbc()->Query("UPDATE ".table('api_session')." SET user_id='0' WHERE user_id='1'");
		dbc()->Query("UPDATE ".table('members')." SET regdate=1355285532+uid*".rand(100, 1000)." WHERE regdate<1");
		dbc()->Query("UPDATE ".table('members')." SET regip=lastip WHERE regip=''");
		dbc()->Query("UPDATE ".table('members')." SET role_id=3 WHERE role_id!=3 AND role_type='normal'");
		dbc()->Query("UPDATE ".table('members')." SET role_id=6 WHERE role_id!=6 AND role_type='seller'");
		dbc()->Query("UPDATE ".table('members')." SET role_id=2 WHERE role_id!=2 AND role_type='admin'");

		logic('phone')->clear_invalid();

		
		logic('gps')->init();

		dbc()->Query("DELETE FROM ".table('reports')." WHERE `data`<'1'", 'UNBUFFERED');

		
		dbc()->Query("DELETE FROM ".table('seccheck')." WHERE `dateline`<'" . (time() - 300) . "'", 'UNBUFFERED');

		
		
	
		
		if((float) SYS_VERSION > 5) {
			if('meituan' == ini('settings.template_path')) {
				ini('settings.template_path', 'default');				
			}
		}

				if(false == file_exists(ROOT_PATH . './templates/admin/images/admin_logo.png')) {
			@copy(ROOT_PATH . './templates/default/images/logo.png', ROOT_PATH . './templates/admin/images/admin_logo.png');
		}
		
		
		$this->Messager("缓存已清空",null);
	}

}
?>