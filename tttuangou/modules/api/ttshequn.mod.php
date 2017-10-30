<?php

/**
 * 模块：默认功能区
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo<moyo@cenwor.com>
 * @package module
 * @name index.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	private $params;
	private $appInfo;

	public function ModuleObject( $config )
	{
		$this->MasterObject($config);

		$this->_init_env();

		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function main() {
		$this->output('API TTSHEQUN IS OK');
	}

	public function generate() {
		;
	}

	private function _init_env() {
		if(!function_exists('apim')) {
			$this->error('API ERROR : NOT SUPPORTED');
		}
		if(true !== UCENTER) {
			$this->error('UCENTER NOT SUPPORTED');
		}
	}

	private function _init_input() {
		$this->params = ($_POST['params'] ? $_POST['params'] : $_GET['params']);
		if(empty($this->params)) {
			$this->error('params empty');
		}

		;
	}

	/* 内容输出 */
	private function output($data, $status = 'ok', $errcode = 0, $errdata = '') {
		$output = array(
				'status' => $status,
			);
		if($data) {
			$output['data'] = $data;
		}
		if($errcode) {
			$output['errcode'] = (string) $errcode;
		}
		if($errdata) {
			$output['errdata'] = $errdata;
		}
		if('error' == $status) {
			errorlog($output, 'api_ttshequn_output_error');
		}
		$output = array_iconv($this->Config['charset'], 'utf-8', $output);
		header('Content-type: application/json');
		echo json_encode($output);
	}
	private function error($errdata, $errcode = -1, $data = array()) {
		$this->output($data, 'error', $errcode, $errdata);
		exit;
	}
}