<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 邮箱配置
 */
$config = Array(
    'protocol' => 'smtp',
    'smtp_host' => 'smtp.exmail.qq.com',
    'smtp_port' => 25,//465
    'smtp_user' => 'your email',
    'smtp_pass' => 'password',
    'mailtype' => 'html'
);