<?php

/**
 * 逻辑区：专题管理
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package special.logic.php
 * @version $Id$
 */

class SpecialLogic {

	public function add($name) {
		if(false != ($one = self::get_one($name, 1))) {
			return $one['id'];
		}
		$data = array('name'=>$name, 'time'=>time(), 'display_order'=>'99', 'status'=>'0');
		$id = dbc(DBCMax)->insert('special')->data($data)->done();
		$this->_list_cache_clean();
		return $id;
	}

	public function del($id) {
		$id = (int) $id;
		if($id < 1) {
			return ;
		}
		$one = self::get_one($id);
		if(false == $one) {
			return ;
		}
		dbc(DBCMax)->delete('special')->where(array('id' => $id))->done();
		$this->_list_cache_clean();
	}

	public function set($data, $id = 0) {
		$id = ($id ? $id : $data['id']);
		$id = (int) $id;
		if($id < 1) {
			return ;
		}
		$one = self::get_one($id);
		if(false == $one) {
			return ;
		}
		if(isset($data['settings'])) {
			$data['settings'] = serialize($data['settings']);
		}
		unset($data['link'], $data['product_count']);
		if($data) {
			dbc(DBCMax)->update('special')->data($data)->where(array('id'=>$id))->done();
		}
		$this->_list_cache_clean();
	}

	public function get_one($id, $is_name = false) {
		$key = '';
		$val = '';
		if($is_name) {
			$key = 'name';
			$val = strip_tags($id);
		} else {
			$key = 'id';
			$val = max(0, (int) $id);
		}
		if($key && $val) {
			$one = dbc(DBCMax)->select('special')->where(array($key=>$val))->limit(1)->done();
			if($one) {
				$one['settings'] = unserialize($one['settings']);

							if (!empty($one['settings']['product'])) {
					foreach ($one['settings']['product'] as $idx => $p) {
						if ($p['id'] > 0) {
							$query = dbc()->query('select `begintime`, `status`, `successnum`, `type`, `virtualnum` from '
													. table('product').' where id = '. $p['id']);
							if (false != ($row = $query->GetRow())) {

								$one['settings']['product'][$idx]['begintime'] = $row['begintime'];
								$one['settings']['product'][$idx]['status'] = $row['status'];

								if ($row['status'] == PRO_STA_Normal) {
									if ($row['type'] == 'prize') {
										$succ_real = logic('prize') -> sigCount('pid='. $p['id']);
									}
									else {
										$succ_real = logic('product') -> BuyersCount($p['id']);
									}
									$succ_buyers = $succ_real + $row['virtualnum'];
									$succ_remain = $row['successnum'] - $succ_buyers;

									$one['settings']['product'][$idx]['succ_total'] = $row['successnum'];
									$one['settings']['product'][$idx]['succ_remain'] = $succ_remain;
								}
							}
						}
					}
				}
				$one['product_count'] = count($one['settings']['product']);
								if(!empty($one['settings']['seller'])) {
					foreach ($one['settings']['seller'] as $idx => $p) {
						if ($p['id'] > 0) {
							$query = dbc()->query('select `sellername`, `selleraddress`, `sellerphone`, `price_avg` from '
													. table('seller').' where id = '. $p['id']);
							if (false != ($row = $query->GetRow())) {
								$one['settings']['seller'][$idx]['sellername'] = $row['sellername'];
								$one['settings']['seller'][$idx]['selleraddress'] = $row['selleraddress'];
								$one['settings']['seller'][$idx]['sellerphone'] = $row['sellerphone'];
								$one['settings']['seller'][$idx]['price_avg'] = $row['price_avg'];
							}
						}
					}
				}
				$one['seller_count'] = count($one['settings']['seller']);
				$one['link'] = $one['settings']['link'];
				if($one['pic']) {
					$one['pic'] = ini('settings.site_url').str_replace('./', '/', $one['pic']);
				} else {
					$one['pic'] = ini('settings.site_url').'/static/images/special.png';
				}
			}
			return $one;
		} else {
			return false;
		}
	}

	public function get_all($filter = true, $limit = 0) {
		$rets = array();
		$key = "special/list_all";
		if(false === ($rets = fcache($key, 3600))) {
			$rets = dbc(DBCMax)->select('special')->order(' `display_order` DESC, `id` DESC ')->done();
			fcache($key, $rets);
		}
				if ((true == $filter) && !empty($rets)) {
				foreach($rets as $rk=>$r) {
				if(false == $r['status']) {
					unset($rets[$rk]);
				} else {
					$rets[$rk]['settings'] = unserialize($r['settings']);
					$rets[$rk]['link'] = $rets[$rk]['settings']['link'];
					if($rets[$rk]['pic']) {
						$rets[$rk]['pic'] = ini('settings.site_url').str_replace('./', '/', $rets[$rk]['pic']);
					} else {
						$rets[$rk]['pic'] = ini('settings.site_url').'/static/images/special.png';
					}
					if('seller' == $r['type']) {
						$oneSeller = current($rets[$rk]['settings']['seller']);
						$rets[$rk]['seller_id'] = (int) $oneSeller['id'];
					}
				}
			}
		}
		if($limit > 0 && $limit < count($_rets)) {
			$_rets = array();
			foreach($rets as $rk=>$r) {
				if(count($_rets) < $limit) {
					$_rets[] = $r;
				}
			}
			$rets = $_rets;
			unset($_rets);
		}
				return (empty($rets) ? $rets : array_values($rets));
		}

	public function get_product($one, $offset=0, $length=0) {
		if(is_numeric($one)) {
			$one = self::get_one($one);
		}
		if($one['id'] < 1) {
			return array();
		}
		if('product' != $one['type']) {
			return array();
		}
		if(is_array($one['settings']['product'])) {
			$key = 'special/list_' . $one['id'];
			if(false === ($list = fcache($key, 600))) {
				foreach($one['settings']['product'] as $row) {
					$list[] = logic('product')->GetOne($row['id']);
				}
				fcache($key, $list);
			}
		}
		if($list) {
			if($length > 0) {
				return array_slice($list, $offset, $length);
			} else {
				return $list;
			}
		} else {
			return array();
		}
	}

	public function get_seller($one, $offset=0, $length=0) {
		if(is_numeric($one)) {
			$one = self::get_one($one);
		}
		if($one['id'] < 1) {
			return array();
		}
		if('seller' != $one['type']) {
			return array();
		}
		if(is_array($one['settings']['seller'])) {
			$key = 'special/list_' . $one['id'];
			if(false === ($list = fcache($key, 600))) {
				foreach($one['settings']['seller'] as $row) {
					$one = logic('seller')->GetOne($row['id']);
					$one['imgs'] = ($one['imgs'] != '') ? explode(',', $one['imgs']) : array();
					$one['img'] = (isset($one['imgs'][0]) ? $one['imgs'][0] : '');
					$list[] = $one;
				}
				fcache($key, $list);
			}
		}
		if($list) {
			if($length > 0) {
				return array_slice($list, $offset, $length);
			} else {
				return $list;
			}
		} else {
			return array();
		}
	}

	private function _list_cache_clean($id = 0) {
		fcache("special/list_all", 0);
		fcache("special/list_{$id}", 0);
		handler('io')->ClearDir(CACHE_PATH . 'fcache/special/');
	}

}