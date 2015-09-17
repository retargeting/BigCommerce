<?php

/**
*	App Configuration Page
*	
*	- loaded through an Iframe in the Shop's Administration Section
*/

if (empty($_GET['signed_payload'])) {
	die('<p>Invalid Request!</p>');
}

require 'config.php';
require 'lib/ConfigSystem.php';
//use Retargeting\Lib\App;

$app = new App(Config());

?>
<?php if ($app->validRequest) : ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Retargeting</title>

		<link rel="stylesheet" type="text/css" href="css/configuration.css">
		<link href='//fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic' rel='stylesheet' type='text/css'>

		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script type="text/javascript">
			var RA_SHOPID = '<?php echo $app->shopId; ?>';
			var RA_SHOPEMAIL = '<?php echo $app->shopEmail; ?>';
			var RA_SHOPCONTEXT = '<?php echo $app->shopContext; ?>';
		</script>
		<script type="text/javascript" src="js/configuration.js"></script>
	</head>

	<body>

		<section class="wizard">
			<header class="span-1">
				<h1>Wizard</h1>
				<div id="btn-runWizard" class="btn btn-save">Run</div>
			</header>
			<div class="content span-3">
				
				<div class="input">
					<label for="ra_status">Installation</label>
					<p class="description">As <strong>Bigcommerce</strong> doesn't allow applications to add <strong>JS snippets</strong> in shops, every client needs to add the snippets <strong>manually</strong>. It's pretty easy, you'll just have to follow this wizard.</p>
				</div>

			</div>
		</section>

		<section>
			<header class="span-1">
				<h1>App Status</h1>
				<div id="btn-changeStatus" class="btn btn-save"><?php echo ($app->status ? 'Disable' : 'Enable'); ?></div>
			</header>
			<div class="content span-3">

				<div class="input">
					<label for="ra_status"><?php echo ($app->status ? 'Running' : 'Not running'); ?></label>
					<p class="description"><?php echo ($app->status ? 'Your <a href="http://retargeting.biz">Retargeting</a> App is <strong>up</strong> and <strong>running</strong>' : 'Currently your <a href="http://retargeting.biz">Retargeting</a> App is <strong>not</strong> tracking any of your users. To activate it please press the <strong>Enable</strong> button from to top right of your screen.'); ?></p>
				</div>

			</div>
		</section>

		<section>
			<header class="span-1">
				<h1>Secure Keys</h1>
				<div id="btn-save" class="btn btn-save">Save</div>
			</header>
			<div class="content span-3">

				<div class="input">
					<label for="ra_domain_api_key">Domain API Key</label>
					<input type="text" name="ra_domain_api_key" id="ra_domain_api_key" placeholder="ex: 1238BFDOS0SFODBSFKJSDFU2U32" value="<?php echo $app->domainApiKey; ?>">
					<p class="description">You can find your Secure Domain API Key in your <a href="http://retargeting.biz">Retargeting</a> account.</p>
				</div>

				<div class="input">
					<label for="ra_discounts_api_key">Discounts API Key</label>
					<input type="text" name="ra_discounts_api_key" id="ra_discounts_api_key" placeholder="ex: 1238BFDOS0SFODBSFKJSDFU2U32" value="<?php echo $app->discountsApiKey; ?>">
					<p class="description">You can find your Secure Discounts API Key in your <a href="http://retargeting.biz">Retargeting</a> account.</p>
				</div>

			</div>
		</section>

		<section>
			<header class="span-1">
				<h1>Preferences</h1>
				<div id="btn-savePreferences" class="btn btn-save">Save</div>
			</header>
			<div class="content span-3">
				
				<div class="input">
					<label for="ra_help_pages">Help Pages</label>
					<input type="text" name="ra_help_pages" id="ra_help_pages" placeholder="about-us" value="<?php echo $app->helpPages; ?>">
					<p class="description">Please add the URL handles for the pages on which you want the "visitHelpPage" event to fire. Use a comma as a separator for listing multiple handles. For example: http://yourshop.com/<strong>about-us</strong> is represented by the "about-us" handle.</p>
				</div>

				<div class="info">
					<label>JavaScript Query Selectors</label>
					<p>The <a href="http://retargeting.biz">Retargeting</a> App should work out of the box for most themes. But, as themes implementation can vary, in case there would be any problems with events not working as expected you can modify the following settings to make sure the events are linked to the right theme elements.</p>
				</div>

				<div class="input">
					<label for="ra_">Add To Cart Button</label>
					<input type="text" name="ra_add_to_cart" id="ra_add_to_cart" placeholder='form[action="/cart/add"] [type="submit"]' value="<?php echo $app->querySelectors['addToCart']; ?>">
					<p class="description">Query selector for the button used to add a product to cart.</p>
				</div>

			</div>
		</section>

		<section class="wizard-content">
			<div class="overlay"></div>

			<article>
				<h1><strong>Installation</strong> Overview</h1>
				<p>As BigCommerce doesn't allow Applications to add <strong>custom JavaScript</strong> to shops, you'll have add some code snippets manually. Don't worry, as it's a pretty straight forward process with just a few steps.</p>
				<div class="btn-wizardControl btn-closeWizard">Close</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #1 - Helpers, Embedding & General Methods</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=Panels/Header.html</i></p>
				<p>In your address bar, delete everything after ".com". Paste this line there, instead. If you have any trouble, you can get to the same link by going to Settings - Store Setup - Design - Template Files - Panels and editing the Header file.</p>
				<p>Now add the following code snippet at the end of the Header.html file</p>
				<code>
