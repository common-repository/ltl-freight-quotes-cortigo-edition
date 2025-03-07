=== LTL Freight Quotes - Cortigo Edition ===
Contributors: Eniture Technology
Tags: Cortigo,LTL,LTL freight,freight,shipping,shipping rates,shipping calculator,shipping estimate,estimator,carriers, woocommerce,woocommerce shipping,eniture,eniture technology,shipping quotes,eniture ltl freight quotes
Requires at least: 5.2
Tested up to: 5.6
Stable tag: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Real-time LTL freight quotes from Cortigo. Fifteen day free trial.

== Description ==

Cortigo (http://cortigo.com) is a third party logistics company that gives its customers access
to UPS and over 60 LTL freight carriers through a single account relationship. The plugin retrieves 
the LTL freight rates you negotiated Cortigo, takes action on them according to the plugin settings, and displays the 
result as shipping charges in your WooCommerce shopping cart. To establish a Cortigo account call 1.800.734.5351.

**Key Features**

* Three rating options: Cheapest, Cheapest Options and Average.
* Custom label results displayed in the shopping cart.
* Control the number of options displayed in the shopping cart.
* Display transit times with returned quotes.
* Restrict the carrier list to omit specific carriers.
* Product specific freight classes.
* Support for variable products.
* Option to determine a product's class by using the built in density calculator.
* Option to include residential delivery fees.
* Option to include fees for lift gate service at the destination address.
* Option to mark up quoted rates by a set dollar amount or percentage.

**Requirements**

* WooCommerce 4.0 or newer.
* A Cortigo shipper ID.
* Your username and password to Cortigo's online shipping system.
* Your Cortigo web services authentication key.
* A license from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* Your Cortigo account number.
* Your username and password to Cortigo's online shipping system.
* Your Cortigo web services authentication key.

If you need assistance obtaining any of the above information, contact your local Cortigo office
or call the [Cortigo](http://cortigo.com/) corporate headquarters at 1.800.734.5351.

A more extensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/ltl-freight-quotes-cortigo-edition/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "eniture ltl freight quotes", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get a license from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/ltl-freight-quotes-cortigo-edition/) and pick a
subscription package. When you complete the registration process you will receive an email containing your license key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your licenses and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the license. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings => Cortigo Quotes. Use the *Connection* link to create a connection to your Cortigo
account.

**4. Identify the carriers**
Go to WooCommerce => Settings => Cortigo Quotes. Use the *Carriers* link to identify which carriers you want to include in the 
dataset used as input to arrive at the result that is displayed in your cart. Including all carriers is highly recommended.

**5. Select the plugin settings**
Go to WooCommerce => Settings => Cortigo Quotes. Use the *Quote Settings* link to enter the required information and choose
the optional settings.

**6. Define warehouses and drop ship locations**
Go to WooCommerce => Settings => Cortigo Quotes. Use the *Warehouses* link to enter your warehouses and drop ship locations.  You should define at least one warehouse, even if all of your products ship from drop ship locations. Products are quoted as shipping from the warehouse closest to the shopper unless they are assigned to a specific drop ship location. If you fail to define a warehouse and a product isn’t assigned to a drop ship location, the plugin will not return a quote for the product. Defining at least one warehouse ensures the plugin will always return a quote.

**7. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the Shipping Zones link. Add a US domestic shipping zone if one doesn’t already exist. Click the “+” sign to add a shipping method to the US domestic shipping zone and choose SEFL from the list.

**8. Configure your products**
Assign each of your products and product variations a weight, Shipping Class and freight classification. Products shipping LTL freight should have the Shipping Class set to “LTL Freight”. The Freight Classification should be chosen based upon how the product would be classified in the NMFC Freight Classification Directory. If you are unfamiliar with freight classes, contact the carrier and ask for assistance with properly identifying the freight classes for your  products.

== Frequently Asked Questions ==

= What happens when my shopping cart contains products that ship LTL and products that would normally ship FedEx or UPS? =

If the shopping cart contains one or more products tagged to ship LTL freight, all of the products in the shopping cart 
are assumed to ship LTL freight. To ensure the most accurate quote possible, make sure that every product has a weight 
and dimensions recorded.

= What happens if I forget to identify a freight classification for a product? =

In the absence of a freight class, the plugin will determine the freight classification using the density calculation method. 
To do so the products weight and dimensions must be recorded.

= Why was the invoice I received from Cortigo more than what was quoted by the plugin? =

One of the shipment parameters (weight, dimensions, freight class) is different, or additional services (such as residential 
delivery, lift gate, delivery by appointment and others) were required. Compare the details of the invoice to the shipping 
settings on the products included in the shipment. Consider making changes as needed. Remember that the weight of the packaging 
materials,such as a pallet, is included by the carrier in the billable weight for the shipment.

= How do I find out what freight classification to use for my products? =

Contact your local Cortigo office for assistance. You might also consider getting a subscription to ClassIT offered 
by the National Motor Freight Traffic Association (NMFTA). Visit them online at classit.nmfta.org.

= How do I get a Cortigo account number? =

Cortigo is a US national franchise organization. Check your phone book for local listings or call its corporate 
office at 1.800.734.5351 and ask how to contact the sales office serving your area.

= Where do I find my Cortigo username and password? =

Usernames and passwords to Cortigo’s online shipping system are issued by Cortigo. Contact the Cortigo office servicing your account to request them. If you don’t have a Cortigo account, contact the Cortigo corporate office at 1.800.734.5351.

= Where do I get my Cortigo authentication key? =

You can can request an authentication key by logging into Cortigo’s online shipping system (cortigo.com) and 
navigating to Services > Web Services. An authentication key will be emailed to you, usually within the hour.

= How do I get a license key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or 
purchased a license outright. At the conclusion of the registration process an email will be sent to you that will include the 
license key. You can also login to eniture.com using the username and password you created during the registration process 
and retrieve the license key from the My Licenses tab.

= How do I change my plugin license from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My Licenses tab. There you will be able to manage the licensing of all of your 
Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site license. To use it on another website you will need to purchase an additional license. 
If you want to change the website with which the plugin is registered, login to eniture.com and navigate to the My Licenses tab. 
There you will be able to change the domain name that is associated with the license key.

= Do I have to purchase a second license for my staging or development site? =

No. Each license allows you to identify one domain for your production environment and one domain for your staging or 
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings > Cortigo Quotes > Connections) 
then you have one or more of the following licensing issues:

1) You are using the license key on more than one domain. The licenses are for single sites. You will need to purchase an additional license.
2) Your trial period has expired.
3) Your current license has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and go to the My Licenses tab to resolve any of these issues.

== Screenshots ==

1. Carrier inclusion page
2. Quote settings page
3. Quotes displayed in cart

== Changelog ==

= 2.1.0 =
* Update: Compatibility with WordPress 5.6

= 2.0.6 =
* Update: Compatibility with WordPress 5.5

= 2.0.5 =
* Update: Compatibility with WordPress 5.1

= 2.0.4 =
* Fix: Selection of carrier list.

= 2.0.3 =
* Fix: Corrected user guide link.
    
= 2.0.2 =
* Update: Compatibility with WordPress 5.1
	
= 2.0.1 =
* Fix: Identify one warehouse and multiple drop ship locations in basic plan.

= 2.0.0 =
* Update: Introduced new features and Basic, Standard and Advanced plans.

= 1.1.0 =
* Update: Introduced compatibility with the Residential Address Detection plugin.

= 1.0.1 =
* Fix: Corrected user guide link.

= 1.0 =
* Initial release.

== Upgrade Notice ==

