<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name alipay_md5.function.php
 * @date 2015-09-28 17:03:51
 */



function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}


function md5Verify($prestr, $sign, $key) {
	if ($_POST){
		$prestr = iconv('gbk','utf-8',$prestr . $key);
	}else{
		$prestr = $prestr . $key;
	}

	$mysgin = md5($prestr);


	if($mysgin == $sign) {
		return true;
	}
	else {
		
		return false;
	}
}
?>