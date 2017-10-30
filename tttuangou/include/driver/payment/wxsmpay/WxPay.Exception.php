<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name WxPay.Exception.php
 * @date 2015-09-23 18:07:18
 */



class WxPayException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
