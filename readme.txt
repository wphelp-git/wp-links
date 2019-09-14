=== Ultimate Nofollow ===
Contributors: codecollective
Tags: nofollow,link,links,meta,insert,rel nofollow,seo,checkbox,popup,shortcode,blogroll
Requires at least: 2.8
Tested up to: 5.2.2
Stable tag: 1.4.8

Adds a checkbox in the insert link popup box for including rel="nofollow" in links as you create them; as well as other tools that provides complete control over the rel="nofollow" tag on your blogroll links and comments.

== Description ==

**CURRENTLY WORKING ONLY WITH CLASSIC EDITOR**

This plugin contains several tools in one package to significantly increase your control of the rel="nofollow" tag on every link on your blog, on both an individual and categorical basis. It is designed to give you fine-grained control of linking for SEO purposes.

The plugin's main features are: 

* The addition of a **nofollow checkbox when inserting links in posts**
* Several **nofollow shortcodes**
* A nofollow option for **individual blogroll links**
* Or a **nofollow for all blogroll links** option
* Add or remove the nofollow tag from **all links in comments**

== Installation ==
1. Download the latest zip file and extract the `nofollow` directory.
2. Upload it to your `/wp-content/plugins/` directory.
3. Activate `Ultimate Nofollow` on the `Plugins` menu in WordPress.

== Frequently Asked Questions ==

= How do I nofollow links I insert in my posts or pages? =
Just check the `add rel="nofollow" to link` option direction under the default `open link in a new window/tab` option.

= How do I use the shortcode? =
You can use any of these shortcodes to insert a nofollowed link `[relnofollow]`, `[nofollow]`, `[nofol]`, `[nofo]`, or `[nf]` using the following format:

`[nf href="http://link-url.com"]Link Text[/nf]`

You can also include the optional <a> attributes `title` and `target`. A full example would look like:

`[nf href="http://link-url.com" title="Link Title" target="_blank"]Link Text[/nf]`

== Screenshots ==
1. Nofollow option on the insert/edit link popup for posts and pages.
2. Settings page on the Dashboard.
3. Nofollow checkbox on the add/edit blogroll link page.

== Changelog ==
= 1.4.8 =
* Updated description for incoming update

= 1.4.7 =
* The Ultimate NoFollow plugin is now being solely maintained by [codecollective].

= 1.4.6 =
* Fixed some errors that were causing error messages on admin pages

= 1.4.5 =
* Fixed checkbox bug, thanks [keesromkes](https://wordpress.org/support/profile/keesromkes)

= 1.4.4 =
* Purely cosmetic WordPress 4.4 update

= 1.4.3 =
* Fixes compatibility issue with Wordpress 4.2
* Thanks to [Zoe Corkhill](https://profiles.wordpress.org/zoecorkhill/) for the fix

= 1.4.2 =
* Fixes compatability issue with WordPress 3.9

= 1.4.1 =
* Removed forgotten var_dump() left from debugging process

= 1.4 =
* Official release
* Added nofollow checkbox to individual post links

= 0.1.3.1 =
* Updates to documentation.

= 0.1.3 =
* Stable beta version.
* Nofollow checkbox added to the add/edit blogroll links dialogue.
* Adds option to nofollow all blogroll links.

= 0.1.2 =
* Stable beta version.
* Add/remove nofollow from all links in comments.

= 0.1.1 =
* Stable beta version.
* Adds full nofollow shortcodes.
* Adds options page.

= 0.1.0 =
* First released beta version.
* Stable, but not all functions active yet. 

== Upgrade Notice ==
The Ultimate NoFollow plugin is now being solely maintained by [codecollective].

= 1.4.6 =
Fixes error messages being shown on some admin pages.

= 1.4.5 =
Fixes checkbox bug introduced in WP 4.5.

= 1.4.4 =
Purely cosmetic WordPress 4.4 update. Happy New Year.

= 1.4.3 =
Required update if using WordPress 4.2 or above.

= 1.4.2 =
Required update if using WordPress 3.9 or above.

= 1.4.1 =
Strongly recommended upgrade, version 1.4 is dumping a NULL variable onto public facing pages.

= 1.4 =
Strongly recommended upgrade, significant improvement in usefulness.

= 0.1.3.1 =
Updated documentation.
