<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name isearcher.ui.php
 * @date 2015-08-31 16:32:13
 */





class iSearcherUI
{
    
    public function load($idx)
    {
		        $map = ini('isearcher.map');
        $fidString = ini('isearcher.idx.'.$idx);
        $fids = explode(',', $fidString);
		        $filter = ini('isearcher.filter');
        $ffsString = ini('isearcher.frc.'.$idx);
        $frcs = explode(',', $ffsString);
				$timev = ini('isearcher.timev');
		$tvString = ini('isearcher.tvs.'.$idx);
		$tvss = explode(',', $tvString);
				$tvinputs = array();
		foreach ($tvss as $tvsk)
		{
			if (isset($_GET['iscp_tvbegin_'.$tvsk]))
			{
				$tvinputs[$tvsk]['begin'] = get('iscp_tvbegin_'.$tvsk, 'txt');
			}
			if (isset($_GET['iscp_tvfinish_'.$tvsk]))
			{
				$tvinputs[$tvsk]['finish'] = get('iscp_tvfinish_'.$tvsk, 'txt');
			}
		}
		$iscp_input_value = ($_GET['iscp_input_value'] ? $_GET['iscp_input_value'] : $_POST['iscp_input_value']);
		
		
		
		 if ($_GET['mod'] == 'product' && (!isset($_GET['code']) || preg_match('/^vlist$/i', trim($_GET['code']))) ) {
						$catalogFirstFilter = array(
				'name' => '分类显示',
				'key'  => 'catalogFirst',
				'list' => array(),
			);
			$result = logic('catalog')->GetList();
			
							if (!empty($result)) {
				foreach ($result as $catalog) {
					$catalogFirstFilter['list'][$catalog['id']] =  $catalog['name'];
				}
			}
						$catalogSecondFilter = array(
				'name' => '',
				'key'  => 'catalogSecond',
				'list' => array(),
			);
			if(isset($_GET['catalogFirst'])){
				$catalogFirst = get('catalogFirst', 'int');
				if (is_numeric($catalogFirst)) {
					$result = logic('catalog')->GetList($catalogFirst);
					foreach($result as $catalog) {
						$catalogSecondFilter['list'][$catalog['id']] =  $catalog['name'];
					}
				}
			}
						array_splice($frcs, -1, 0, array('product_catalogFirst', 'product_catalogSecond'));
	
			$filter = array_merge($filter, array('product_catalogFirst' => $catalogFirstFilter, 
										'product_catalogSecond' => $catalogSecondFilter));
		 }
									
		        include handler('template')->file('@html/isearcher/index');
    }
}

?>