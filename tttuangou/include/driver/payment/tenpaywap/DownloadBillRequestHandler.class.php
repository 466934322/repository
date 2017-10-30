<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name DownloadBillRequestHandler.class.php
 * @date 2015-08-31 16:32:13
 */



class DownloadBillRequestHandler extends RequestHandler{


	function WapResponseHandler(){
		parent::ResponseHandler();
	}

	function createSign(){
	
	$signPars="";
	$signPars=$signPars."spid=".$this->getParameter("spid"). "&" .
		        "trans_time=".$this->getParameter("trans_time"). "&" .
				"stamp=".$this->getParameter("stamp"). "&".
				"cft_signtype=".$this->getParameter("cft_signtype"). "&" .
				"mchtype=".$this->getParameter("mchtype"). "&" .
				"key=".$this->getKey();
				
	$sign = strtolower(md5($signPars));
	
	$this->setParameter("sign", $sign);
		
				$this->_setDebugInfo($signPars . " => sign:" . $sign);
	
	}

}


