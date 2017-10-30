<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name catalog.ui.php
 * @date 2016-05-25 16:51:50
 */





class CatalogUI
{
    public $tpl_suffix = '';

    public function __construct() {
        if('m' == WEB_BASE_ENV_DFS::$APPNAME) {
            $this->tpl_suffix = '.m';
        } else {
            $this->tpl_suffix = '';
        }
    }

    
    public function display($meituannav = 0)
    {
        if (!logic('catalog')->Enabled()) return;
                $catalog = logic('catalog')->Navigate($meituannav);
                if (logic('catalog')->FilterEnabled())
        {
            foreach ($catalog as $_i => $_topclass)
            {
                if (isset($_topclass['oslcount']))
                {
                    $_topclass['subclass'] || $_topclass['subclass'] = array();
                    $subprocount = 0;
                    foreach ($_topclass['subclass'] as $_ii => $_subclass)
                    {
                        if (isset($_subclass['oslcount']))
                        {
                            if ($_subclass['oslcount'] == 0)
                            {
                                unset($_topclass['subclass'][$_ii]);
                                continue;
                            }
                            $subprocount += $_subclass['oslcount'];
                        }
                    }
                    if ($subprocount == 0)
                    {
                        unset($catalog[$_i]);
                    }
                    else
                    {
                        $catalog[$_i] = $_topclass;
                    }
                }
            }
        }
        include handler('template')->file('@html/catalog/navigate' . $this->tpl_suffix);
    }
    public function seller_display()
    {
        if (!logic('catalog')->Enabled()) return;
                $catalog = logic('catalog')->seller_navigate();

        include handler('template')->file('@html/catalog/navigate' . $this->tpl_suffix);
    }
    public function hot_display($limit = 0)
    {
    	if (!logic('catalog')->Enabled()) return ;
    	                $catalog = logic('catalog')->hot($limit);
        $cate_num = count($catalog);
                if(empty($catalog)) return ;
                $display = $cate_num < 10 ? 1 : 2;
        if($display == 2 && $cate_num >= 10){
            $catalog2 = logic('catalog')->hot2();
                    }
        include handler('template')->file('@html/catalog/hot_navigate' . $this->tpl_suffix);
    }
    public function inputer($category)
    {
        $category || $category = 0;
        $category && $master = logic('catalog')->GetOne($category);
        $catalog = logic('catalog')->Navigate(2);
        include handler('template')->file('@html/catalog/inputer' . $this->tpl_suffix);
    }
    public function home_inputer()
    {
        $catalog = logic('catalog')->Navigate(2);
        include handler('template')->file('@html/catalog/home_inputer' . $this->tpl_suffix);
    }
    public function tree($category)
	{
		$category || $category = 1;
		$treeList = array();
		while( $category > 0){
			$catalog = logic('catalog')->GetOne($category);
			$arr = array(
				'title' => $catalog['name'],
				'url' => logic('url')->create('catalog', array('code' => $catalog['flag'])),
			);
			$treeList[] = '<a href="'. $arr['url'] .'">'. $arr['title'] .'</a>';
			$category = $catalog['parent'];
		}
		krsort($treeList);
		echo implode( ' &gt;&gt; ', $treeList );
	}
}
?>