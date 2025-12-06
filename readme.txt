=== Slash Edit ===
Contributors: ronalfy
Tags: admin, edit, edit post, edit page, quick-edit
Requires at least: 3.9.1
Tested up to: 6.9
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://mediaron.com/give/

Quickly edit your posts, pages, post types, users, archives, and terms with a simple "/edit" shortcut at the end.

== Description ==

Quickly edit posts, pages, custom post types, users, archives, and terms (tags and categories) by adding a "/edit" to the end of the URL.  If you are not logged in, you will be prompted to log in in order to edit the item. Please note that only those with Editor privileges and above can quick-edit content.

Send clients pretty URLs to the admin instead of a ton of query variables!

> `https://domain.com/about/edit` instead of `https://domain.com/wp-admin/post.php?post=5&action=edit`

The "/edit" functionality also works on author, taxonomy archives, post type archives, and if you have a page assigned as your front page of your site. If you try to edit from a blog archive, you'll be taken to Settings->Reading.

https://www.youtube.com/watch?v=8LKFK5-FokE

This is useful if:

* You are not logged in, and want an easy shortcut to edit an item.
* You hate the admin bar and have disabled it, but still want an easy shortcut to edit an item.
* You have clients. Send them a pretty URL with "/edit" on the end.

As a security precaution, only users with Editor privileges or above can edit items.

== Installation ==

1. Just unzip and upload the "slash-edit" folder to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= How do I use the plugin? =
Just browse to the posts, pages, post types, users, archives, and terms and add "/edit" to the end (e.g., http://domain.com/posts/edit).

You'll need <a href="https://wordpress.org/documentation/article/customize-permalinks/">pretty permalinks enabled</a>, which pretty much everyone already does.

= Who can edit items? =
Only Editors and above. This can be overridden by filters `slash_edit_capability_check` and `slash_edit_can_edit`.

= Will you allow quick editing of categories and other items later? =
You're able to edit any term or category item by adding `/edit` to the end.

= What about attachment pages? =
That one I couldn't figure out. Most themes disable this. Patches welcome.

= Where are the options? =
No options :)

= English is not my first language.  Can I change the "/edit" into something else? =

Yep, just throw <a href="https://gist.github.com/ronalfy/cbbc1599bda2811c9a86">this code</a> in a <a href="http://www.wpbeginner.com/beginners-guide/what-why-and-how-tos-of-creating-a-site-specific-wordpress-plugin/">Site-specific plugin</a>.

Just keep in mind that whatever you choose to override with must be alphanumeric characters.  Something like edición will be parsed as edicion.

If you choose to use this filter, you'll need to <a href="https://wordpress.org/documentation/article/settings-permalinks-screen/">update your permalinks</a> or deactivate and reactivate the Slash Edit plugin.

== Screenshots ==

1. Slash Edit when editing a post on the front-end.

== Changelog ==

= 1.2.0 =
* Updated 2025-12-06
* Security fix: Adding `/edit` can expose IDs that are otherwise private. Update for a better login workflow. As a security precaution, only those with Editor privileges and above can quickly edit items.
* New feature: Allow terms and child terms to be editable.
* New feature: Adding `/edit` to a blog archive redirects to Settings->Reading.
* New feature: Editing a post type archive will redirect to post list screen for the archive.
* Misc: Code cleanup throughout, updating to WPCS and passing Plugin Check.

= 1.1.1 =
* Updated 2015-08-20 - Ensuring WordPress 4.3 compatibility
* Updated 2015-04-19 - Ensuring WordPress 4.2 compatibility
* Updated 2014-12-11 - Ensuring WordPress 4.1 compatibility 
* Released 2014-11-13
* Fixing endpoint when page is created with same slug as the endpoint

= 1.1.0 =
* Released 2014-11-13
* Added "/edit" to the front of the site (e.g., www.domain.com/edit) if you have a page set as your front page.
* Added a `slash_edit_url` filter to determine where to redirect a user when "/edit" is present (props <a href="https://profiles.wordpress.org/bjornjohansen/">Bjørn J.</a>)

= 1.0.0 =
* Released 2014-10-19
* Initial Release

== Upgrade Notice ==

= 1.2.0 =
Security fix: New version no longer exposes IDs to non-logged-in users. Only those with Editor privileges or above can edit items. Code cleanup, and better ability to edit terms and archive items.