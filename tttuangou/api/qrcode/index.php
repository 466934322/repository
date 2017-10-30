<?php

/**
 * QR Code 在线生成
 *
 * @author 狐狸<foxis@qq.com>
 * @version $Id$
 */

error_reporting(E_ERROR);

function mmkdir($dir, $mode = 0777) {
	if(!is_dir($dir)) {
		clearstatcache();
		mmkdir(dirname($dir));
		@mkdir($dir, $mode);
	}
	return true;
}

function authcode ($string, $operation, $key = '') {	
	// TODO hacks for Swfuploader
	$key = md5($key ? $key :  md5(time()));

	$key_length = strlen($key);
	$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;

	$string_length = strlen($string);
	$rndkey = $box = array();
	$result = '';

	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
			return substr($result, 8);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}
}

include '../../setting/settings.php';
$key = $config['settings']['auth_key'];

include "qrlib.php";

$level = (in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H')) ? $_REQUEST['level'] : 'H');

$size = 4;
if(isset($_REQUEST['size'])) {
	if('small' == $_REQUEST['size']) {
		$size = 3;
	} else {
		$size = min(10, max(1, (int) $_REQUEST['size']));
	}
}

$margin = 2;
if(isset($_REQUEST['margin'])) {
	$margin = min(10, max(0, (int) $_REQUEST['margin']));
}

$data_rule = '~^.{11,1111}$~';
$data = trim(strip_tags(authcode(($_REQUEST['data'] ? $_REQUEST['data'] : ($_POST['data'] ? $_POST['data'] : $_GET['data'])), 'DECODE', $key)));

if ($data && preg_match($data_rule, $data)) {

	$md5 = md5($margin . $key . $size . $data . $level);

	$dir = 'data/' . substr($md5, -2) . '/' . substr($md5, 0, 2) . '/';
	$rdir = dirname(__FILE__) . '/' . $dir;

	$file = 'tttg_' . $md5 . '.png';
	$rfile = $rdir . $file;

	$img_src = $dir . $file;

	if(false == file_exists($rfile)) {

		mmkdir($rdir);
		QRcode::png($data, $rfile, $level, $size, $margin);

	}


} else {

	$img_src = 'temp/tttg.png';

	if(3 == $size) {
		$img_src = 'temp/tttg_small.png';
	}

}

header('Location: ' . $img_src);

//echo "<img src='{$img_src}' />";

//QRtools::timeBenchmark();
