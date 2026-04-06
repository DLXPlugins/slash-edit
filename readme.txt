=== Slash Edit: Admin Shortcuts to Edit Posts and Pages Faster ===
Contributors: ronalfy
Tags: shortcuts, edit, edit post, edit page, quick-edit
Requires at least: 6.5
Tested up to: 7.0
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://mediaron.com/give/

Edit posts, pages, users, and more with a simple `/edit` URL shortcut. A fast admin shortcut for WordPress editing.

== Description ==

Slash Edit adds a simple **admin shortcut** to WordPress that lets you **edit posts and pages quickly** by adding `/edit` to any URL.

Instead of navigating through wp-admin or relying on the admin bar, just append `/edit` and go straight to the edit screen.

> https://yoursite.com/about/edit

This makes it easy to:

* **Edit posts quickly**
* **Edit pages quickly**
* Use a reliable **quick edit shortcut**
* Access a clean **admin shortcut to edit content**

https://www.youtube.com/watch?v=8LKFK5-FokE

== A True WordPress Shortcut for Editing ==

Slash Edit is built for speed. Whether you're a developer, editor, or site owner, it gives you a consistent **shortcut to edit posts and pages** without relying on the admin bar.

* No more searching through the dashboard  
* No more long admin URLs  
* Just a fast and predictable **edit shortcut**

== Replace Complex Admin URLs ==

Stop sharing links like:

> `https://yourdomain.com/wp-admin/post.php?post=123&action=edit`

Instead, send a clean **edit page shortcut**:

> `https://yourdomain.com/about/edit`

Perfect for clients and teams who need a simple way to **quick edit content** or for email clients who mangle URLs.

== What You Can Edit ==

Slash Edit works across your entire site:

* Posts and pages  
* Custom post types  
* Users  
* Categories and tags  
* Taxonomies  
* Author archives  
* Front page (if assigned)
* Blog page (if assigned) 

If you are not logged in, you will be prompted to log in and then redirected to the correct edit screen.

== Why Use This Edit Shortcut? ==

* You want to **edit posts and pages quickly**  
* You need a reliable **admin shortcut** without the admin bar  
* You want to send clients a simple **edit page link**  
* You prefer a fast **quick edit workflow**  

== Security ==

Only users with Editor capabilities (or higher) can access edit screens.

Developers can customize access using filters:

* `slash_edit_capability_check`  
* `slash_edit_can_edit`  

== Developer Friendly ==

* Filter the `/edit` endpoint if needed  
* Extend redirect behavior  
* Lightweight and no settings required  

== Installation ==

1. Just unzip and upload the "slash-edit" folder to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I use the plugin? =
Navigate to any post, page, or archive and add `/edit` to the end of the URL.

Example (editing a page):
> https://example.com/about/edit

Example (editing the homepage):

> https://example.com/edit

Example (editing a user):

> https://example.com/author/myuser/edit


= Do I need pretty permalinks enabled? =
Yes. Slash Edit requires pretty permalinks to work.

= Who can access edit links? =
Only users with Editor permissions or higher by default.

Developers can override this using the provided filters.

= What happens if I am not logged in? =
You will be redirected to the login screen and then back to the correct edit page.

= Does this work with custom post types and taxonomies? =
Yes. Slash Edit supports custom post types, taxonomies, and some archives.

= What happens on archive pages? =
That's still in the works for custom post types.

All term archives work.

Blog archives redirect to Settings > Reading.

= Can I change `/edit` to something else? =
Yes. You can use the provided filter `slash_edit_endpoint` and then flush permalinks.

= Are there any settings? =
No. The plugin works out of the box with no configuration required.

= Does this plugin work with WordPress Multisite? =
Yes, this plugin works wonderfully with Multisite.

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