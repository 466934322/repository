<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name catalog.mod.php
 * @date 2016-04-12 18:33:12
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
		$this->CheckAdminPrivs('catalog');
		$catalog = logic('catalog')->Navigate(2);
		include handler('template')->file('@admin/catalog_list');
	}
	function Add_ajax()
	{
		$this->CheckAdminPrivs('catalog','ajax');
		$parent = get('parent', 'int');
		$parent || $parent = 0;
		$parent && $master = logic('catalog')->GetOne($parent);
		$master['parent'] > 0 && $master = false;
		include handler('template')->file('@admin/catalog_add_ajax');
	}
	function Add_save()
	{
		$this->CheckAdminPrivs('catalog','ajax');
		$parent = post('parent', 'int');
		$parent || $parent = 0;
		$flag = post('flag', 'text');
		$name = post('name', 'text');
		$id = -1;
		if (preg_match('/^[a-z0-9]+$/i', $flag) && $flag && $name)
		{
			$id = logic('catalog')->Add($parent, $flag, $name);
		}
		exit('<script type="text/javascript">var op = window.opener ? window.opener : window.parent; op.__catalog_add_finish('.$id.');</script>');
	}
	function Del_ajax()
	{
		$this->CheckAdminPrivs('catalog','ajax');
		$id = get('id', 'int');
		$id || exit('false');
		logic('catalog')->Delete($id);
		exit('ok');
	}
    function Goodscate_del_ajax()
    {
        $this->CheckAdminPrivs('catalog','ajax');
        $id = get('id', 'int');
        $id || exit('false');
        logic('catalog')->Goodscate_delete($id);
        exit('ok');
    }
	function Subclass_list_ajax()
	{
		$parent = get('parent', 'int');
		$category = get('category', 'int');
		$list = logic('catalog')->GetList($parent);
		foreach ($list as $i => $one)
		{
			$extend = '';
			if ($one['id'] == $category)
			{
				$extend = ' selected="selected"';
			}
			echo '<option value="'.$one['id'].'"'.$extend.'>'.$one['name'].'</option>';
		}
	}

	function delImages()
	{
	    $this->CheckAdminPrivs('catalog');
	    $id = get('id', 'int');
	    $r = $this->_delImages($id);
	    echo json_encode($r);
	}
	
			private function _delImages($id)
	{
	    try
	    {
	        $uploads_id = ini('catalog.icon.'.$id.'.uploads_id');
	        logic('upload')->delete($uploads_id);
	        ini('catalog.icon.'.$id.'.icon', '');
	        ini('catalog.icon.'.$id.'.uploads_id', INI_DELETE);
	        $r = array('error'=>0,'reason'=>'');
	    }
	    catch (Exception $e)
	    {
	        $r = array('error'=>1,'reason'=>$e->getMessage());
	    }
	    return $r;
	}
	
        function edit()
    {
    	$this->CheckAdminPrivs('catalog');
		$id = post('id', 'int');
    	if ($id> 0) {
    		$data = ini('catalog.icon');
	        $script = post('script', 'string');
	        $result = logic('upload')->Save('catalog_image', false, false);
	        if(isset($result['error']))
	        {
	           ini('catalog.icon.'.$id.'.script', $script);
	        }
	        else
	        {
	            	            $this->_delImages($id);
	            $data[$id] = array('icon'=>$result['url'],'script'=>$script,'uploads_id'=>$result['id']);
	            ini('catalog.icon', $data);
	        }
	        $this->Messager('分类配置已经更新！', '?mod=catalog');
    	}else{
    		$id = get('id', 'int');
    		$data = ini('catalog.icon');
			if($data && is_array($data)){
    		foreach ($data as $key => $value) {
    			if ($key != $id) {
    				unset($data[$key]);
    			}
    			$data[$key]['script'] = stripcslashes($value['script']);
    		}
			}
    		include handler('template')->file('@admin/catalog_edit');
    	}

    }
    
    function Hot_ajax() {
    	$this->CheckAdminPrivs('catalog','ajax');
		$id = get('id', 'int');
		$info = logic('catalog')->GetOne($id);
		
		$data = ini('catalog.icon');

		include handler('template')->file('@admin/catalog_hot_ajax');
    }

    function Hot_save() {
    	$this->CheckAdminPrivs('catalog','ajax');
		$id = post('id', 'int');
				$hot = (post('hot', 'int') ? 1 : 0);
		$new = array(
			'hot' => $hot,
			'fontcolor' => post('fontcolor', 'string'),
			'hotorder' => post('hotorder', 'int'),
		);
		$r = logic('catalog')->hot_save($id, $new);	

		if(isset($_FILES['catalog_image']) && $_FILES['catalog_image']['name']) {
			$data = ini('catalog.icon');
	        $result = logic('upload')->Save('catalog_image', false, false);
	        if(false == $result['error'])
	        {
	        			        $this->_delImages($id);
		        $data[$id]['icon'] = $result['url'];
		        $data[$id]['uploads_id'] = $result['uploads_id'];
		        ini('catalog.icon', $data);
	        }
	    }
		
		exit('<script type="text/javascript">var op = window.opener ? window.opener : window.parent; op.__catalog_hot_finish("'.$id.'", "'.$hot.'");</script>');
    }

        public function goodscate_edit(){
        $this->CheckAdminPrivs('catalog');
        $id = get('id', 'int');
        $action = '?mod=catalog&code=goodscate_edit';
        if ($id > 0) {
            $goodscate = dbc(DBCMax)->select('goods_catalog')->where('id='.$id)->limit(1)->done();
        }else{
                        $data['name'] = post('name');
            $data['flag'] = post('flag');
            $data['order'] = post('order','int');
            $id = post('id','int');
            $rs = dbc(DBCMax)->update('goods_catalog')->data($data)->where('id='.$id)->done();
            if($rs){
                $this->Messager('商品分类更新完成！', '?mod=creditmall&code=goods_category');
            }
        }
        include handler('template')->file('@admin/goods_catalog_edit');
    }

        public function goodscate_add(){
        $this->CheckAdminPrivs('catalog');
        $action = '?mod=catalog&code=goodscate_add';
        if(post('name')){
            $data['name'] = post('name');
            $data['flag'] = post('flag');
            $data['order'] = post('order','int');
            $rs = dbc(DBCMax)->insert('goods_catalog')->data($data)->done();
            if($rs){
                $this->Messager('商品分类添加完成！', '?mod=creditmall&code=goods_category');
            }
        }

        include handler('template')->file('@admin/goods_catalog_edit');
    }
}
?>