&lt;script&gt; _ra_cartItems = []; _ra_getVariation = function() {return false;}; function _ra_queryApi(method, params) { if (typeof method === 'undefined') return false; params = JSON.stringify(params || ''); var _ra_js = document.createElement(&quot;script&quot;); _ra_js.type =&quot;text/javascript&quot;; _ra_js.async = true; _ra_js.src = &quot;https://retargeting.biz/BigCommerce/api.php?method=&quot;+method+&quot;&amp;shop=<?php echo $app->shopId; /* encodeURI(window.location.host)*/?>&amp;params=&quot;+params; var s = document.getElementsByTagName(&quot;script&quot;)[0]; document.getElementsByTagName(&quot;head&quot;)[0].appendChild(_ra_js); } /* RA::embedd */ _ra_queryApi('embedd'); &lt;/script&gt; &lt;script&gt; $(document).ready(function() { /* RA::setEmail */ var _ra_input = document.querySelector('#login_email'); if (_ra_input !== null) { _ra_input.addEventListener(&quot;change&quot;, function() { _ra_ce = this.value; _ra_queryApi('setEmail', {id: _ra_ce});	}); } /* RA::addToCart */ $('.add-to-cart, a[href*=&quot;cart.php?action=add&quot;]').click(function() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid == null) { var _ra_regex = $(this).attr('href').match(/cart.php\?action=add\&amp;product_id=([0-9]+)/); _ra_pid = (_ra_regex !== null ? _ra_regex[1] : null); } if (_ra_pid !== null) { _ra.addToCart(_ra_pid, _ra_getVariation()); } }); /* RA::addToWishlist */ $('.wishTrigger').click(function() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra.addToWishlist(_ra_pid); } }); }); /* RA::visitHelpPages */ _ra_queryApi('visitHelpPages', {id: encodeURIComponent(window.location.pathname)}); &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #2 - Send Category Event</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=category.html</i> (or open the Category file)</p>
				<p>Now add the following code snippet right before closing the <i>body (&lt;/body&gt;)</i> tag:</p>
				<code>
&lt;script&gt; /* RA::sendCategory */ if (typeof category !== 'undefined') _ra_queryApi('sendCategory', {id: category}); &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #3 - Send Brand Event</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=brands.html</i> (or open the Brands file)</p>
				<p>Now add the following code snippet right before closing the <i>body (&lt;/body&gt;)</i> tag:</p>
				<code>
&lt;script&gt; /* RA::sendBrand */ if (&quot;%%GLOBAL_TrailBrandName%%&quot; != &quot;&quot;) _ra_queryApi('sendBrand', {id: encodeURIComponent(&quot;%%GLOBAL_TrailBrandName%%&quot;)}); &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #4 - Product Events</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=Panels/ProductDetails.html</i> (or open the Product file)</p>
				<p>Now add the following code snippet right before closing the <i>body (&lt;/body&gt;)</i> tag:</p>
				<code>
&lt;script&gt; var _ra_ProductId = '%%GLOBAL_ProductId%%'; $(document).ready(function() { /* RA::sendProduct */ if (_ra_ProductId !== '') { _ra_queryApi('sendProduct', {id: _ra_ProductId}); } /* RA::setVariation */ $('.productAttributeRow .option, .productAttributeRow .swatch, .productAttributeRow .checker, .productAttributeRow .radio').click(function() { _ra_setVariation(); }); $('.productAttributeRow .Field').change(function() { _ra_setVariation(); }); _ra_getVariation = function() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid === null) return false; var _ra_variation = false, _ra_arr_code = [], _ra_arr_details = []; for (var i = 0; i &lt; $('.productAttributeRow').length; i++) { var _ra_el = $('.productAttributeRow')[i], _ra_elInner = null, _ra_oValue = null, _ra_oLabel = null; _ra_oLabel = $(_ra_el).find('.productAttributeLabel .name'); if (_ra_oLabel.length) { _ra_oLabel = _ra_oLabel.text(); } _ra_elInner = $(_ra_el).find('.selectedValue .name, .Field'); if (_ra_elInner.length) { _ra_oValue = _ra_elInner.text(); } else { _ra_elInner = $(_ra_el).find('.checker'); if (_ra_elInner.length) { _ra_elInner = $(_ra_elInner).find('[type=&quot;checkbox&quot;]'); _ra_oValue = _ra_elInner.checked ? 'Yes' : 'No'; }	} if (_ra_oValue !== &quot;&quot; &amp;&amp; _ra_oValue !== null &amp;&amp; _ra_oLabel !== null) { _ra_oValue = _ra_oValue.replace(/-/g, ''); _ra_arr_code.push(_ra_oValue); _ra_arr_details[_ra_oValue] = {&quot;category_name&quot;: _ra_oLabel, &quot;category&quot;: _ra_oLabel, &quot;value&quot;: _ra_oValue}; } } if (_ra_arr_code.length &gt; 0) _ra_variation = { &quot;code&quot;: _ra_arr_code.join(&quot;-&quot;), &quot;details&quot;: _ra_arr_details }; return _ra_variation; }; function _ra_setVariation() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra = typeof _ra !== 'undefined' ? _ra : {}; _ra.setVariationInfo = { &quot;product_id&quot;: _ra_pid, &quot;variation&quot;: _ra_getVariation() }; if (_ra.ready !== undefined) { _ra.setVariation(_ra.setVariationInfo.product_id, _ra.setVariationInfo.variation); } } } /* RA::clickImage */ document.querySelector(&quot;.ProductThumbImage&quot;).addEventListener(&quot;mouseover&quot;, function(ev) { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra.clickImage(_ra_pid); } }); /* RA::commentOnProduct */ $('form[action*=&quot;postreview.php&quot;] [type=&quot;submit&quot;]').click(function() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra.commentOnProduct(_ra_pid); } }); /* RA::mouseOverPrice */ var _ra_mop = document.querySelectorAll('.ProductMain .ProductPrice'); for (var i = 0; i &lt; _ra_mop.length; i ++) { _ra_mop[i].addEventListener(&quot;mouseover&quot;, function(ev) { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra_queryApi('mouseOverPrice', {id: _ra_pid}); } });	} /* RA::mouseOverAddToCart */ document.querySelector('.ProductMain .add-to-cart').addEventListener(&quot;mouseover&quot;, function() { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null && typeof _ra.mouseOverAddToCart !== 'undefined') { _ra.mouseOverAddToCart(_ra_pid); } }); /* RA::likeFacebook */ if (typeof FB != &quot;undefined&quot;) { FB.Event.subscribe(&quot;edge.create&quot;, function () { var _ra_pid = typeof _ra_ProductId !== 'undefined' ? _ra_ProductId : null; if (_ra_pid !== null) { _ra.likeFacebook(_ra_pid); } }); }; }); &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #5 - Save Order Event</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=order.html</i> (or open the Order file)</p>
				<p>Now add the following code snippet right before closing the <i>body (&lt;/body&gt;)</i> tag:</p>
				<code>
