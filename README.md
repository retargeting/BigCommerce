# Retargeting App for BigCommerce

### Contents
* Requirements & Compatibility.
* Installing Retargeting Extension.
* Configuration.
* Troubleshooting & Support.

### Requirements & Compatibility
* A Retargeting account
* A Shopify store

### Installing Retargeting Application
As Bigcommerce doesn't allow applications to add JS snippets in shops, every client needs to add the snippets manually. It's pretty easy, you'll just have to follow the wizard.

To run the wizzard just click "Run" under the "Wizard" title and follow the steps shown there.
> *Keep in mind that after changing some settings in our Application's configuration page, it is necessary to go through these steps again, to ensure the proper functionality of our application.*

### Configuration
The extension’s settings are available via Apps - Retargeting.
* Setup Domain API Key
 * Go to your [Retargeting Account](https://retargeting.biz/admin)
 * Get the Domain API Key from Settings Page
 * Select & copy Discounts API Key
 * Go to your BigCommerce Store Admin Panel - Apps - Retargeting
 * Paste Domain API Key in the coresponding field
 * Click Save

* Setup Discounts API Key
 * Go to your [Retargeting Account](https://retargeting.biz/admin)
 * Get the Discount API Key from Settings Page
 * Select & copy Discounts API Key
 * Go to your BigCommerce Store Admin Panel - Apps - Retargeting
 * Paste Discounts API Key in the coresponding field
 * Click Save

* To enable the Retargeting Application just press Enable from under the "App Status" title. After that the App Status should be now set to Running

* Setup Help Pages
 * Go to your BigCommerce Store Admin Panel - Apps - Retargeting
 * Now add the URL handles for the pages on which you want the "visitHelpPage" event to fire. Use a comma as a separator for listing multiple handles. For example: http://yourshop.com/about-us is represented by the "about-us" handle.
 * Click Save

* Javascript Query Selectors
 * The Retargeting App should work out of the box for most themes. But, as themes implementation can vary, in case there would be any problems with events not working as expected you can modify the following settings to make sure the events are linked to the right theme elements.

### Troubleshooting & Support
For help or more info, please contact us at [info@retargeting.biz](info@retargeting.biz) or visit our [website](https://retargeting.biz)
