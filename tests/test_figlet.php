<?php
error_reporting(E_ALL);
chdir('..');
$systemroot = getcwd();
include_once('include/command.php');
require_once 'include/figlet.php';

$figlet = new Zend_Text_Figlet();
print $figlet->render('foobar');
