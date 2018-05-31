<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//文件缓存保存路径
$config['path'] = 'application/cache/cache_driver_data/' ;
$config['prefix'] = 'mini_course_';
$config['EnabledMemcache'] = FALSE;
$config['memcache']['Host'] = '127.0.0.1';
$config['memcache']['Port'] = '11211';
$config['memcache']['PConnect'] = false;
$config['EnabledEac'] = FALSE;
$config['EnabledFile'] = TRUE;