<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name gps.logic.php
 * @date 2015-04-23 19:11:42
 */
 




class GpsLogic
{
	
	public function product_linker($seller_id, $map)
	{
		list($longitude, $latitude, $level) = explode(',', $map);
		if ($longitude && $latitude)
		{
			dbc(DBCMax)->update('seller')->where(array('id' => $seller_id))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
			dbc(DBCMax)->update('product')->where(array('sellerid' => $seller_id))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
		}
	}
	
	public function seller_linker($seller_id)
	{
		$seller = dbc(DBCMax)->select('seller')->where(array('id' => $seller_id))->limit(1)->done();
		if ($seller)
		{
			$this->product_linker($seller_id, $seller['sellermap']);
		}
	}
	
	public function init()
	{
		$this->maplocation4sogou2baidu();

		$sellers = dbc(DBCMax)->query("select `id`, `sellermap`, `longitude`, `latitude` from ".table('seller')." where `sellermap`!=''")->done();
		if($sellers) {
			foreach ($sellers as $seller)
			{
				if($seller['sellermap']) {
					list($longitude, $latitude, $level) = explode(',', $seller['sellermap']);
					if ($longitude && $latitude)
					{
						$_sellermap = $seller['sellermap'];
						if($level < 1) {
							$_sellermap = "{$longitude},{$latitude},16";
						}

						if($longitude != $seller['longitude'] || $latitude != $seller['latitude'] || $_sellermap != $seller['sellermap']) {
							dbc(DBCMax)->update('product')->where(array('sellerid' => $seller['id']))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
							dbc(DBCMax)->update('seller')->where(array('id' => $seller['id']))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude,'sellermap'=>$_sellermap))->done();
						}
					}
				}
			}
		}
	}

	public function get_baidu_xy($xy) {
		return $this->maplocation4sogou2baidu($xy, true);
	}

	private function maplocation4sogou2baidu($id = null, $is_coords = false) {
		if(false == $is_coords) {
			$sql = "select `id`, `sellermap` from ".table('seller')." 
				 where ".(null == $id ? '' : (" `id`='".(int) $id."' AND "))." `maptype`='sogou' 
				 AND `sellermap`!='' order by `id` ASC LIMIT 100";
			$sellers = dbc(DBCMax)->query($sql)->done();
			if($sellers) {
				$coords = array();
				foreach($sellers as $row) {
					list($row['x'], $row['y'], $row['z']) = explode(',', $row['sellermap']);
					if($row['x'] > 0 && $row['y'] > 0 && $row['z'] > 10) {
						$coords[] = "{$row['x']},{$row['y']}";
						$sellers[] = $row;
					}
				}
			}
		} else {
			$coords = (array) $id;
		}

		if($coords) {
			$url = "http:/"."/api.map.baidu.com/geoconv/v1/?coords=".urlencode(implode(';', $coords))."&from=2&to=5&ak=".ini('baidu_map_ak.server');
			$res = json_decode(file_get_contents($url), true);
			
			if(is_array($res['result']) && count($res['result'])) {
				if($sellers) {
					foreach($res['result'] as $k=>$row) {
						dbc(DBCMax)->update('seller')->where(array('id' => $sellers[$k]['id']))->data(array('sellermap'=>"{$row['x']},{$row['y']},{$sellers[$k]['z']}",'maptype'=>"baidu"))->done();
					}
				} else {
					return ((1 == count($coords) && is_string($id)) ? "{$res['result'][0]['x']},{$res['result'][0]['y']}" : $res['result']);
				}
			} else {
				error_log("\r\n-------------------------- " . date('Y-m-d H:i:s') . " --------------------------\r\n" . var_export($res, true) . "\r\n\r\n", 3, ROOT_PATH . 'errorlog/gps_sogou2baidu_error_log.'.date('Ym').'.txt');
			}
		}
	}

}