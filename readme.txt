=== Plugin Name ===
Contributors: eugenmihailescu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KQZLBZXPWQD62
Tags: braintree,3D Secure,payments,billings,card,credit card,debit
card,woocommerce,dropin,hosted fields,paypal,refunds,recurrent,recurring,card
validation,CCV,CVV,CSC,AVS,merchant,payment
gateway,ecommerce,e-commerce,commerce,wordpress
ecommerce,store,shop,sales,shopping,cart,checkout,woocommerce
payment,woocommerce card,braintree woocommerce plugin,braintree payment
gateway,woocommerce credit cards payment with braintree,free braintree woocommerce
gateway
for woocommerce,wordpress payments,braintree payments,braintree
plugin,braintree gateway,payment processing,payment gateways
Requires at least: 3.3
Tested up to: 4.8
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Enables 3D Secure card payments and refunds in WooCommerce via Braintree - a Level 1 PCI DSS compliant payment gateway

== Description ==

[Woo Braintree Payment](http://mynixworld.info/shop/product/woo-mynix-braintree) is a plug-in that enables [WooCommerce](https://wordpress.org/plugins/woocommerce) merchants to accept debit/credit card and/or PayPal payments via the [Braintree](http://braintreepayments.com) - a PayPal company. [Braintree is a Level 1](https://en.wikipedia.org/wiki/Braintree_%28company%29) [PCI-DSS](https://en.wikipedia.org/wiki/Payment_Card_Industry_Data_Security_Standard) compliant service provider.

Our plug-in extends the WooCommerce by defining a new (card) payment method called Braintree. As such the WooCommerce checkout page offers a new payment method - Credit card - with three fields: a card number, card expiry date and the Card Code Verification (aka CCV or CVC).

The plug-in is built with [PCI DSS](https://en.wikipedia.org/wiki/Payment_Card_Industry_Data_Security_Standard) in mind. The card information is exchanged exclusively between the card holder and the Braintree gateway - a Level 1 PCI DSS service provider. This is very important if you want your webshop [to be Level 3 PCI-DSS compliant](https://www.braintreepayments.com/faq#pci-compliance) (and you, as a merchant, [should be](https://www.pcisecuritystandards.org/security_standards/why_comply.php)!). 

The plug-in allows various levels of card customization, from its aspect (via custom CSS) to functionality. Nevertheless it allows you to configure it and train its functionalities in a Sandbox environment and finally, when you are ready, just switch it to production mode with one click. See a comparision between [Sandbox mode and Production mode](https://articles.braintreepayments.com/get-started/try-it-out#sandbox-vs.-production).


= Major features included(*) in Woo Braintree Payment = 

* Three different UI integration types with your WooCommerce checkout form: Custom UI, [Drop-in UI](http://braintreepayments.com/features/drop-in) and [Hosted Fields](http://braintreepayments.com/features/hosted-fields) integration
* Complies with the PCI DSS rules in all its integration types (ie. Custom UI, Drop-in UI and Hosted Fields)
* Support for the most common cards issued by [Visa, MasterCard, Maestro, AmericanExpress, Discover, Diner's Club and JCB](https://articles.braintreepayments.com/get-started/payment-methods).
* Support for payments in [130+ local currencies](https://articles.braintreepayments.com/get-started/currencies#multi-currency-setups)
* Support for [multiple merchant accounts](https://articles.braintreepayments.com/control-panel/important-gateway-credentials#additional-credentials) (ie USD orders funds go into your USD account, EUR orders funds go into your EUR account, etc)
* Supports card validation through the card issuer’s [3D-Secure](https://developers.braintreepayments.com/guides/3d-secure/overview) layer (eg. [Verified by VISA](http://www.visaeurope.com/making-payments/verified-by-visa))
* Manages the payment risk for the non-3DS cards by setting a payment threshold for any of the supported currency and additionally via [AVS rules](https://articles.braintreepayments.com/guides/fraud-tools/avs-cvv) 
* Checkout card form customization for all integration types (ie. Custom UI, Drop-in UI and Hosted Fields)
 - define and display the accepted card issuers (eg. Visa, MasterCard, etc)
 - enable/disable the CCV/CVC as mandatory
 - validate the card data on the fly (while users type-in)
 - set your custom cards validation (regex) rules
 - show/hide the card and/or the PCI compliance badges on the checkout form
 - customize the card badges position on the checkout form
 - customize the card form/fields using custom CSS
 - support your custom language (only Custom UI and Hosted Fields integration)
* Support for integrated PayPal button with [Braintree Vault checkout flow](http://developers.braintreepayments.com/guides/paypal/vault/javascript/v2) or with [PayPal checkout flow](http://developers.braintreepayments.com/guides/paypal/checkout-with-paypal/javascript/v2)
* Support for [automatic/manual settlement](https://articles.braintreepayments.com/get-started/transaction-life-cycle#submitted-for-settlement) (ie the payment is sent automatically to be settled or you may do that manually in the [Braintree Control Panel](https://articles.braintreepayments.com/control-panel/basics/overview))
* Support for training mode (aka [Sandbox](https://articles.braintreepayments.com/get-started/try-it-out)) and [production mode](https://articles.braintreepayments.com/get-started/try-it-out#testing-in-production) (all payments are for real)
* Custom bank [statement descriptors](https://articles.braintreepayments.com/control-panel/transactions/descriptors) (what your customers will see on their statement when they make a purchase through your webshop)
* Support for [partial/complete refunds](https://articles.braintreepayments.com/control-panel/transactions/refunds-voids-credits) from the order level with one-click (requires WC 2.1+)
* Support for [recurring billings](http://developers.braintreepayments.com/ios+php/guides/recurring-billing/overview)
* Support for debugging (all operations are logged in a custom log file accessible from within [WooCommerce admin dashboard](https://docs.woothemes.com/document/understanding-the-woocommerce-system-status-report/))
* Sets the [order status](http://docs.woothemes.com/document/managing-orders/) automatically (Completed/Failed/Refunded); nevertheless it adds an order note with each payment status change 

<a name="key_note"></a>(*) Please note that some of these features are not included in the free version. See [here](http://mynixworld.info/shop/woo-mynix-braintree-comparison) a comparison between the free and Pro versions. Check also the [30+ full feature list](http://mynixworld.info/shop/woo-mynix-braintree-full-features-list) or the [40+ screenshots gallery](http://mynixworld.info/shop/woo-mynix-braintree-screenshots) that reveals the most of these features at work.

> It was tested and works with IE8+ and all versions of Chrome, Firefox or Opera. It works both on desktop systems as well as on mobile devices. Works on any IIS6+/Apache/Nginx web server that has PHP5.3+, WordPress 3.3+ and WooCommerce 1.6.6+ up and running. Nonetheless it is WordPress multisite aware.

= Premium Support =

> The users of the free version hosted by wordpress.org are welcome to use our free online support resources such as [guides](http://mynixworld.info/shop/getting-started/woo-mynix-braintree), [tutorials](http://mynixworld.info/shop/tutorials/woo-mynix-braintree), [FAQ](http://mynixworld.info/shop/faq-woo-mynix-braintree), [Knowledge Base](http://mynixworld.info/shop/woo-mynix-braintree/knowledge-base) and [YouTube channel](#) videos. Read more [here](http://mynixworld.info/shop/get-support/).
> 
> However, if you need dedicated one-time assistance regarding installation, getting/setting the right Braintree credentials, internal/sandbox testing, or if you just need ongoing support, we are here to help you. More about this [here](http://mynixworld.info/shop/shop/premium-support).


= Woo Braintree Payment - PRO version =

This plug-in comes in two different flavors:

* Woo Braintree Payment - the free edition of this plug-in hosted at WordPress.org. This edition should be just fine for the average merchant.
* [Woo Braintree Payment - PRO](http://mynixworld.info/shop/product/woo-mynix-braintree-pro) - the premium edition which is hosted by [ourselves](http://mynixworld.info/shop/). It is oriented towards those merchants who need a more robust and customizable WooCommerce credit card payment plug-in. For a comparison between the two editions please see a [features comparison matrix](http://mynixworld.info/shop/woo-mynix-braintree-comparison/).

= How it works =

1. First you need to [sign up for a Braintree account](https://www.braintreepayments.com/signup). You will get an unique [merchant ID](https://articles.braintreepayments.com/control-panel/important-gateway-credentials) which is linked to a single bank account/currency. You may setup your Braintree account such that [you may have different bank accounts corresponding to different currencies](https://articles.braintreepayments.com/get-started/currencies).
2. When the customer places a shopping order the plug-in will send on your behalf a request to the Braintree Payment Gateway to authorize the sale transaction. By doing that the Braintree will charge your customer the amount specified by the customer order's total amount.
3. If the transaction is authorized (ie. the card is valid and has enough funds for the transaction) then in order to collect the founds the transaction is going to be settled. After a transaction is settled, funds will pass through your merchant account to your bank account.   

[Read this](https://articles.braintreepayments.com/get-started/transaction-life-cycle) to understand the transaction life cycle from the merchant's perspective or [maybe this](https://developers.braintreepayments.com/ios+php/start/overview#how-it-works) to get an in depth technical insight.

= Localization =

* English (default) - always included
* .pot file (`default.po`) for translators is also included
* *Want to contribute with your language? [Translations are welcome](http://mynixworld.info/shop/localization/woo-mynix-braintree)*

= Feedback =

* I am open to suggestions. The feedback is welcome. Thank you for using or trying out one of [my plug-ins](https://profiles.wordpress.org/eugenmihailescu#content-plugins)!
* Drop me a line [@eugenmihailescu](http://twitter.com/eugenmihailescu) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/eugenmihailescu)
* Or follow me on [+Eugen Mihailescu](http://plus.google.com/+EugenMihailescu) on Google Plus ;-)
 
== Installation ==

[Please read our complete installation tutorial](http://mynixworld.info/shop/tutorials/woo-mynix-braintree#install).

== FAQ ==

The answers to the most frequently asked questions can be found at [http://mynixworld.info/shop/faq-woo-mynix-braintree](http://mynixworld.info/shop/faq-woo-mynix-braintree).

== Screenshots ==

1. The basic options of Braintree Payment Gateway plug-in for WooCommerce
2. The common settings for checkout form customization
3. The customization of the Custom card fields on the checkout form
4. How the Custom card fields integrates withing the checkout form
5. The customization of the Drop-in UI card fields on the checkout form
6. How the Drop-in UI integrates within the checkout form
7. The customization of the Hosted Fields card on the checkout form
8. How the Hosted Fields integrates within the checkout form
9. The customization of the PayPal button on the checkout form
10. How the PayPal button integrates within the Custom card checkout form
11. The options that boost functionalities of Braintree payment method (PRO version only)
12. The options that secure the Braintree payments and/or lower the payment risk (PRO version only)
13. The option that allows recurrent payment via Braintree Recurring Billing Plans  
14. The log file that keeps the track of all events send/received from the Braintree gateway
15. The debug console log that helps in case of 'It does not work. Why?' 
16. How the Braintree's card form looks in your WooCommerce checkout page
17. How to refund automatically a payment made with Braintree payment method
18. It adds notes to the order so you can track all events from the first user attempt to its successful payment  
19. How to retrieve your Braintree Sandbox/Production merchant ID, public and private keys (Braintree dashboard)
20. How to retrieve your Braintree Merchant Account ID for different currencies(Braintree dashboard)
21. How to submit manually a Braintree payment for settlement (Braintree dashboard)
22. The 3D Secure layer authentication screen where the buyer enters his/her secret password
23. The card form adapts to any theme and layout, small, large, medium. It just works!
24. The same theme but now on a 360x640 display
25. Other WordPress theme, our Custom UI form on a 320x480 display

== Changelog ==

Please visit [Woo Braintree Payment blog](http://mynixworld.info/shop/blog/woo-mynix-braintree) for a more detailed version of changelog.

<h4>0.2-1</h4><ul><li><strong>[fix]</strong> PCI-badge icon CSS: auto-width and padding</li>
<li><strong>[fix]</strong> fixed `please wait` blockUI position on order submit</li>
<li><strong>[new]</strong> added option to enable/disable the blockUI `please wait` layer on order submit</li>
</ul>
<h4>0.2</h4><ul><li><strong>[update]</strong> Automatic migration to latest Braintree API</li></ul>
<h4>0.1-22</h4><ul><li><strong>[update]</strong> WordPress 4.7 compatible</li></ul>
<h4>0.1-19</h4><ul><li><strong>[update]</strong> added the processor response message to the UI message in case of declined payments</li>
<li><strong>[fix]</strong> fixed compatibility with WooCommerce 2.6 and later</li></ul>
<h4>0.1-18</h4><ul><li><strong>[fix]</strong> fixed the payment handler that induced the 500 Internal Error</li></ul>
<h4>0.1-17</h4><ul><li><strong>[update]</strong> Braintree Client API library - requires PHP 5.4 or newer</li>
<li><strong>[update]</strong> prevent the collition of the global variable name of autoloader class </li>
<li><strong>[fix]</strong> hide the initialization JS scripts while payment method not enabled</li>
<li><strong>[fix]</strong> make sure required values are entered before saving settings</li>
<li><strong>[new]</strong> Italian localization</li>
</ul>
<h4>0.1-16</h4><ul>


<li><strong>[tweak]</strong> Custom UI card form expiry field accepts MM/YYYY date</li>
<li><strong>[tweak]</strong> Custom UI card form CCV field toggles the numeric keypad on mobile devices</li>
<li><strong>[tweak]</strong> PayPal button container is now entirely clickable</li>
<li><strong>[new]</strong> PayPal logo within card's badges</li>

<li><strong>[improvement]</strong> rendering PayPal button on payment method change</li>

<li><strong>[improvement]</strong> blocking the checkout form and displaying `Please wait...` message while sending payment  </li></ul>
<h4>0.1-15</h4><ul><li><strong>[new]</strong> added `Reset settings` which allows starting with factory settings</li>
<li><strong>[new]</strong> added `PCI compliance badge` which allows displaying a PCI badge under card form</li>
<li><strong>[new]</strong> added `PayPal button` which integrates PayPal payments via Braintree gateway</li>


<li><strong>[improvement]</strong> reworked the admin settings look & feel (feels cleaner and lighter)</li>
<li><strong>[improvement]</strong> refactored the code to leverage the maintenance/updates</li></ul>
	

== Upgrade Notice ==

Upgrade Woo Braintree Payment to the latest version to make sure you benefit the latest improvements and bug fixes.
   
== Translations ==

* English - default, always included
* Italian - maintained by [Sergio Peirone](mailto:sergio@10thplanet.company)

*Note:* The plug-in is localized/translatable by default. Please contribute your language to the plug-in to make it even more useful. For translating I recommend the ["PoEdit" application](http://poedit.net/).
