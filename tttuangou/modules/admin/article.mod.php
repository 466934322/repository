<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name article.mod.php
 * @date 2015-09-11 16:54:02
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$this->CheckAdminPrivs('article');
		$articles = logic('article')->get_list();
        if($articles){
            foreach($articles as $k=>$v){
                $articles[$k]['timestamp_create'] = date('Y-m-d H:i:s',$v['timestamp_create']);
            }
        }
        $action = "admin.php?mod=article&code=deletearticles";
		include handler('template')->file('@admin/articles_list');
	}
	
	public function create()
	{
		$this->CheckAdminPrivs('article');
		$article = array(
			'writer' => MEMBER_NAME
		);
		include handler('template')->file('@admin/article_modify');
	}
	
	public function modify()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		$article = logic('article')->get_one($id);
		include handler('template')->file('@admin/article_modify');
	}
	
	public function delete()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		logic('article')->delete($id);
		$this->Messager('删除成功！', '?mod=article');
	}
    
    function Deletearticles(){
        $this->CheckAdminPrivs('article');
        $ids = (array) post('ids');
        $ids[] = get('id', 'int');
        $this->DatabaseHandler->SetTable(TABLE_PREFIX.'tttuangou_article');
        foreach($ids as $id) {
            $id = intval($id);
            if($id > 0) {
                $this->DatabaseHandler->Delete('','id='.$id);
            }
        }
        $this->Messager("操作成功","?mod=article");
    }
	
	public function save()
	{
		$this->CheckAdminPrivs('article');
		$title = post('title', 'string');
		$content = post('content');
		$writer = post('writer', 'string');
		if ($title && $content && $writer)
		{
			if(false == logic('seccode')->verify(post('seccode'))) {
				$this->Messager('请输入正确的图片验证码');
			}
			$id = post('id', 'int');
			if ($id)
			{
				logic('article')->update($id, $title, $content, $writer);
			}
			else
			{
				logic('article')->create($title, $content, $writer);
			}
			$this->Messager('保存成功！', '?mod=article');
		}
		else
		{
			$this->Messager('标题或者内容或者署名都不可以为空！', -1);
		}
	}
}

?>