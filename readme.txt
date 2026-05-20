=== Display Site Numbers  ===
=======================


Contributors: fmarzocca, dylan-k
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=58XFDXEP4336Y
Tags: widget, count posts, count categories, counters
Requires at least: 4.0.1
Tested up to: 7.0
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget and shortcode to display all relevant site content numbers

== Description ==

= Widget =

The widget will show (and automatically update) the following site-content counters:

* Categories count
* Posts count
* Pages count
* Images count
* Tags count
* Comments count
* Authors count

In the admin backend, user can select what counter(s) to display.

= Shortcode =

Add this shortcode in your page/post:

<pre><code>
[DSN-list]
</code></pre>
to show all the 6 counters. If you want to specify what counter(s) to show, use this syntax;

<pre><code>
[DSN-list show="Categories, Comments, Posts]
</code></pre>

Valid terms in the list are: Categories, Comments, Posts, Pages, Authors, Images, Tags.


> If you like the plugin, feel free to rate it (on the right side of this page) or [donate via PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=58XFDXEP4336Y). Thanks a lot! :)

== Installation ==

1. Upload `display-site-numbers` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the widget 

== Frequently Asked Questions ==

= Can I customize the style in the widget? =
   Yes, all elements are included in *DSN-wrapper* div. Left items are in the *item* class, while counters are in *count* class. You can override them in your child theme style.css

== Screenshots ==

1. The admin backend
2. The widget frontend
3. Another view of the frontend, with a different style
4. A view of the shortcode output in a post/page.

== Changelog ==

= 1.0 =
* Adding support for custom post types

= 1.0 =
* Implementing Selective Refresh Support

= 0.9 =
* Ensuring compatibility with WP 4.3
* Removed deprecated php4 code
* Ready for php7

= 0.8 =
* Added Norwegian localization

= 0.7 =
* Added pages count

= 0.6 =
* Added shortcode

= 0.5 =
* First working version
