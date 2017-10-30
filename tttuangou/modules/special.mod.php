<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name special.mod.php
 * @date 2015-04-21 16:43:55
 */
 


class ModuleObject extends MasterObject {

	public function ModuleObject( $config ) {
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function Main() {
		exit('special is ok');
	}

	public function view() {
		$id = get('id', 'int');

		$special = logic('special')->get_one($id);
		if('link' == $special['type']) {
			$this->Messager(null, $special['link']);
		} elseif ('product' == $special['type']) {
			if($special['product_count'] > 0) {
				$special['count'] = $this->_count($special['product_count']);
				$length = $special['count']['perpage'];
				$offset = ($special['count']['pagenow'] - 1) * $length;
				$special['product'] = logic('special')->get_product($special, $offset, $length);
			}
		}

		$this->Title = $special['name'];
		include template('special_view');
	}

	private function _count($total) {
		page_moyo($total);
		return page_moyo_summary();
	}

}