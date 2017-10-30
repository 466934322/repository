<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name getseller.mod.php
 * @date 2016-04-12 18:33:12
 */


class ModuleObject extends MasterObject
{
	var $Config = array(); 	var $ID;

	function ModuleObject(& $config){
		$this->MasterObject($config);
		$this->initMemberHandler();
		$this->ID=$this->Post['id']?(int)$this->Post['id']:(int)$this->Get['id'];
		Load::moduleCode($this);$this->Execute();
	}

	function Execute(){
		switch ($this->Code){
			case 'linkproduct':
				$this->Linkproduct();
				break;
			case 'getupseller':
				$this->getupseller();
				break;
			case 'getsubseller':
				$this->getsubseller();
				break;
			case 'getprosubseller':
				$this->getprosubseller();
				break;
			case 'getprosubsellerplace':
				$this->getprosubsellerplace();
				break;
			default:
				$this->Showseller();
				break;
		}
	}

		function getprosubsellerplace() {
		$pid = get('pid', 'int');

		$where = " `enabled`='true' ";

		$sids = logic('product')->get_sub_sellerids($pid);
		$where .= " AND `id` IN ('" . implode("','", $sids) . "') ";

		$region = get('region', 'int');
		if($region > 0) {
			$where .= " AND `city_place_region`='{$region}' ";
		}

		$sql = "SELECT `city_place_region` AS `nid`, `city_place_street` AS `tid` FROM " . table('seller') . " WHERE {$where}";
		$list = dbc()->Query($sql)->GetAll();
		$regions = $streets = array();
		foreach($list as $rs) {
			$regions[$rs['nid']] = $rs['nid'];
			$streets[$rs['tid']] = $rs['tid'];
		}

		$cpids = array();
		if($region > 0) {
			$cpids = $streets;
		} else {
			$cpids = $regions;
		}

		$sql = "SELECT * FROM ".table('city_place')." WHERE `id` IN ('".implode("','", $cpids)."') ";
		$places = dbc()->Query($sql)->GetAll();
		$html = '<option value="0">全部</option>';
		foreach($places as $data) {
			$html .= '<option value="'.$data['id'].'">'.$data['name'].'</option>';
		}
		exit($html);
	}

	function getprosubseller() {
		page_moyo_max_io(3); 		$pid = get('pid', 'int');

		$where = " `enabled`='true' ";

		$sids = logic('product')->get_sub_sellerids($pid);
		$where .= " AND `id` IN ('" . implode("','", $sids) . "') ";

		$region = get('region', 'int');
		if($region > 0) {
			$where .= " AND `city_place_region`='{$region}' ";
		}
		$street = get('street', 'int');
		if($street > 0) {
			$where .= " AND `city_place_street`='{$street}' ";
		}

		$sql = "SELECT * FROM " . table('seller') . " WHERE {$where} ORDER BY `display_order` DESC";
		$sql = page_moyo($sql);
		$list = dbc()->Query($sql)->GetAll();
		if(empty($list)){echo __('暂无商家');exit;}
		
		include template('biz_wrapper_body');
		exit;
	}

	function getsubseller() {
		$seller = get('seller', 'int');
		$pid = get('pid', 'int');
		$sql = "SELECT `id`, `sellername` FROM " . table('seller') . " WHERE `enabled`='true' AND `up_sellerid`='$seller' ORDER BY `id` ASC";
		$list = dbc()->Query($sql)->GetAll();
		if(empty($list)){echo __('暂无商家');exit;}

		$sub_sellerid = array();
		if($pid > 0) {
			$product = logic('product')->SrcOne($pid);
			$sub_sellerid = explode(',', $product['sub_sellerid']);
		}

		$HTML = '';
		foreach($list as $row) {
			$HTML .= '<label for="sub_sellerid_' . $row['id'] . '"><input type="checkbox" id="sub_sellerid_' . $row['id'] . '" name="sub_sellerid[]" value="' . $row['id'] . '"' . (in_array($row['id'], $sub_sellerid) ? ' checked="checked" ' : '') . ' /> ' . $row['id'] . '、' . $row['sellername'] . '</label> &nbsp; ';
		}
		exit($HTML);
	}

	function getupseller() {
		$area = get('area', 'int');
		$seller = get('seller', 'int');
		$sql = "SELECT `id`, `sellername` FROM " . table('seller') . " WHERE `enabled`='true' AND `area`='$area' AND `up_sellerid`='0' ORDER BY `id` ASC";
		$list = dbc()->Query($sql)->GetAll();
		if(empty($list)){echo __('暂无商家');exit;}
		$HTML = '<select name="up_sellerid">';
		$HTML .= '<option value="0">我是总店（没有分店）</option>';
		foreach($list as $row) {
			if(get('sid', 'int') != $row['id']) {
				$HTML .= '<option value="' . $row['id'] . '"' . ($seller == $row['id'] ? ' selected="selected" ' : '') . '>' . $row['id'] . '、' . $row['sellername'] . '</option>';
			}
		}
		$HTML .= '</select>';
		exit($HTML);
	}

	function Showseller(){
		$id=$this->Get['city'];
		$sql='SELECT `id`, `sellername` FROM '.TABLE_PREFIX.'tttuangou_seller where `enabled` = "true" and `area` = '.intval($id);
        $query = dbc()->Query($sql);
		$seller=$query->GetAll();
		if(empty($seller)){echo __('暂无商家');exit;}
		echo '<select name="sellerid" id="sellerid">';
		foreach($seller as $i => $value){
			echo '<option value="'.$value['id'].'"';
			if ($_GET['seller'] == $value['id'])
			{
			    echo ' selected="selected"';
			}
			echo '>'.$value['sellername'].'</option>';
		}
		echo ' </select>';
		exit;
	}

	function Linkproduct(){
		$html = '';
		$id = $this->Get['city'];
		if($id > 0){
			$sellers = dbc(DBCMax)->query("SELECT id,sellername FROM `".table('seller')."` WHERE `enabled`='true' AND `area` ='".intval($id)."'")->done();
		}
		if($sellers){
			foreach($sellers as $k => $v){
				$html .= '<option value="'.$v['id'].'">'.$v['sellername'].'</option>';
			}
		}else{
			$html .= '<option value="">请选择...</option>';
		}
		echo $html;
		exit;
	}
}
?>