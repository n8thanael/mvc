<?php
// autoload classes
spl_autoload_extensions(".class.php");
spl_autoload_register();

// configs
require 'config/paths.php';
require 'config/database.php';

// primary MVC libraries which load primary system
require 'libs/Bootstrap.php';
require 'libs/Controller.php';
require 'libs/View.php';
require 'libs/Database.php';
require 'libs/Session.php';
require 'libs/Model.php';

$session = new Session();
$session->init();
$app = new Bootstrap();

// absoulte bottom of eveything is here


