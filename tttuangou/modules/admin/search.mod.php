<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name search.mod.php
 * @date 2015-04-21 16:43:55
 */
 




class ModuleObject extends MasterObject
{
	var $keyword = '';

	function ModuleObject($config)
	{
		$this->MasterObject($config);

		$this->_init_keyword();

		$this->Execute();
	}

	
	function Execute()
	{
		ob_start();
		switch ($this->Code)
        {
        	case 'menu':
        		$this->Menu();
        		break;
        	case 'menu_click':
        		$this->MenuClick();
        		break;

			default:
				$this->Code = 'index';
                $this->Main();
		}
		$body=ob_get_clean();

		$this->ShowBody($body);
	}

	function Main()
	{
		;
	}

	function Menu()
	{
		if(!$this->keyword) {
			$this->Messager('请指定一个搜索关键词', null);
		}

		$kws = explode(' ', $this->keyword);

		$search_admin_menu_index = ini('search_admin_menu_index');
		$search_admin_menu_click = ini('search_admin_menu_click');

		if(!$search_admin_menu_index)
		{
			$this->Messager('配置不存在了', null);
		}

		$search_index = $search_admin_menu_index;
		if(!$search_admin_menu_click || count($search_admin_menu_click) != count($search_admin_menu_index))
		{
			$search_admin_menu_click = array_fill(0, count($search_index), 0);

			ini('search_admin_menu_click', $search_admin_menu_click);
		}
		$search_list = $search_admin_menu_click;


		$search = 0;
		foreach($search_index as $k=>$vs)
		{
			if(!$search_index[$k]['search'])
			{
				foreach($vs['text'] as $text)
				{
					foreach ($kws as $kw)
					{
						if(false !== stripos($text, $kw))
						{
							$search_index[$k]['search'] = 1;

							$search += 1;

							continue 3;
						}
					}
				}
			}
		}


		$iii = 0;
		$search_result = array();
		if($search)
		{
			foreach($search_list as $id=>$v)
			{
				if($search_index[$id]['search'])
				{
					$title = $url = '';
					foreach ($search_index[$id]['index'] as $ik=>$iv)
					{
						$title = $ik;
											}

					$text = implode(" ", $search_index[$id]['text']);

					$title = $this->_highlight($title, $kws);
					$text = $this->_highlight($text, $kws);

					$row = array(
						'url' => "admin.php?mod=search&code=menu_click&id=".$id."&highlight=".urlencode($this->keyword),
						'title' => ++$iii . '、' . $title,
						'text' => $text,
					);

					$search_result[] = $row;
				}
			}
		}


		include template('@admin/search_menu');
	}

	function MenuClick()
	{
		$search_admin_menu_index = ini('search_admin_menu_index');
		$search_admin_menu_click = ini('search_admin_menu_click');

		$id = (int) $this->Get['id'];

		if(!isset($search_admin_menu_index[$id]))
		{
			$this->Messager('请指定一个正确的地址', null);
		}

		$search_admin_menu_click[$id] += 1;

		arsort($search_admin_menu_click);

		ini('search_admin_menu_click', $search_admin_menu_click);


		$url = current($search_admin_menu_index[$id]['index']);


		$this->Messager(null, $url);
	}

	function _init_keyword()
	{
		$keyword = ($this->Post['keyword'] ? $this->Post['keyword'] : $this->Get['keyword']);
		$keyword = trim(strip_tags($keyword));

		$this->keyword = $keyword;
	}

	function _highlight($str, $kws)
	{
		$kws = (array) $kws;

		$search = $replace = array();
		foreach($kws as $kw)
		{
			$search[$kw] = $kw;
			$replace[$kw] = "<font color='red'>{$kw}</font>";
		}

		$str = str_replace($search, $replace, $str);

		return $str;
	}


}


?>
