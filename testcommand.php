<?php
include_once("include/command.php");
include_once("commands/google.php");
$google = new google();
$google->process("edecan ife");
