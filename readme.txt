=== Plugin Name ===
Contributors: Ste-Bi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9401356
Tags: AppStore, Affiliate, iPhone, iPod, iPad, Apps, App, Download, Apple, Tradedoubler, appstore, widget, sidebar, top10, Mac AppStore, Mac, screenshots, phg
Requires at least: 2.8.0
Tested up to: 3.8.1
Stable tag: 3.8.1

Plugin for easy linking to (Mac) AppStore Apps. You can use the PGH-ID for automatically creating Affiliate-Links

== Description ==
Use this Plugin if you are tired in changing URLs and prices on every Link to the AppStore on your page. This plugin updates all Data directly from the Apple Server.  You also can setup caching-times for images and the content. If you have an PHG-ID the plugin automatically creates Affiliate Links to the AppStore.

You can easily add Links to the Apple AppStore with using [app ##idnumber##] in your posts, pages or comments. The ID number is the number from the official AppStore URL.

* [appimg ##idnumber##] gives you the screenshots!
* [appext ##idnumber##] gives you info, screenshots and a nice border.
* BETA (use with care): You can use something like: [app 307658513]<img src="{artwork60}" alt="Logo"/><strong>Developer:</strong>{trackname} 
<strong>Price:</strong>{price} {dllink} 
[/app]
* With these tags: {trackname}, {sellername}, {dllink}, {price}, {stars}, {description}, {artwork100}, {artwork60}
You can follow us for news on Twitter: http://twitter.com/AppStore_plugin

== Installation ==
1. Upload the `AppStore` Folder and its contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Setup the Plugin via the plugin-settings-page
4. You can install one or more Widgets from the Design->Widgets Section
5. Setup Widget

== Frequently Asked Questions ==
1. If you do not have an PGH ID leave it blank.
2. If you have an error like this: 
`Error in function _realLoadImage: The supplied filename '.../httpdocs/wp-content/plugins/appstore/cache/337617836.png' does not point to a readable file.` 
CHMOD the folder `cache` to 0777.
3. If the Plugin just prints "Kein Artikel gefunden" make sure that "allow_url_fopen" is set to "on". (Contact your provider for more details).
4. If statistic is not fetching data - deactivate and activate the plugin.
 
== Screenshots ==
1. Screenshot
AppStore Plugin 
2. Screenshot
Widget
3. Screenshot 
Widget Settings
4. Screenshot
Screenshots
5. Screenshot
Settings
6. Screenshot
Translation example

== Changelog ==
= 4.5.2 = 
* Removed Tradedoubler (in settings - if you are using it, it will work until you enter a phg-token
* Wasn't that change from Tradedoubler to phg easy as 123? hm?
* Settings are English and will not be translatet in future versions
* Bugfix

= 4.5.1 = 
* Bugfix 

= 4.5.0 = 
* Added support for PHG - iTunes Affiliate Program (will replace Tradedoubler)

= 4.4.9 = 
* when "Download-Link-Name" is empty, a Appstore (Mac-Appstore) image will be displayed (Tradedoubler asked for this feature)

= 4.4.8 =
* fixed CSS when Screenshots will be displayed vertical
* changed price to Apples "formattedPrice"

= 4.4.7 =
* fetching Currency from Apple
* fixed some Countrycodes
* Multiple-Country-Caching now possible
* Fixed a bug when displaying screenshots of iPpad-Only apps 
* Addet artwork512, averageuserrating, averageuserratingforcurrentversion, releasenotes to CustomCode
* Translation taiwanese (Thanks to @Clementtang)

= 4.4.6 = 
* Translation Espaniol (big thanks to Luis Twitter:@radeklu http://www.applepack.esç)
* &#165; yensign

= 4.4.5 = 
* Links in Widgets open in new windows
* Eurosign 

= 4.4.4 = 
* the real fix!

= 4.4.3 = 
* Quick fix for Problem with shortcode.php

= 4.4.2 = 
* Updatet Italian Translation
* Fixed allow_url_fopen=false in widget

= 4.4.1 = 
* Fixed: Appinmg not working

= 4.4.0 = 
* Changed from old regex to new wp-shortcode (better detection of shortcodes - Old code will also work)
* now you can use [app id="284417350"] or [app id=284417350 screenshots=true] too. (more flexible for future enhancements)
* Info: if no sceenshots are detected, no screenshots will be displayed)
* Fallback to CURL when allow_url_fopen = false
* Updatet Translations 
* Added Translations (Chinese,Italian)
* Translation to Chinese (big thanks to Emily)
* Translation to Italian (big thanks to Francesco)

= 4.3.2 = 
* Changed detection of Screenshots
* Bugfixes
* Added Link to configuration-video

= 4.3.1 = 
* Updatet translations (as far as I could)
* Added icon to the menu-item
* fixed problem with artwork
* temporary removed Screenshots from Mac Apps, because Apple does not deliver sceenshots as this time

= 4.3.0 = 
* Possibillity to use an alternative Affiliate Partner

= 4.2.3 =
* Also works in Spain (Thanks to Jose)
* Added a lot of Countries where the plugin should now work with Tradedoubler (Thanks to Tradedoubler)

= 4.2.2 =
* Fixed a bug when Tradedoubler says "There is no relationship..." This could already happen if there is no Tradedoubler Affiliate in your country. If you got this Problem and there is an TD-Affiliate Programm in your Country, please send me an working TD-Link (http://tirolercast.ste-bi.net/kontakt/)and I will fix this asap.
 (Thanks to @_AppForThat_) 
* Added Polish translation (Thanks to Bartosz)

= 4.2.1 =
* fix for wrong characters in Sellername (Widget)
* Statistics can be disabled
* Updatet translations

= 4.2.0 =
* Updatet Widged - More options (Top Free/paid/grossing vor Mac and iOs) but you have to re-setup your exiting ones (sorry for that)
* More Countries
* More fun ;-)

= 4.1.2 = 
* Added Button to TinyMCE - Just gives you a search in an exernal window with ID-Result (more coming soon)

= 4.1.1 = 
* Translation of the Admin Interface
* Added US and GB

= 4.1.0 = 
* Added ratings (optional)
* If not enough ratings no stars will be displayed!
* Changed detection of Mac Software because Apple removed the "kind" attribute 
* <Div> Tag for Download
* CSS: Changed IDs to Classes (Please change in custom CSS)

= 4.0.0 = 
* Added Finland
* When selecting english as language also the desctiptions in the plugin-results are english (changing the plugin-main-language to english follows in one of the next versions)
* Added new Image "Removed" when language not de_de
* Added more countries to the widget
* Removed error in statistiks when App was removed from AppStore
* Addet possibillity to add Screenshots with [appimg ##idnumber##]
* Addet possibillity to style the plugin (settings->style)
* Added a few more bods to the bod-filter in statistics
* Changed z-index from overlay (because of lightbox)

= 3.0.1 =
* Fixed a bug when writing to cachefile

= 3.0.0 =
* Addet tiny Statistic - jay

= 2.0.1 =
* shows korrekt size on iPad icons
* Added a simple Clickcounter
* Changed "id" to "appid"
* Fixed a bug in displaying "derzeit nicht im Appstore"

= 2.0.0 =
* NEW: Added Top 10 Widgets 
* Change: Apps are now defined with Stylesheet - solves problems on different Themes
* Change: using border-radius for pages with backgrounds other than white (does not work on any Browser)





