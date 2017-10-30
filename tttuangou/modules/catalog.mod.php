<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name catalog.mod.php
 * @date 2015-08-31 16:32:14
 */




class ModuleObject extends MasterObject
{
    public $Title = '';
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
                $runCode = Load::moduleCode($this, false, false);
        $this->Sort($runCode);
    }
    private function Sort($catalog)
    {
		$data = logic('product')->display(logic('catalog')->Filter($catalog, 'product'));
                if (!$data)
        {
            $data = array('product'=>array(),'mutiView'=>true);
        }
        else
        {
                        $data['mutiView'] = true;
            $product = (isset($data['product']['id']) && $data['product']['id']>0) ? array($data['product']) : $data['product'];
        }
		$cataname = logic('catalog')->Flag2Name($catalog);
        $this->Title = $data['mutiView'] ? $cataname : $product['name'];
                include handler('template')->file('home');
    }
}

?>
