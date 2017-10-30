<?php

/**
 * 模块：优惠码管理
 * @copyright (C)2016 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package module
 * @name dbf.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function Main()
    {
        $code = '';
        $orderid = '';

        $sname = get('sname');
        $svalue = get('svalue');
        if('code' == $sname) {
            $code = $svalue;
        } elseif ('orderid' == $sname) {
            $orderid = (is_numeric($svalue)?$svalue:0);
        }

        $params = array(
                'code'=>$code, 
                'orderid'=>$orderid,
            );
        $status = get('status', 'int');
        if($status) {
            $params['status'] = ($status > 0 ? 1 : 0);
        }
        $datas = logic('promo_code')->getList($params);
        $page = page_moyo();

        include template('@admin/promo_code_list');
    }    
    public function delete() {
        $id = get('id', 'int');
        if($id < 1) {
            $this->Messager('ID不能为空');
        }

        $params = array(
                'id'=>$id,
            );
        $ret = logic('promo_code')->deleteInfo($params);

        $this->Messager('删除成功');
    }
    public function massGenerate() {

        logic('promo_code')->massGenerate(20);
        
        $this->Messager('批量生成成功', 'admin.php?mod=promo_code');
    }
    public function generate() {

        $rets = logic('promo_code')->generate();
        if($rets['error']) {
            $this->Messager('【生成失败】' . $rets['result']);
        }
        
        $this->Messager(null, 'admin.php?mod=promo_code&code=edit&id=' . $rets['result']);
    }    
    public function edit() {
        $id = get('id', 'int');
        if($id < 1) {
            $this->Messager('ID不能为空');
        }

        $params = array(
                'id'=>$id,
            );
        $info = logic('promo_code')->getInfo($params);
        if(!$info) {
            $this->Messager('ID错误');
        }

        include template('@admin/promo_code_info');
    }
    public function doEdit() {
        $id = post('id', 'int');
        if($id < 1) {
            $this->Messager('ID不能为空');
        }

        $params = array(
                'id'=>$id,
            );
        $info = logic('promo_code')->getInfo($params);
        if(!$info) {
            $this->Messager('ID错误');
        }

        $params = array(
                'id'=>$id,
                'code'=>post('scode'),
                'name'=>post('name'),
            );
        $rets = logic('promo_code')->saveInfo($params);
        if($rets['error']) {
            $this->Messager($rets['result']);
        }

        $this->Messager('更新成功', 'admin.php?mod=promo_code');
    }

}