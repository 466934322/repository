<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name get-last-apk.php
 * @date 2015-04-21 16:43:55
 */
 


$dir = 'uploads/apks/release/';

if (is_dir($dir))
{
	$releases = array();
	$handler = opendir($dir);
	while (false != $file = readdir($handler))
	{
		if (preg_match('/^(\w+)\.(.*?)\-(\d{8})\.apk$/i', $file, $match))
		{
			$releases[$match[3]] = $match[0];
		}
	}
	closedir($handler);
	if ($releases)
	{
		ksort($releases);
		$last = end($releases);
		if ($last)
		{
			$site_url = rtrim(htmlspecialchars('http:/'.'/'.$_SERVER['HTTP_HOST'].preg_replace("/\/+/",'/',str_replace("\\",'/',dirname($_SERVER['PHP_SELF']))."/")),'/');
						header('Location: '.$site_url . '/' . $dir.$last);
			exit;
		}
	}
}

exit('APK.GET(LAST).ERROR');

?>