&lt;script&gt; /* RA::saveOrder */ _ra_queryApi('saveOrder', {id: &quot;%%GLOBAL_OrderId%%&quot;}); &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>

			<article>
				<h1><strong>Installation</strong> Step #6 - Checkout Events</h1>
				<p>Copy the following line: <i>/admin/designmode.php?ToDo=editFile&File=Snippets/CartItem.html</i> (or open the Cart Item snippet from the Snippets folder)</p>
				<p>Now add the following code snippet at the end of the file:</p>
				<code>
&lt;script&gt; _ra_cartItems.push(%%GLOBAL_ProductId%%); &lt;/script&gt;​
				</code>
				<p>All that's left to do is add the next code snippet on the Cart Coupon Code Box and the Side Coupon Code Box (<i>/admin/designmode.php?ToDo=editFile&File=Panels/CartCouponCodeBox.html</i> &amp; <i>/admin/designmode.php?ToDo=editFile&File=Panels/SideCouponCodeBox.html</i>)</p>
				<code>
&lt;script&gt; /* RA::checkoutIds */ var _ra = _ra || {}; _ra.checkoutIdsInfo = _ra_cartItems; if (_ra.ready !== undefined) { _ra.checkoutIds(_ra.checkoutIdsInfo); } &lt;/script&gt;
				</code>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-next">Next</div>
			</article>
			<article>
				<h1><strong>Installation</strong> Complete!</h1>
				<p>All that's left now is to enjoy the functionalities and benefits of <a href="http://retargeting.biz">Retargeting</a>.</p>
				<p><strong>! Important Notice:</strong> <i>keep in mind that after changing some settings in our Application's configuration page, it is necessary to go through these steps again, to ensure the proper functionality of our application.</i></p>
				<div class="btn-wizardControl btn-prev">Back</div>
				<div class="btn-wizardControl btn-closeWizard">Enjoy</div>
			</article>

		</section>

		<section class="init <?php if ($app->init) echo 'disabled'; ?>">
			
			<article>
				<!--<img src="imgs/logo-big.jpg">-->
				<h1>Hello!</h1>
				<h2>To have access to our awesome features you need a <a href="https://retargeting.biz" target="_blank">Retargeting account</a>.</h2>
				<div class="row">
					<div class="btn-init btn-disableInit">I already have an account</div>
					<a href="https://retargeting.biz/signup"><div class="btn-init btn-cta">Start your 14-day Free Trial</div></a>
				</div>
			</article>
		
		</section>
	</body>
</html>
<?php else : ?>
<p>Unauthorized Request!</p>
<?php endif ?>