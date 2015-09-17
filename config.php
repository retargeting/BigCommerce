<?php
/**
*	Config
*
*	- returns cofinguration
*/
function Config() {

	return array(
		'clientId' => 'ik3lwp29e2o0iounqw7ph80vz3qwv06',
		'clientSecret' => 'rqjzt24z53d7pcroxsef1056spaoxyw',
		'callbackUrl' => 'https://retargeting.biz/BigCommerce/authCallback.php',
		'bcAuthService' => 'https://login.bigcommerce.com',
		'db' => array(
			'host' => 'localhost',
			'user' => 'retarbiz_shopify',
			'pass' => 'R*F2;nTGMwqf',
			'db' => 'retarbiz_bigcommerce'
		),
		'debug' => false,
		'logFile' => "logs/application.log",
		'timezone' => 'Europe/Bucharest'
	);

}
