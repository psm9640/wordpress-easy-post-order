=== Post Order ===
Contributors: marraman
Tags: drag-and-drop, post-order, custom post types, admin, reorder posts, sort posts
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allows for drag-and-drop ordering of posts for specific custom post types from an admin interface. 

== Description ==

Post Order allows administrators to manage the order of posts within specific post types using a drag-and-drop interface. By selecting which post types the plugin applies to via the settings page, administrators can reorder posts in a user-friendly manner and have those orders reflected on both the front-end and back-end of the site.

**Features include:**
* Simple drag-and-drop interface for reordering posts.
* Select which post types are eligible for reordering via plugin settings.
* Works for any custom post type, including default post types such as "Posts" and "Pages."
* Applies custom post order across both front-end and admin queries.
* Lightweight plugin using jQuery UI for smooth sorting.
* Optionally use the post thumbnail for visual clarity during reordering.

== Installation ==

1. Upload the `post-order` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings > Post Order to configure the plugin.
4. Use the new "Order" submenu for each selected post type to drag and drop posts into your desired order.

== Frequently Asked Questions ==

= What post types can I reorder? =

You can reorder any public custom post type, including the default "Posts" and "Pages." You can select which post types you want to apply the ordering functionality to via the settings page.

= Does this plugin support custom taxonomies? =

This plugin focuses on ordering posts within custom post types, but it does not currently support custom taxonomy term ordering.

= Will this affect the order of posts on the front end of my website? =

Yes, the order you set will be reflected in all post queries, both in the admin and on the front-end, provided they are set to order by `menu_order`.

= Can I order WooCommerce products with this plugin? =

Yes, as long as the product post type is selected in the plugin settings, you can reorder WooCommerce products using the drag-and-drop interface.

= Do I need jQuery UI? =

jQuery UI is automatically enqueued when the plugin is active, so you do not need to manually add it.

== Screenshots ==

1. **Plugin Settings Page** - Easily select which post types the plugin applies to.
2. **Drag-and-Drop Interface** - Use a simple drag-and-drop interface to reorder posts.

== Changelog ==

= 1.0.4 =
* Added wrapper for better CSS scoping.
* Removed inline styles and improved CSS structure.
* Refined admin interface.

= 1.0.3 =
* Added plugin settings page to select post types.
* Added global query filter to ensure posts are ordered by `menu_order`.

= 1.0.2 =
* Initial release with drag-and-drop post reordering.

== Upgrade Notice ==

= 1.0.4 =
This update improves the CSS handling, ensuring styles are scoped correctly and no conflicts occur with other admin pages. It also removes all inline styles in favor of external CSS.
