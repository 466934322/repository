<!doctype html>
<html>
<head>
<?php echo '<base h'.'ref="'.ini('settings.site_url').'/" />'; ?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ini("settings.charset"); ?>" />
<meta http-equiv="X-UA-Compatible" content="Chrome=1,IE=edge" />
<title>天天团购安装向导</title>
<?php echo ui('loader')->css('#inizd/install/style'); ?>
</head>
<div class="container">
<div class="header">
<h1>天天团购安装向导</h1>
<span>Version: <?php echo SYS_VERSION; ?> <?php echo SYS_BUILD; ?></span>