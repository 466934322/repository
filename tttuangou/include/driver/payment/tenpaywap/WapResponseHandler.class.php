<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name WapResponseHandler.class.php
 * @date 2015-08-31 16:32:13
 */



class WapResponseHandler extends ResponseHandler {
	
	function __construct() {
		$this->WapResponseHandler();
	}
	
	function WapResponseHandler(){
		parent::ResponseHandler();
	}
	
	function isTenpaySign() {
				$keysArr = array(				
				"ver",
				"charset",
				"pay_result",
				"transaction_id",
				"sp_billno",
				"total_fee",
				"fee_type",
				"bargainor_id",
				"attach",
				"time_end"
		);	
		return parent::isTenpaySign($keysArr);	
	}
}