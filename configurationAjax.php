<?php

/**
* Configuration Ajax Route
*
* - route for configuration changes in the App's Administration section
*/

if (empty($_POST['shopId']) || empty($_POST['shopEmail']) || empty($_POST['shopContext'])) {
	die('<p>unauthorized request!</p>');
}
if ((!isset($_POST['domainApiKey']) || !isset($_POST['discountsApiKey'])) && (empty($_POST['disableInit']))) {
	die('<p>Invalid request!</p>');
}

require 'config.php';
require 'lib/Ajax.php';

$app = new App(Config());

if ($app->validRequest) {

	die($app->dispatch());
}