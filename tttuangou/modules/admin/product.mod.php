<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name product.mod.php
 * @date 2016-08-16 17:36:22
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
		$this->CheckAdminPrivs('product');
		header('Location: ?mod=product&code=vlist');
	}
	function vList()
	{
		$this->CheckAdminPrivs('product');
		logic('product')->Maintain();
				$orderSql = '';
		if(isset($_GET['prodorder'])) {
			
			$prodorder = get('prodorder' , 'int');
			(!is_numeric($prodorder)) && $prodorder = 0;

			switch ($prodorder) {
				case PRODUCT_ORDER_BY_DEFAULT:											break;
				case PRODUCT_ORDER_BY_BEGINTIME_DESC:   					$orderSql .= ' p.begintime DESC,';
				break;
				case PRODUCT_ORDER_BY_OVERTIME_DESC:							$orderSql .= ' p.addtime DESC,';
				break;
				case PRODUCT_ORDER_BY_SURPLUS_ASC:						$orderSql .= ' surplus_order ASC,';								break;
				case PRODUCT_ORDER_BY_TOTALSALES_DESC:						$orderSql .= ' totalsales_order DESC,';							break;
				default:
									break;
			}
		}
		 
				$whereSql = '1';
				if(isset($_GET['prosta'])){
			$prosta = get('prosta', 'int');
			
			if (is_numeric($prosta)) {
				switch ($prosta) {
					case PRO_STA_Failed:						case PRO_STA_Normal:						case PRO_STA_Success:   					case PRO_STA_Finish:    					case PRO_STA_Refund:    						$whereSql .= ' AND p.status='.$prosta;
					break;
					case PRO_STA_WillExpire:							$whereSql .= ' AND p.overtime >= ' . time();
						$orderSql .= ' p.overtime ASC,';
						break;
					case PRO_STA_Expired:								$whereSql .= ' AND (p.overtime < ' . time() . ' and p.overtime > 0)';
						$orderSql .= ' p.overtime DESC,';
						break;
					case PRO_STA_Alarm:									$whereSql .= ' AND (overtime >= '. time() .' or overtime < 1) AND p.maxnum > p.sells_count AND p.maxnum - p.sells_count < 10';
						break;
					case PRO_STA_Recommend:									$whereSql .= ' AND `hotenabled` = "true" ';
						break;
					default:
												break;
				}
			}
		}
		$orderSql .= ' p.order DESC, p.id DESC';				
				if(isset($_GET['prodsp'])){
			$prodsp = get('prodsp', 'int');
			is_numeric($prodsp) && $whereSql .= ' AND p.display='.$prodsp;
		}
				if(isset($_GET['catalogFirst'])) {
			$catalogFirst = get('catalogFirst', 'int');
			is_numeric($catalogFirst) && $whereSql .= ' AND c1.id=' . $catalogFirst;
			
			if(isset($_GET['catalogSecond'])) {
				$catalogSecond = get('catalogSecond', 'int');
				is_numeric($catalogSecond) && $whereSql .= ' AND c2.id=' . $catalogSecond;
			}
		}
				if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$whereSql .= ' AND p.id IN('.$asql.')';
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $asql = logic('product')->manage_area_product(MEMBER_ID);
                $whereSql .= ' AND p.id IN('.$asql.')';
            }
        }
		$list = logic('product')->getList_forAdmin($whereSql, $orderSql);
		logic('product')->AVParser($list);
				$drfCount = logic('product')->GetDraftCount();
                if('admin' == MEMBER_ROLE_TYPE){
            $admin = 1;
        }
				include handler('template')->file('@admin/product_list');
	}
	function Add()
	{
		$this->CheckAdminPrivs('product');
		$p = array();
		$p['successnum'] = ini('product.default_successnum');
		$p['virtualnum'] = ini('product.default_virtualnum');
		$p['oncemax'] = ini('product.default_oncemax');
		$p['oncemin'] = ini('product.default_oncemin');
		$p['fundprice'] = '-1';
		$p['multibuy'] = 'true';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sinfo = dbc(DBCMax)->query('select id,area from '.table('seller')." where userid='".MEMBER_ID."'")->limit(1)->done();
			if($sinfo){
				$p['sellerid'] = (int)$sinfo['id'];
				$p['city'] = (int)$sinfo['area'];
			}else{
				$this->Messager('您还没有录入商家信息，无法添加产品，请联系网站管理员！');
			}
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
            $city = dbc(DBCMax)->query('select cityid,cityname from '.table('city')." where cityid='".$smember['area']."'")->limit(1)->done();
            $region = dbc(DBCMax)->query('select id,name from '.table('city_place')." where id='".$smember['city_place_region']."'")->limit(1)->done();
                        $streets = dbc(DBCMax)->query('select id,name from '.table('city_place')." where parent_id='".$smember['city_place_region']."'")->done();
            if($smember['area']){
                if($smember['city_place_region']){
                    $cor_sellers = dbc(DBCMax)->query('select id,sellername from '.table('seller')." where area='".$smember['area']."' and city_place_region='".$smember['city_place_region']."'")->done();
                }else{
                                        $cor_sellers = dbc(DBCMax)->query('select id,sellername from '.table('seller')." where area='".$smember['area']."'")->done();
                }
            }
                        if($smember['area']){
                $manage_area = 1;
            }else{
                $manage_area = 0;
            }
        }
		include handler('template')->file('@admin/product_mgr');
	}
	function Add_image()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$id = get('id', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		$p = logic('product')->SrcOne($pid);
		$imgs = explode(',', $p['img']);
		foreach ($imgs as $i => $iid)
		{
			if ($iid == '' || $iid == 0)
			{
				unset($imgs[$i]);
			}
		}
		$imgs[] = $id;
		$new = implode(',', $imgs);
		logic('product')->Update($pid, array('img'=>$new));
		exit('ok');
	}
	function Edit()
	{
		$this->CheckAdminPrivs('product');
		$id = get('id', 'int');
		$did = get('draftID', 'int');
		$queryID = $did ? $did : $id;
		if($this->doforbidden($queryID)){
			$this->Messager('您不可操作该产品！');
		}
        if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
            $city = dbc(DBCMax)->query('select cityid,cityname from '.table('city')." where cityid='".$smember['area']."'")->limit(1)->done();
            $region = dbc(DBCMax)->query('select id,name from '.table('city_place')." where id='".$smember['city_place_region']."'")->limit(1)->done();
                                    $streets = dbc(DBCMax)->query('select id,name from '.table('city_place')." where parent_id='".$smember['city_place_region']."'")->done();
            if($smember['area']){
                $manage_area = 1;
                if($smember['city_place_region']){
                    $cor_sellers = dbc(DBCMax)->query('select id,sellername from '.table('seller')." where area='".$smember['area']."' and city_place_region='".$smember['city_place_region']."'")->done();
                }else{
                                        $cor_sellers = dbc(DBCMax)->query('select id,sellername from '.table('seller')." where area='".$smember['area']."'")->done();
                }
            }else{
                $manage_area = 0;
            }
        }
		$p = logic('product')->GetOne($queryID);
        $needknow = unserialize($p['needknow']);
        $tc = unserialize($p['tc']);
        $tc_count = count($tc) ? count($tc) : 0;
		$p || exit('PID Invalid');
		$p['id'] = $id;
		$draft = logic('product')->CheckProductDraft($id);
		include handler('template')->file('@admin/product_mgr');
	}
	function Save()
	{
		$this->CheckAdminPrivs('product');
		$data = array();
		$data['name'] = post('name', 'txt');
		$data['flag'] = post('flag', 'txt');
		$data['flag'] || $data['flag'] = $data['name'];
		$data['intro'] = post('intro');		$data['city'] = post('city', 'int');
		$data['sellerid'] = post('sellerid', 'int');
		$data['nowprice'] = max(0, post('nowprice', 'float'));
		if('admin' == MEMBER_ROLE_TYPE) {
			$data['display'] = post('display', 'int');
			$data['order'] = post('order', 'int');
			$data['promo_cut'] = max(0, post('promo_cut', 'float'));
			$data['newbie_cut'] = max(0, post('newbie_cut', 'float'));
			$data['client_cut'] = max(0, post('client_cut', 'float'));
			$data['limit_level'] = post('limit_level', 'int');
			$data['rebate_money'] = post('rebate_money', 'float');
			$data['rebate_limit'] = post('rebate_limit', 'int');
			$data['rebate_day'] = post('rebate_day', 'int');
			$data['last_rebate_day'] = post('rebate_day', 'int');
            $data['share_rebate_money'] = post('share_rebate_money', 'float');
                        if($data['share_rebate_money'] > $data['nowprice']){
                $this->Messager('分享返利金额不能大于团购价', -1);
            }
            $data['role_rebate_user'] = post('role_rebate_user', 'int');
			$data['score'] = max(0,post('score', 'int'));
			if(isset($_POST['fundprice']) && is_numeric($_POST['fundprice']) && $_POST['fundprice'] >=0){
				$data['fundprice'] = post('fundprice', 'float');
			}else{
				$data['fundprice'] = -1;
			}

			if($data['rebate_money'] > $data['nowprice']) {
				$this->Messager('购买返现金额不能大于团购价', -1);
			}
			if($data['promo_cut'] > $data['nowprice'] || 
				$data['newbie_cut'] > $data['nowprice'] || 
				$data['client_cut'] > $data['nowprice']) {
					$this->Messager('优惠立减金额不能大于团购价', -1);
			}
						$data['sub_sellerid'] = (post('sub_sellerid') ? implode(',', post('sub_sellerid')) : '');
		}
		$data['content'] = post('content');
		$data['cue'] = post('cue');
		$data['theysay'] = post('theysay');
		$data['wesay'] = post('wesay');
		$data['price'] = max(0, post('price', 'float'));
		$data['maxnum'] = max(0, post('maxnum', 'int'));
		$data['begintime'] = strtotime(post('begintime'));
		$data['overtime'] = post('overtime')?strtotime(post('overtime')):0;
		$data['type'] = post('type', 'txt');
		$data['perioddate'] = strtotime(post('perioddate'));
		$data['allinone'] = post('allinone', 'txt');
		$data['weight'] = post('weight', 'float');
		$data['weight'] *= (post('weightunit', 'txt') == 'g') ? 1 : 1000;
		$data['successnum'] = post('successnum', 'int');
		$data['successnum'] < 1 && $data['successnum'] = 1;
		$data['virtualnum'] = post('virtualnum', 'int');
		$data['oncemax'] = max(0, post('oncemax', 'int'));
		$data['oncemin'] = max(1, post('oncemin', 'int'));
		$data['multibuy'] = post('multibuy', 'txt');
		$data['is_countdown'] = post('is_countdown', 'int');
        $needknow = post('needknow');
        $needknow = array_filter($needknow,create_function('$needknow','return !empty($needknow);'));
        $tc = post('tc');
        if($tc){
            foreach($tc as $key=>$val){
                if(empty($val)){
                    continue;
                }
                $tc[$key]= $val;
            }
        }
        $data['needknow'] = serialize($needknow);
        $data['tc'] = serialize($tc);
				$data['yungou'] = 0;
		$isYunGou = post('isYunGou');
		if ($isYunGou && $isYunGou[0] == 'yes') {
			$data['yungou'] = YUNGOU_STA_Normal;
		}
				$data['saveHandler'] = post('saveHandler', 'txt') == 'draft' ? 'draft' : 'normal';
		$isDraft = $data['saveHandler'] == 'draft';
		$draftID = post('draftID', 'int');
		$data['draft'] = $isDraft ? $draftID : 0;
				$noNULL = ($isDraft ?  array() : array(
			'name' => '产品名',
			'price' => '产品原价',
			'nowprice' => '产品现价',
		));
		if($noNULL && 'admin' == MEMBER_ROLE_TYPE) {
			$noNULL = array_merge($noNULL , array(
				'city' => '产品投放城市',
				'sellerid' => '产品所属商家',
			));
		}
		foreach ($noNULL as $key => $name)
		{
			if ($key == 'nowprice' && is_numeric($data[$key])) continue;
			if (!$data[$key])
			{
				$this->Messager('【'.$name.'】不能为空！', -1);
			}
		}
				if (post('imgs') != '')
		{
			$data['img'] = substr(post('imgs', 'txt'), 0, -1);
		}
				logic('catalog')->ProUpdate($data);
		
		logic('city')->product_on_save($data);
				if ($data['type'] == 'prize')
		{
						$data['successnum'] = $data['successnum'] > $data['virtualnum'] ? $data['virtualnum'] : $data['successnum'];
						$data['multibuy'] = 'false';
		}
				if ($data['yungou']) {
						$data['successnum'] = $data['successnum'] > $data['virtualnum'] ? $data['virtualnum'] : $data['successnum'];
						$data['overtime'] = strtotime('2038-1-1');
						if ($data['type'] == 'prize') {
				$this->Messager('启用云购模式时，“团购类型”不能为“抽奖”！', -1);
			}
						if (post('presell_is')) {
				$_POST['presell_is'] = $_GET['presell_is'] = false;					}
						$data['allinone'] = 'false';
						if ($data['maxnum'] <= 0) {
				$this->Messager('启用云购模式时，“云购总份数”必于大于0', -1);
			}
			else if ($data['virtualnum'] > $data['maxnum']) {
				$this->Messager('启用云购模式时，“虚拟已购买人数”必于小于等于云购总份数', -1);
			}
			else if ($data['oncemax'] > $data['maxnum']) {
				$this->Messager('启用云购模式时，“一次最多购买数”必于小于等于云购总份数', -1);
			}
			else if ($data['oncemin'] > $data['maxnum']) {
				$this->Messager('启用云购模式时，“一次最少购买数”必于小于等于云购总份数', -1);
			}
		}
				$id = post('id', 'int');
		if ($id == 0)
		{
			$data['addtime'] = time();
			$data['status'] = PRO_STA_Normal;
			$id = logic('product')->Publish($data);
						if($id > 0) {
				logic('product_tag')->save($id, post('tag_ids'));
			}
		}
		else
		{
						if ($data['yungou']) {
				$data['successnum'] = $data['successnum'] > $data['virtualnum'] ? $data['virtualnum'] : $data['successnum'];
				unset($data['virtualnum']);
				unset($data['overtime']);
				unset($data['yungou']);
			}
			
			if($this->doforbidden($id)){
				$this->Messager('您不可操作该产品！');
			}
						$data['@extra'] = array(
				'category' => post('__catalog_subclass', 'int') > 0 ? post('__catalog_subclass', 'int') : post('__catalog_topclass', 'int'),
				'hideseller' => post('hideseller', 'txt'),
				'irebates' => post('irebates', 'txt'),
				'expresslist' => post('expresslist'),
				'specialPayment' => post('specialPayment', 'txt'),
				'specialPaymentSel' => post('specialPaymentSel') ? (implode(',', post('specialPaymentSel')).',') : ''
			);
			$allow2CSaveHandler = logic('product')->allowCSaveHandler($id, $data['saveHandler']);
			if ($allow2CSaveHandler)
			{
				logic('product')->Update($id, $data);
			}
			else
			{
				zlog('product')->saveError($id, '非法的草稿保存请求！');
				$alert = '保存失败！! 草稿源数据已被删除或者非法的草稿保存请求！';
				if ($isDraft)
				{
					exit(jsonEncode(array('status'=>'failed','msg'=> $alert)));
				}
				{
					$this->Messager($alert, -1);
				}
			}
		}
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sinfo = dbc(DBCMax)->query('select id,area from '.table('seller')." where userid='".MEMBER_ID."'")->limit(1)->done();
			$data['sellerid'] = (int)$sinfo['id'];
			$data['city'] = (int)$sinfo['area'];
		}		
				$hideSeller = post('hideseller', 'txt');
		if ($hideSeller == 'true')
		{
			meta('p_hs_'.$id, 'yes');
		}
		else
		{
			meta('p_hs_'.$id, null);
		}
				$inviteRebates = post('irebates', 'txt');
		if ($inviteRebates == 'true')
		{
			meta('p_ir_'.$id, 'yes');
		}
		else
		{
			meta('p_ir_'.$id, null);
		}
				if (post('expresslist', 'trim') != '')
		{
			meta('expresslist_of_'.$id, post('expresslist'));
		}
		else
		{
			meta('expresslist_of_'.$id, null);
		}
				$specialPayment = post('specialPayment', 'txt');
		if ($specialPayment == 'true')
		{
			$paymentSel = post('specialPaymentSel');
			if ($paymentSel)
			{
				$listString = '';
				foreach ($paymentSel as $i => $pCode)
				{
					$listString .= $pCode.',';
				}
				meta('paymentlist_of_'.$id, $listString);
			}
		}
		else
		{
			meta('paymentlist_of_'.$id, null);
		}
				$notifyType = post('notifyType', 'txt');
		if ($notifyType != '-1')
		{
			if (ini('notify.api.'.$notifyType.'.enabled'))
			{
				meta('p_nt_'.$id, $notifyType);
			}
			else
			{
				meta('p_nt_'.$id, null);
			}
		}
		else
		{
			meta('p_nt_'.$id, null);
		}
				$not_allow_refund = (post('not_allow_refund', 'int') > 0 ? 1 : 0);
		if($not_allow_refund) {
			meta('p_nar_'.$id, $not_allow_refund);
		} else {
			meta('p_nar_'.$id, null);
		}
		if('admin' == MEMBER_ROLE_TYPE) {
						logic('product')->PresellSubmit($id);
		}
				logic('attrs')->ProductSubmit($id);
				logic('gps')->seller_linker($data['sellerid']);
				$isDraft || logic('product')->Maintain($id);
		$isDraft && exit(jsonEncode(array('status'=>'ok','pid'=>$id)));
		logic('product')->ClearDraft($id, $draftID);
		$this->Messager('产品数据更新完成！', '?mod=product&code=vlist');
	}
	function Save_intro()
	{
		$this->CheckAdminPrivs('product','ajax');
		$id = get('id', 'int');
		$intro = get('intro', 'txt');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('upload')->Field($id, 'intro', $intro);
		exit('ok');
	}
    function Ajax_save_sort(){
        if('admin' == MEMBER_ROLE_TYPE){
            $id   = (int)$_GET['pid'];
            $sort = (int)$_GET['sort'];
            $sql  = "update ".table('product')." set `order`='$sort' where `id`='$id'";
            if(dbc()->Query($sql)){
                echo 1;
            }else{
                echo 0;
            }
        }
    }
	function Draft_restore()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$did = get('did', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		logic('product')->ClearDraft($pid, $did, $did);
		exit('admin.php?mod=product&code=edit&id='.$pid.'&draftID='.$did.'&~iiframe=yes');
	}
	function Draft_list()
	{
		$this->CheckAdminPrivs('product');
		$list = logic('product')->GetDraftList();
		include handler('template')->file('@admin/product_draft_list');
	}
	function Draft_del()
	{
		$this->CheckAdminPrivs('product');
		$this->Draft_clear(false);
		$this->Messager('已经删除！');
	}
	function Draft_clear($exit = true)
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$did = get('did', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		logic('product')->ClearDraft($pid, $did);
		$exit && exit('ok');
	}
	function Del()
	{
		$this->CheckAdminPrivs('product');
		if('admin' != MEMBER_ROLE_TYPE) {
			$this->Messager('只有管理员才有权限执行此操作');
		}
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			$this->Messager('您不可操作该产品！');
		}
		logic('product')->Delete($id);
		$this->Messager('产品成功删除！', '?mod=product&code=vlist');
	}
	function Del_image()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$id = get('id', 'int');
		$p = logic('product')->SrcOne($pid);
		if ($p['img'] == '')
		{
						logic('upload')->Delete($id);
		}
		else
		{
			$imgs = explode(',', $p['img']);
			foreach ($imgs as $i => $iid)
			{
				if ($iid == $id)
				{
					logic('upload')->Delete($id);
					unset($imgs[$i]);
				}
			}
			$new = implode(',', $imgs);
			logic('product')->Update($pid, array('img'=>$new));
		}
		exit('ok');
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
	function Quick_addCity()
	{
		$this->CheckAdminPrivs('city','ajax');
		$name = get('name', 'txt');
		$flag = get('flag', 'txt');
		$data = array(
			'cityname' => $name,
			'shorthand' => $flag,
			'display' => 1
		);
		dbc()->SetTable(table('city'));
		$r = dbc()->Insert($data);
		exit($r ? (string)$r : '添加失败！');
	}
	function Quick_addSeller()
	{
		$this->CheckAdminPrivs('seller','ajax');
				$username = get('username', 'txt');
		if(empty($username)) {
			exit('用户名不能为空');
		}
		$password = '123456';
		$rr = logic('seller')->Register($username, $password);
		$rr['error'] && exit($rr['result']);
		$uid = $rr['result'];
				$city = get('city', 'int');
		$sellername = get('sellername', 'txt');
		if($uid < 1 || $city < 1 || empty($sellername)) {
			exit('商家名不能为空');
		}
		$sid = logic('seller')->Add($city, $uid, $sellername);
				exit($sid ? (string)$sid : '添加失败！');
	}
	private function doforbidden($productid){
		$return = false;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			if(!in_array($productid,$pids)){
				$return = true;
			}
		}
		return $return;
	}

	function linklist()
	{
		$this->CheckAdminPrivs('product');
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sellerid = logic('seller')->U2SID(MEMBER_ID);
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $sellerid = logic('seller')->area_seller($smember['area']);
                            }
        }
		$list = logic('product')->get_link_list($sellerid);
		include handler('template')->file('@admin/product_link_list');
	}

	function addlink()
	{
		$this->CheckAdminPrivs('product');
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sellerid = logic('seller')->U2SID(MEMBER_ID);
		}else{
			$step = 'seller';
		}

                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $area = $smember['area'];
                $citys = dbc(DBCMax)->select('city')->where(array('cityid' => $area))->done();
            }else{
                if($step == 'seller'){
                    $citys = dbc(DBCMax)->query("SELECT cityid,cityname FROM `".table('city')."` WHERE cityid IN(SELECT DISTINCT `area` FROM ".table('seller').")")->done();
                }
            }
        }
		if(post('sidpost')){
			$sellerid = post('sidpost');
			$step = '';
		}
		if($sellerid){
			$list = logic('product')->GetOwnerLink($sellerid);
		}
		include handler('template')->file('@admin/product_link');
	}

	function editlink()
	{
		$this->CheckAdminPrivs('product');
		$linkid = get('id','int');
		$linkinfo = logic('product')->get_link_product($linkid);
		if($linkinfo){
			$sellerid = $linkinfo['sellerid'];
			if(MEMBER_ROLE_TYPE == 'seller' && $sellerid != logic('seller')->U2SID(MEMBER_ID)){
				$this->Messager('您没有权限进行该操作！', '?mod=product&code=linklist');
			}
		}else{
			$this->Messager('该套餐不存在！', '?mod=product&code=linklist');
		}
		if($sellerid){
			$list = logic('product')->GetOwnerLink($sellerid);
		}
		include handler('template')->file('@admin/product_link');
	}

	function addlinksave()
	{
		$this->CheckAdminPrivs('product');
		$data = array();
		$linkid = max(0, post('linkid','int'));
		$sellerid = max(0, post('sellerid','int'));
		$link_product_ids = post('link_product_ids');
		$link_product_names = post('link_product_names');
		if($sellerid && $link_product_ids && $link_product_names){
			foreach($link_product_ids as $key => $val){
				if($val && trim($link_product_names[$key])){
					$data[] = array('pid'=>$val,'name'=>$link_product_names[$key]);
				}
			}
		}
		if(count($data) > 1){
			if($linkid > 0){
				$return = logic('product')->updatelink($linkid,$data);
				$this->Messager('套餐编辑成功！', '?mod=product&code=linklist');
			}else{
				$return = logic('product')->linksave($sellerid,$data);
				$this->Messager('套餐添加成功！', '?mod=product&code=linklist');
			}
		}else{
			$this->Messager('套餐数据不符合要求，添加失败！', '?mod=product&code=linklist');
		}
	}

	function dellink()
	{
		$this->CheckAdminPrivs('product');
		$id = get('id','int');
		if(logic('product')->check_link_byid($id)){
			logic('product')->deletelink($id);
			$this->Messager('套餐删除成功！', '?mod=product&code=linklist');
		}else{
			$this->Messager('您没有权限进行操作！', '?mod=product&code=linklist');
		}
	}
}

?>