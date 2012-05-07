<?php
include_once("include/command.php");
include_once("commands/calc.php");
include_once("commands/twitter.php");
/*$calc = new calc();
$calc->process("(2 + 2) / (3 /9 * 2.3)");*/
$twitter = new twitter();
$twitter->process("hey_mx");