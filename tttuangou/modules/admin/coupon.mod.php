<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name coupon.mod.php
 * @date 2015-11-12 16:33:06
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
        $this->CheckAdminPrivs('coupon');
		header('Location: ?mod=coupon&code=vlist');
    }
    function vList()
    {
    	$this->CheckAdminPrivs('coupon');
		if(isset($_GET['coupsta'])){
			$coupSTA = get('coupsta', 'int');
		}else{
			$coupSTA = TICK_STA_ANY;
		}
		$fpids = '';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$fpids = 0;
			if($pids){
				$fpids = implode(',',$pids);
			}
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $fpids = logic('product')->manage_area_product(MEMBER_ID);
            }
        }
        $list = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
        include handler('template')->file('@admin/coupon_list');
    }
    function Add()
    {
        $this->CheckAdminPrivs('coupon');
		$uid = get('uid', 'int');
        $pid = get('pid', 'int');
        $oid = get('oid', 'number');
        include handler('template')->file('@admin/coupon_add');
    }
    function Add_save()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$uid = get('uid', 'int');
        $pid = get('pid', 'int');
        $oid = get('oid', 'number');
        $number = get('number', 'number');
        if (!$number || strlen($number) != 9) $number = false;
        $password = get('password', 'number');
        if (!$password || strlen($password) != 3) $password = false;
        $mutis = get('mutis', 'int');
        if (!$mutis) $mutis = 1;
        logic('coupon')->Create($pid, $oid, $uid, $mutis, $number, $password);
        exit('ok');
    }
    function Alert()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        $c = logic('coupon')->GetOne($id);
        logic('notify')->Call($c['uid'], 'admin.mod.coupon.Alert', $c);
        exit('ok');
    }
    function Reissue()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        $c = logic('coupon')->SrcOne($id);
        $uid = $c['uid'];
        $data = array
        (
            'uid' => $c['uid'],
            'productid' => $c['productid'],
        	'orderid' => $c['orderid'],
    		'number' => $c['number'],
    		'password' => $c['password'],
            'mutis' => $c['mutis'],
			'status' => $c['status']
        );
        logic('coupon')->Create_OK($uid, $data);
        exit('ok');
    }
    function Delete()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        logic('coupon')->Delete($id);
        exit('ok');
    }
    function Config()
    {
        $this->CheckAdminPrivs('coupon');
		include handler('template')->file('@admin/coupon_config');
    }
	private function doforbidden($ticketid){
		$return = false;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$tinfo = dbc(DBCMax)->query('select productid from '.table('ticket')." where ticketid='".$ticketid."'")->limit(1)->done();
			if(!in_array($tinfo['productid'],$pids)){
				$return = true;
			}
		}
		return $return;
	}
}

?>