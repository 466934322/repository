<?php
/**
 * 模块：专题功能
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package special.mod.php
 * @version $Id$
 */
class ModuleObject extends MasterObject {

	public function ModuleObject( $config ) {
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function Main() {
		$this->CheckAdminPrivs('special');

		$list = logic('special')->get_all(false);

		include handler('template')->file('@admin/special');
	}

	public function add() {
		$this->CheckAdminPrivs('special');

		$name = post('name', 'txt');
		if(empty($name)) {
			$name = get('name', 'txt');
		}
		if(empty($name)) {
			$this->Messager('专题名称不能为空');
		}
		$ret = logic('special')->add($name);
		if(is_numeric($ret) && $ret > 0) {
			$this->Messager('专题添加成功，现在为您转入编辑页面', 'admin.php?mod=special&code=set&id=' . $ret);
		} else {
			$this->Messager($ret);
		}
	}

	public function del() {
		$this->CheckAdminPrivs('special');

		$id = get('id', 'int');
		if($id < 1) {
			$this->Messager('请指定一个专题ID');
		}
		logic('special')->del($id);
		$this->Messager('删除成功', 'admin.php?mod=special');
	}

	public function set() {
		$this->CheckAdminPrivs('special');

		$id = get('id', 'int');
		if($id < 1) {
			$id = post('id', 'int');
		}
		if($id < 1) {
			$this->Messager('请指定一个专题ID');
		}
		$one = logic('special')->get_one($id);
		if(false == $one) {
			$this->Messager('专题已经不存在了');
		}
		if(post('dosubmit')) {
			$data = post('one');
			if('product' == $data['type']) {
				$data['settings']['product'] = $this->_product_reorder($data['settings']['product']);
			}
			if('seller' == $data['type']) {
				$data['settings']['seller'] = $this->_seller_reorder($data['settings']['seller']);
			}
			if(isset($_FILES['picfile']) && is_array($_FILES['picfile']) && $_FILES['picfile']['name']) {
				$data['pic'] = UPLOAD_PATH . 'special/'.$id.'.{$EXT}';
				$r = logic('upload')->save('picfile', $data['pic']);
				if($r['error']) {
					$this->Messager($r['msg']);
				} else {
					$data['pic'] = $r['path'];
				}
			}
			logic('special')->set($data, $id);
			$this->Messager('设置成功了', 'admin.php?mod=special');
		}

		include handler('template')->file('@admin/special_set');
	}

	public function product_Ajax() {
		$this->CheckAdminPrivs('special');

		$id = post('id', 'int');
		if($id < 1) {
			exit('专题ID不能为空');
		}
		$one = logic('special')->get_one($id);
		if(false == $one) {
			exit('专题已经不存在了');
		}
		$act = post('act', 'txt');
		$method = '_product_' . $act;
		if($act && method_exists($this, $method)) {
			echo $this->$method($one);
		} else {
			exit('act is invalid');
		}
		exit;
	}
	private function _product_search($one) {
		$name = post('name', 'txt');
		if(empty($name)) {
			return '请输入搜索关键词';
		}
		$time = time();
		$query = dbc()->query("select `id`, `name`, `intro`, `flag`, `begintime`, `status`, `successnum`, `type`, `virtualnum` from "
				. table('product')."
			where
				saveHandler = 'normal' AND "
									  ."(`name` like '%{$name}%' OR `intro` like '%{$name}%' OR `flag` like '%{$name}%')
			order by `order` DESC, `id` desc limit 50");

		$html = '';
		while(false != ($row = $query->GetRow())) {
			if ($row['begintime'] > time()) {
				$statusTxt = '即将开团，开团时间<br />'. date('Y-m-d H:i:s', $row['begintime']);
			}
			else {
				$statusTxt = logic('product')->STA_Name($row['status']);
				if ($row['status'] == PRO_STA_Normal) {
					if ($row['type'] == 'prize') {
						$succ_real = logic('prize')->sigCount('pid='. $row['id']);
					}
					else {
						$succ_real = logic('product')->BuyersCount($row['id']);
					}
					$succ_buyers = $succ_real + $row['virtualnum'];
					$succ_remain = $row['successnum'] - $succ_buyers;
					$statusTxt .= '<br/>'. $row['successnum'].' 人成团，差 '. $succ_remain .' 人';
				}
			}
			$html .= "<div><label><input onclick=\"product_search_onclick('{$row[id]}')\" type=\"checkbox\" id=\"product_search_{$row[id]}\" value=\"{$row[id]}\" /> &nbsp; <span id=\"product_search_name_{$row[id]}\" data-status=\"{$statusTxt}\">{$row['name']}</span></label></div>";
		}
		return ($html ? $html : '没有搜索到任何产品信息，请换个关键词重新进行搜索！');
	}
	private function _product_add($one) {
		$pid = post('pid', 'int');
		$order = post('order', 'int');
		$this->_product_settings($one, $pid, $order, 'add');
	}
	private function _product_del($one) {
		$pid = post('pid', 'int');
		$this->_product_settings($one, $pid, 0, 'del');
	}
	private function _product_edit($one) {
		$pid = post('pid', 'int');
		$order = post('order', 'int');
		$this->_product_settings($one, $pid, $order, 'edit');
	}
	private function _product_settings($one, $pid, $order, $act) {
		$product = $one['settings']['product'];
		$pn = logic('product')->GetOne($pid);
		if(false == $pn) {
			return ;
		}
		settype($product, 'array');
		foreach($product as $k=>$row) {
			if($pid == $row['id']) {
				unset($product[$k]);
			}
		}
		if('del' != $act) {
			$product[] = array('id'=>$pid, 'order'=>$order, 'name'=>$pn['name'], 'status'=>$pn['status']);
		}
		$one['settings']['product'] = $this->_product_reorder($product);
		logic('special')->set(array('settings'=>$one['settings']), $one['id']);
	}
	private function _product_reorder($product) {
			if (!empty($product) && is_array($product)) {
			usort($product, create_function('$a,$b', 'return ($a[order]==$b[order]?0:($a[order]<$b[order]?1:-1));'));
		}
		return $product;
	}

	public function seller_Ajax() {
		$this->CheckAdminPrivs('special');

		$id = post('id', 'int');
		if($id < 1) {
			exit('专题ID不能为空');
		}
		$one = logic('special')->get_one($id);
		if(false == $one) {
			exit('专题已经不存在了');
		}
		$act = post('act', 'txt');
		$method = '_seller_' . $act;
		if($act && method_exists($this, $method)) {
			echo $this->$method($one);
		} else {
			exit('act is invalid');
		}
		exit;
	}
	private function _seller_search($one) {
		$name = post('name', 'txt');
		if(empty($name)) {
			return '请输入搜索关键词';
		}
		$time = time();
		$query = dbc()->query("select `id`, `sellername` AS `name`, `display_order` AS `order` from "
				. table('seller')."
			where
				`enabled` = 'true' AND "
			  ."(`sellername` like '%{$name}%' OR `selleraddress` like '%{$name}%')
			order by `display_order` DESC, `id` desc limit 50");

		$html = '';
		while(false != ($row = $query->GetRow())) {
			$html .= "<div><label><input onclick=\"seller_search_onclick('{$row[id]}')\" type=\"checkbox\" id=\"seller_search_{$row[id]}\" value=\"{$row[id]}\" /> &nbsp; <span id=\"seller_search_name_{$row[id]}\" data-status=\"{$statusTxt}\">{$row['name']}</span></label></div>";
					}
		return ($html ? $html : '没有搜索到任何商家信息，请换个关键词重新进行搜索！');
	}
	private function _seller_add($one) {
		$pid = post('pid', 'int');
		$order = post('order', 'int');
		$this->_seller_settings($one, $pid, $order, 'add');
	}
	private function _seller_del($one) {
		$pid = post('pid', 'int');
		$this->_seller_settings($one, $pid, 0, 'del');
	}
	private function _seller_edit($one) {
		$pid = post('pid', 'int');
		$order = post('order', 'int');
		$this->_seller_settings($one, $pid, $order, 'edit');
	}
	private function _seller_settings($one, $pid, $order, $act) {
		$seller = $one['settings']['seller'];
		$pn = logic('seller')->GetOne($pid);
		if(false == $pn) {
			return ;
		}
		settype($seller, 'array');
		foreach($seller as $k=>$row) {
			if($pid == $row['id']) {
				unset($seller[$k]);
			}
		}
		if('del' != $act) {
			$row = array('id'=>$pid, 'order'=>$order, 'name'=>$pn['sellername'], 'status'=>$pn['enabled']);
			$seller[] = $row;
					}
		$one['settings']['seller'] = $this->_seller_reorder($seller);
		logic('special')->set(array('settings'=>$one['settings']), $one['id']);
	}
	private function _seller_reorder($seller) {
		if (!empty($seller) && is_array($seller)) {
			usort($seller, create_function('$a,$b', 'return ($a[order]==$b[order]?0:($a[order]<$b[order]?1:-1));'));
		}
		return $seller;
	}

}