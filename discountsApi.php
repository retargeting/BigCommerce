<?php

/**
*	Feed
*	
*	
*/

if (empty($_GET['id']) && empty($_GET['d'])) {
	die('<p>Invalid Request!</p>');
}

require 'config.php';
require 'lib/DiscountsApi.php';
//use Retargeting\Lib\App;

$app = new App(Config());

?>
<?php if ($app->validRequest) : ?>
<?php echo $app->generateCoupons(); ?>
<?php else : ?>
<p>Unauthorized Request!</p>
<?php endif ?>