<?php
error_reporting(E_ALL);
chdir('..');
$systemroot = getcwd();
include_once('init.php');
include_once('include/command.php');
require_once 'commands/unmd5.php';
$teststring = 'acbd18db4cc2f85cedef654fccc4a4d8';
$command = new unmd5();
$command->process($teststring);
print $command->output;

