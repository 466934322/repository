<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name seller_join.mod.php
 * @date 2016-05-13 14:14:48
 */





class ModuleObject extends MasterObject
{
	private $uid = 0;
	private $sid = 0;

	private function iniz()
	{
		$this->uid = user()->get('id');
		if ($this->uid < 0)
		{
			$this->Messager('请先登录！', '?mod=account&code=login');
		}
		if(!$this->Config['selleropen']){
			$this->Messager('网站未开启商家自动申请功能！', 'index.php');
		}
		$this->sid = logic('seller')->U2SID($this->uid);
		if ($this->sid > 0)
		{
			$this->Messager('您已经是商家或者已经申请过商家了，请不要重复申请！', '?mod=seller');
		}
	}
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		
		$runCode = Load::moduleCode($this);

		$this->$runCode();
	}
	public function main()
	{
		header('Location: '.rewrite('?mod=seller_join&code=info'));
	}

	public function info()
	{
        $this->iniz();
        
		$rebate = logic('rebate')->Get_Rebate_setting();
		$profit_pre = 0;
		$profit_id = 0;
		$city = logic('misc')->CityList();
		include handler('template')->file('seller_join_table');
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
    
    public function place_ajaxlist()
    {
        $type = get('type', 'string');
        $id = get('id', 'int');
        $datas = logic('city')->get_of_parent($type, $id);
        $html = '<option value="0">全部</option>';
        foreach ($datas as $data)
        {
            $html .= '<option value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        exit($html);
    }

	function addmap(){
		extract($this->Get);
		extract($this->Post);
        $x = $y = $z = 0;
        if($id) {
            list($x, $y, $z) = explode(',', $id);
        }
        if(empty($x) || empty($y)) {
                        $x = '116.395645';
            $y = '39.929986';
            $z = 4;
        }

        if($z < 1) {
            $z = 16;
        }
        include(handler('template')->file('@admin/tttuangou_baidumap'));
	}
	function save(){
        $this->iniz();
        
		$fields = array('area','city_place_region','city_place_street','category','sellername','selleraddress','sellerphone','sellerurl','price_avg','trade_time','content','sellermap','profit_id');
		$data = array();
		foreach($fields as $f){
            if($f == 'category'){
                $data[$f] = $_POST['__catalog_subclass'] > 0 ? (int)$_POST['__catalog_subclass'] : (int)$_POST['__catalog_topclass'];
            }else{
                $data[$f] = $_POST[$f];
            }
		}
		$data['userid'] = user()->get('id');
				if (isset($_FILES['id_card']['name']) && $_FILES['id_card']['error'] == 0){
			$data['id_card'] = 'uploads/images/seller/idcard/'.$data['userid'].'_'.time().'.gif';
			logic('upload')->Save('id_card', ROOT_PATH.$data['id_card']);
		}
		if (isset($_FILES['zhizhao']['name']) && $_FILES['zhizhao']['error'] == 0){
			$data['zhizhao'] = 'uploads/images/seller/zhizhao/'.$data['userid'].'_'.time().'.gif';
			logic('upload')->Save('zhizhao', ROOT_PATH.$data['zhizhao']);
		}		
		$sid = logic('seller')->Join($data);
		if (!$sid) $this->Messager('提交失败！请重试', -1);
		$this->Messager('申请成功', '?mod=seller');
	}
}
?>