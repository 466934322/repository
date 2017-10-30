<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wips.php
 * @date 2015-08-31 16:32:14
 */



$config["wips"] =  array (
  'sql' => 
  array (
    'enabled' => true,
    'dfunction' => 'load_file,hex,substring,if,ord,char,ascii,mid,sleep',
    'daction' => 'intooutfile,intodumpfile,unionselect,unionall,uniondistinct,(select',
    'dnote' => '--',
    'afullnote' => 'true',
    'dlikehex' => 'true',
    'foradm' => 'false',
    'autoups' => 'true',
  ),
);
?>