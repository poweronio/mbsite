=== BP Profile as Homepage Fork ===
Contributors: Mort3n
Tags: Buddypress, Profile, Homepage, Login, Redirection
Requires at least: 3.5.1
Tested up to: 3.6
Stable tag: 1.1.3
License: GPLv2

== Description ==

This plugin lets you have a normal site Homepage for visitors while logged-in users have their BP Profile as Homepage. This is similar to Facebook. The redirection can be disabled for a particular role.

** When a user logs in, (s)he is redirected to the BP Profile.

** When a logged-in user goes to the site Homepage, (s)he is redirected to the BP Profile.

** When a user logs out, (s)he is redirected to the site Homepage.

The plugin is successfully tested with WordPress 3.6 and BuddyPress 1.8.1

Note: This plugin is a fork of the excellent plugin http://wordpress.org/extend/plugins/bp-profile-as-homepage/ by Jatinder Pal Singh. This fork updates and improves the code in a variety of ways.

== Installation ==

1. Upload `bp-profile-as-homepage-fork` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under Settings -> BP Profile as Homepage Fork, the plugin redirection can be disabled for a particular role. By default, the plugin redirection is disabled for No one.

== Frequently Asked Questions ==

= How is this plugin best used? =

It is suggested that you should have a static HOMEPAGE for visitors which allows registration. Once a user has logged in BuddyPress, (s)he will be redirected to the BP Profile. While being logged in the user is redirected to the BP Profile when clicking Homepage links or the logo of the website. This provides the user with easy access to the BP Profile.

= Is the plugin compatible with earlier version og WP and BP =

The plugin is very likely to be compatible with earlier versions of WordPress and BuddyPress, at least to WP 3.2 and BP 1.5. However, backwards compatibility has not been tested.

= Possible conflicts with this plugin? =

The plugin may conflict with other redirection plugins. There are no known plugin conflicts.

= Is this plugin ready for translation? =

Yes.

== Screenshots ==

No screenshots.

== Changelog ==
= 1.1.3 =
* Removed code used for debugging
= 1.1.2 =
* Updated .POT file
= 1.1.1 =
* Logic behind role checking redone
= 1.1 =
* Status codes are no longer returned
* Separated redirects after login and on homepage for logged in users
* Removed use of $bp global variable
= 1.0 =
* Just forked from http://wordpress.org/extend/plugins/bp-profile-as-homepage/
* Now returns status code 302. This is required by some browsers and firewalls to allow the plugin redirection
* Removed use of deprecated functions
* Now uses nonces for verification
* Now uses redirect function as described in the Codex
* Now handles custom roles
* Now fully ready for localization