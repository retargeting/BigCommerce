$(document).ready(function() {

	$('#btn-runWizard').click(runWizard);
	$('.btn-closeWizard').click(closeWizard);
	$('.btn-next').click(nextWizard);
	$('.btn-prev').click(prevWizard);

	$('#btn-save').click(saveConfiguration);
	$('#btn-savePreferences').click(saveConfiguration);
	$('#btn-changeStatus').click(changeStatus);

	$('.btn-disableInit').click(disableInit);
});

function runWizard() {
	console.log("Implementing Wizard! Wait.. If I am here, and you are here.. who's implementing?");

	$($('.wizard-content article')[0]).css('display', 'block');
	$('.wizard-content').fadeIn('fast');
}

function closeWizard() {
	$('.wizard-content').fadeOut('fast');
}

function nextWizard() {
	$(this).parent().fadeOut('fast', function() {
		$(this).next().fadeIn('fast');
	});
}

function prevWizard() {
	$(this).parent().fadeOut('fast', function() {
		$(this).prev().fadeIn('fast');
	});
}

function disableInit() {
	$.post( "configurationAjax.php", {
			shopId: RA_SHOPID,
			shopEmail: RA_SHOPEMAIL,
			shopContext: RA_SHOPCONTEXT,
			disableInit: true
		}, function(data) {
			var data = JSON.parse(data);
			if (!data) {
				console.log('RA_INFO: Sorry, but we could not disable the initial screen. Please try again later');
			} else {
				$('section.init').fadeOut('fast');
			}
		} 
	)
}

function saveConfiguration() {
	console.info('Retargeting Info :: Saving configuration.');

	$.post( "configurationAjax.php", {
			shopId: RA_SHOPID,
			shopEmail: RA_SHOPEMAIL,
			shopContext: RA_SHOPCONTEXT,
			domainApiKey: $('input[name="ra_domain_api_key"]').val(), 
			discountsApiKey: $('input[name="ra_discounts_api_key"]').val(), 
			help_pages: $('input[name="ra_help_pages"]').val(), 
			qs_add_to_cart: $('input[name="ra_add_to_cart"]').val(), 
			// qs_variation: $('input[name="ra_variation"]').val(), 
			// qs_add_to_wishlist: $('input[name="ra_add_to_wishlist"]').val(), 
			// qs_image: $('input[name="ra_image"]').val(), 
			// qs_review: $('input[name="ra_review"]').val(), 
			// qs_price: $('input[name="ra_price"]').val()
		}, function(data) {
			var data = JSON.parse(data);

			if (!data) {
				console.log('RA_INFO: Sorry, but we could not save the new configuration. Please try again later');
			} else {
				location.reload();
			}
		} 
	)
}

function changeStatus() {
	console.info('Retargeting Info :: Change status.');

	$.post( "configurationAjax.php", {
			shopId: RA_SHOPID,
			shopEmail: RA_SHOPEMAIL,
			shopContext: RA_SHOPCONTEXT,
			changeStatus: true,
			domainApiKey: $('input[name="ra_domain_api_key"]').val(), 
			discountsApiKey: $('input[name="ra_discounts_api_key"]').val(), 
			help_pages: $('input[name="ra_help_pages"]').val(), 
			qs_add_to_cart: $('input[name="ra_add_to_cart"]').val(), 
			// qs_variation: $('input[name="ra_variation"]').val(), 
			// qs_add_to_wishlist: $('input[name="ra_add_to_wishlist"]').val(), 
			// qs_image: $('input[name="ra_image"]').val(), 
			// qs_review: $('input[name="ra_review"]').val(), 
			// qs_price: $('input[name="ra_price"]').val()
		}, function(data) {
			var data = JSON.parse(data);

			if (!data) {
				console.log('RA_INFO: Sorry, but we could not save the new configuration. Please try again later');
			} else {
				location.reload();
			}
		}
	)
}