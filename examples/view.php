<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once 'loader.php';


$t = new \myExtensions\myTemplate();
$t->addFunction("testfunction", function($value){ return "TestFunction: {$value}"; });
$t->setSetting("dir", __dir__.'/templates/');

echo $t->render("index.phtml", array("name" => "myExtension"));