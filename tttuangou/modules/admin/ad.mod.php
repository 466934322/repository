<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ad.mod.php
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
	function Main()
	{
		$this->CheckAdminPrivs('adset');
		header('Location: ?mod=ad&code=vlist');
	}
	function vlist()
	{
		$this->CheckAdminPrivs('adset');
				$list = ini('ad');
				$local = $list_local = $this->local_list();
                if(ini('settings.template_path') != 'default_pro'){
            unset($list['howdnew']);
            unset($local['howdnew']);
            unset($list_local['howdnew']);
        }
		include handler('template')->file('@admin/ad_list');
	}
	public function install()
	{
		$this->CheckAdminPrivs('adset');
		$flag = get('flag', 'txt');
		$list_local = $this->local_list();
		if (!isset($list_local[$flag]))
		{
			$this->Messager('不可识别的广告标记，系统无法进行安装！', '?mod=ad&code=vlist');
		}
				$list_ini = ini('ad');
		if (is_array($list_ini[$flag]))
		{
			$this->Messager('此广告已经安装过了！', '?mod=ad&code=vlist');
		}
				ini('ad.'.$flag, $list_local[$flag]);
		$this->Messager('安装成功！', '?mod=ad&code=vlist');
	}
	function Config()
	{
		$this->CheckAdminPrivs('adset');
		$flag = get('flag', 'txt');
		$cfg = ini('ad.'.$flag.'.config');
                $city_arr = dbc(DBCMax)->select('city')->where(array('display'=>1))->done();
		include handler('template')->file('@html/ad/'.$flag.'.config');
	}
	function Config_save()
	{
		$this->CheckAdminPrivs('adset');
		$flag = get('flag', 'txt');
		$data = post('data');
		$extParse_file = handler('template')->TemplateRootPath.'html/ad/'.$flag.'.function.php';
		if (is_file($extParse_file))
		{
			include $extParse_file;
			$extParse_func = 'ad_config_save_parser_'.$flag;
			function_exists($extParse_func) && $extParse_func($data);
		}
		$olddata = ini('ad.'.$flag.'.config');
		if (count($olddata['list']) != count($data['list']) || $data['fu'])
		{
			$keeps = array();
			$fappends = array();
			foreach ($data['list'] as $id => $cfg)
			{
				if (isset($olddata['list'][$id]))
				{
					$keeps[$id] = $cfg;
				}
				else
				{
										$fappends[$id] = $cfg;
				}
			}
			if ($fappends)
			{
				if (count($fappends) > 1)
				{
					$fappends = array_reverse($fappends, true);
				}
				$data['list'] = array_merge($fappends, $keeps);
			}
			if (isset($data['fu'])) unset($data['fu']);
		}
		ini('ad.'.$flag.'.config', $data);
		$this->Messager('配置已经更新！', '?mod=ad&code=vlist');
	}
	private function Config_link($flag)
	{
		$cfgFile = handler('template')->TemplateRootPath.'html/ad/'.$flag.'.config.html';
		if (!is_file($cfgFile))
		{
			return '<font title="此接口不需要设置">设置</font>';
		}
		else
		{
			return '<a href="?mod=ad&code=config&flag='.$flag.'">设置</a>';
		}
	}
	private $ad_local_list = null;
	private function local_list()
	{
		if (is_null($this->ad_local_list))
		{
			$list_local = array();
			$local_file = handler('template')->TemplateRootPath.'html/ad/ad.list.php';
			if (is_file($local_file))
			{
				$list_local = include $local_file;
			}
			$this->ad_local_list = $list_local;
		}
		return $this->ad_local_list;
	}

    function Quick_listCity()
    {
        $cid = get('icity', int);
        $list = array(
            array(
                'cityid' => 0,
                'cityname' => '请选择城市',
                'shorthand' => '__#__'
            )
        );
        $list = array_merge($list, logic('misc')->CityList());
        foreach ($list as $i => $one)
        {
            $sel = '';
            if ($one['cityid'] == $cid)
            {
                $sel = ' selected="selected"';
            }
            echo '<option value="'.$one['cityid'].'"'.$sel.'>'.$one['cityname'].'</option>';
        }
        exit;
    }
}

?>