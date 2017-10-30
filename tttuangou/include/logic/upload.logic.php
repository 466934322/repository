<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name upload.logic.php
 * @date 2015-08-31 16:32:13
 */





class UploadLogic
{
	
	public function html($class = 'image')
	{
		$exts = explode(',', ini('upload.exts'));
		$allowExts = '';
		foreach ($exts as $i => $ext)
		{
			$allowExts .= '*.'.$ext.';';
		}
		list($unit, $val) = explode(':', ini('upload.size'));
		$allowSize = ($unit == 'mb') ? $val*1024 : $val;
		$uploadSize = $val . $unit;
		include handler('template')->file('@html/uploader/'.$class);
	}
	
	public function GetOne($id)
	{
		$id = (int)$id;
		if (!$id) return array();
		$ckey = 'upload.getone.'.$id;
		$list = cached($ckey);
		if ($list) return $list;
		return cached($ckey, dbc(DBCMax)->query('SELECT * FROM '.table('uploads').' WHERE id='.$id)->limit(1)->done());
	}
	
	public function Field($id, $key, $val = null)
	{
		$id = (int) $id;
		if (!is_null($val))
		{
			$data = array(
				$key => $val
			);
			dbc()->SetTable(table('uploads'));
			dbc()->Update($data, 'id='.$id);
			return;
		}
		$file = $this->GetOne($id);
		return $file ? $file[$key] : '';
	}
	
	public function Update($id, $data)
	{
		$id = (int) $id;
		dbc()->SetTable(table('uploads'));
		return dbc()->Update($data, 'id='.$id);
	}
	
	public function Delete($id)
	{
		$id = (int) $id;
		if ($id <= 0) return;
		$file = $this->GetOne($id);
		if (is_file($file['path']))
		{
			unlink($file['path']);
		}
		dbc()->SetTable(table('uploads'));
		dbc()->Delete('', 'id='.$id);
	}
	
	public function Save($field = 'Filedata', $savePATH = false, $wmd = false)
	{
		$upr = handler('upload')->Newz();
		$upr->AllowExts(ini('upload.exts'));
		$upr->AllowSize(ini('upload.size'));
		$savePATH || $savePATH = UPLOAD_PATH.'{$Y}-{$M}-{$D}/{$HASH}.{$EXT}';
		$upr->SavePath($savePATH);
		$files = $upr->Process($field);
		$result = array();
		if(is_array($files) && count($files)) {
			if ($wmd)
			{
				$files = $this->Watermark_process($files);
			}
			if (isset($files[0]['name']))
			{
								foreach ($files as $i => $file)
				{
					$result[] = $this->Log($file);
				}
			}
			else
			{
								$result = $this->Log($files);
			}
		} elseif(is_string($files)) {
			$result = array(
				'error' => true,
				'msg' => $files
			);
		}
		return $result;
	}
	
	public function AddLocal($path)
	{
		$info = handler('image')->Info($path);
		$file = array(
			'name' => 'localfile',
			'path' => $path,
			'type' => $info['type'],
			'size' => $info['size'],
			'mime' => $info['mime']
		);
		return $this->Log($file);
	}
	
	private function Log($file)
	{
		if (is_string($file))
		{
			return array(
				'error' => true,
				'msg' => $file
			);
		}
		$data = $file;
		$data['intro'] = '';
		$data['url'] = ini('settings.site_url').str_replace('./', '/', $data['path']);
		$data['extra'] = '';
		$data['uid'] = user()->get('id');
		$data['ip'] = ip2long(client_ip());
		$data['update'] = time();
		dbc()->SetTable(table('uploads'));
				$exist = dbc(DBCMax)->select('uploads')->where('path="'.$data['path'].'"')->limit(1)->done();
		if ($exist)
		{
			dbc()->Update($data, 'id='.$exist['id']);
			$data['id'] = $exist['id'];
		}
		else
		{
			$data['id'] = dbc()->Insert($data);
		}
				return $data;
	}
	
	private function Watermark_process($files)
	{
		if (ini('image.watermark.enabled'))
		{
			if (isset($files[0]['name']))
			{
							foreach ($files as $i => $file)
				{
					$this->Watermark_save($file['path']);
				}
			}
			else
			{
							$this->Watermark_save($files['path']);
			}
		}
		return $files;
	}
	
	private function Watermark_save($path)
	{
		if($path && $this->is_image_ext($path) && file_exists($path)) {
			$image_original = str_replace(UPLOAD_PATH, UPLOAD_PATH.'originals/', $path);
						handler('io')->initPath($image_original);
						copy($path, $image_original);
						$this->Watermark($path, $path);
		}
	}
	
	public function Watermark($image_source, $image_dest, $default = null)
	{
		if($image_source && $this->is_image_ext($image_source)) {
			$config = $default ? $default : ini('image.watermark');
			return logic('image')->water($image_source, $image_dest, $config);
		}
	}
	private function is_image_ext($path) {
		static $imgext  = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
		$ext = addslashes(strtolower(substr(strrchr($path, '.'), 1, 10)));
		return in_array($ext, $imgext) ? 1 : 0;
	}
}

?>