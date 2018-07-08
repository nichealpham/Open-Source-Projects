=== Q2W3 Fixed Widget ===
Contributors: Max Bond
Tags: sidebar, widget, scroll, scrolling, fixed, fixed widget, floating, floating widget, sticky, sticky widget, russian, q2w3
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 5.0.4

Fixes positioning of the selected widgets, when the page is scrolled down. 

== Description ==

Enable "Fixed widget" option in the widget settings and it will be always in sight when page is scrolled down or up. There is no problem to "fix" or "stick" more than one widget even located in different sidebars!

[Live demo!](http://q2w3.ru/fixed-widget-demo/)

New in version 5.0:
1. Optimized client side performance. Detection of page changes is now based on MutationObserver API. Widget parameters recount is fired only when needed! Refresh interval option used only for campatibility with old browsers (no MutationObserver API support).
2. Improved compatibility with caching plugins (W3TC, Autoptimize and etc.). No need to exclude jQuery and plugin files from cache!
3. Async/Defer script load method support
4. Added `Disable Width` and `Disable Height` options

Note for cache plugins users. Don't forget to clear the cache after upgrading to version 5! Options format has been changed!

Compatibility note. The plugin is not working with all themes! Theme requirements:

* jQuery 1.7 required. jQuery 1.8.3 (or later) is recommended.
* No JavaScript errors, coused by other plugins and scripts.
* `wp_head()` and `wp_footer()` functions in header.php and footer.php files.
* Widgets must have an id attribute.

In some cases (widget "jumping" during scroll and etc.) theme CSS changes may be required.

Supported languages: 

* English
* Russian
* Spanish - [Ramón](http://apasionados.es) 
* French - [Murat](http://wptheme.fr)
* German - Stefan Meier

== Installation ==

1. Follow standard WordPress plugin installation procedure
2. Activate the plugin through the Plugins menu in WordPress
3. Go to Appearance -> Widgets, enable "Fixed Widget" option on any active widget
4. Fine tune plugin parameters on Appearance -> Fixed Widget Options page


== Frequently Asked Questions ==

= Why plugin is not working? =

There are several reasons:

1. Javascript errors on page. Commonly caused by buggy plugins. Check javascript console of your browser. If you find errors, try to locate and fix its source. 
2. No `wp_head()` and `wp_footer()` functions in template. Check header.php and footer.php files of your active theme.
3. Conflicts with other plugins and scripts
4. CSS incompatibility

= Why the plugin is not working in Chrome (and other Webkit based browsers)? =

Check your CSS files for these two instructions:
`-webkit-backface-visibility:hidden;
-webkit-transform: translate3d(0,0,0);`
If found, disable them and see the result.

= How to prevent overlapping with the footer? =

Go to WP admin area, Appearance -> Fixed Widget Options. Here you can define top and bottom margins. Set bottom margin value >= footer height. Check the result.
If your footer height is changing from page to page it is better to use `Stop ID` option. Here you need to provide html tag id. The position of that html element will determine margin bottom value. For example let's take Twenty Sixteen default theme. Theme's footer container has an id="colophon". In the `Stop ID` option I need to enter just colophon, without any other symbols!

= How to disable the plugin on mobile devices? = 
There are two options: `Disable Width` and `Disable Height`. They works the same way. If browser window width/height is less then or equals specified value - the plugin is disabled.


== Other Notes ==

Other Q2W3 plugins:

* [Code Insert Manager](http://wordpress.org/extend/plugins/q2w3-inc-manager/)


== Changelog ==

= 5.0.4 =
* Compatibility patch for Better Wordpress Minify plugin.

= 5.0.3 =
* Improved solution for "q2w3_sidebar_options is not defined" error.

= 5.0.2 =
* Plugin javascript optimization
* To resolve "q2w3_sidebar_options is not defined" error `wp_add_inline_script` function is used. WordPress 4.5 required for this fix!
* Added option `Disable MutationObserver`. Use this option only as a backup to restore version 4 behavior!

= 5.0.1 =
* Fixed problem in multiple sidebars layout

= 5.0 =
* Optimized client side performance. Detection of page changes is now based on MutationObserver API. Widget parameters recount is fired only when needed! Refresh interval option used only for campatibility with old browsers (no MutationObserver API support).
* Improved compatibility with caching plugins (W3TC, Autoptimize and etc.). No need to exclude jQuery and plugin files from cache!
* Async/Defer script load method support
* Added `Disable Width` and `Disable Height` options
* Note for cache plugins users: don't forget to clear cache after upgrading to version 5! Options format has been changed!

= 4.1 =
* Added `Stop ID` option. Use it when you cannot specify `Margin Bottom` value. Solution provided by [Julian_Kingman](https://wordpress.org/support/profile/julian_kingman)!
* Now the plugin is aware of the Wordpress admin bar presence!
* Fixed destruction of `jQuery(window).load` hook. There should be no problems with other jQuery plugins now!
* Added German translation
* Updated internationalization support

= 4.0.6 =
* A small [bug fix](http://wordpress.org/support/topic/widget-gets-wider-when-it-reaches-the-top)
* Added French translation

= 4.0.5 =
* New option "Inherit widget width from the parent container" to better support responsive layouts.
* Javascript optimization.

= 4.0.4 =
* Added option "Auto fix widget id". It is on by default. If the plugin is working with this option switched off - leave it in off position!  

= 4.0.3 =
* Optimized code to resolve [plugin crash after 4.0.1 update](http://wordpress.org/support/topic/the-plugin-crash-after-401-update) problem
* Minified javascript code

= 4.0.1 =
* Hotfix! Removes problem with duplicated widget code.

= 4.0 =
* Resolved [widget jumping](http://wordpress.org/support/topic/widgets-below-fixed-widgets-jump-up)
* Added code to automatically fix "widget id problem"
* Added new compatibility option (plugin priority)
* Added complete uninstall (uninstall script launched automatically when you DELETE plugin)
* Added Spanish translation
* Removed depricated options

= 3.0 =
* This version brings you a long waited capability to stick widgets located in different sidebars! Enjoy!
* Fixed conflict with WP Page Widget plugin 
* A few small bugs cleaned 
* Warning! "Disable plugin on mobile devices" and "Disable plugin on tablet devices" options now are depricated and will be removed in the next release. Use "Screen Max Width" option instead!

= 2.3 =
* Now user can disable plugin, when browser window width is less then specified value (check plugin options). 

= 2.2.4 =
* This version compatible with jQuery 1.9 and 1.10 

= 2.2.3 =
* Little internal improvments
* Mobile Detect updated to version 2.6.0

= 2.2.2 =
* Fixed PHP [Error](http://wordpress.org/support/topic/breakes-with-php-53)
* Mobile Detect updated to version 2.5.8

= 2.2.1 =
* Fixed PHP [Warning](http://wordpress.org/support/topic/error-with-the-new-update-22)

= 2.2 =
* Now the plugin is able to reflect dynamic page content changes (infinite scroll, ajax basket and other javascript stuff)!!!
* Added new option to plugin settings: Refresh interval. Recommended values between 500 - 2000 milliseconds. Note: setting have impact on the site performance (client side). If you don't have dynamic content, set Refresh interval = 0. 
* Mobile Detect class updated to version 2.5.7

= 2.1 =
* New option to define custom widget IDs for static sidebars and etc.
* New option to disable plugin on mobile devices.
* Fixed javascript error when no sidebars exists on a page.

= 2.0 =
* Fixed footer overlapping problem! Now users can customize top and bottom margins for the fixed widgets from the admin area (Appearance -> Fixed Widget Options).
* Added localization support

= 1.0.3 =
* Normalized plugin behavior when sidebar is longer then main content. Note: possible overlapping with footer is still exists.

= 1.0.2 =
* Fixed problem with widgets displayed only on certain pages.
* Optimized javascript code.

= 1.0.1 =
* Improved compatibility with Webkit based browsers (like Chrome and Safari).
* Removed unnecessary CSS.


= 1.0 =
* First public release.
