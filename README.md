# Quick Edit Featured Image

This lightweight plugin allows to set and remove a Featured Image via the Quick Edit action screen in Post Type List Tables within the WordPress Admin. Out of the box this works for Posts, Pages and any public Post Type which supports Featured Images. (Can be disabled indiviually via code snippet or filter.)

![Quick Edit Featured Image – Inline edit](https://raw.githubusercontent.com/deckerweb/quick-edit-featured-image/master/assets-github/screenshot-inline-edit.jpg)
Screenshot: example of inline edit view

![Quick Edit Featured Image – Admin column](https://raw.githubusercontent.com/deckerweb/quick-edit-featured-image/master/assets-github/screenshot-admin-column.jpg)
Screenshot: the added column in the list table, plus placeholder image icon

* Contributors: [David Decker](https://github.com/deckerweb), [contributors](https://github.com/deckerweb/quick-edit-featured-image/graphs/contributors)
* Tags: featured image, quick edit, admin, list table, post types, image column
* Requires at least: 6.7
* Requires PHP: 7.4
* Stable tag: [master](https://github.com/deckerweb/quick-edit-featured-image/releases/latest)
* Donate link: https://paypal.me/deckerweb
* License: GPL v2 or later

---

[Support Project](#support-the-project) | [Installation](#installation) | [Updates](#updates) | [How Plugin Works](#description) | [Custom Tweaks](#custom-tweaks) | [FAQ](#frequently-asked-questions) | [Changelog](#changelog) | [Plugin Scope / Disclaimer](#plugin-scope--disclaimer)

---

## Support the Project

If you find this project helpful, consider showing your support by buying me a coffee! Your contribution helps me keep developing and improving this plugin.

Enjoying the plugin? Feel free to treat me to a cup of coffee ☕🙂 through the following options:

- [![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/W7W81BNTZE)
- [Buy me a coffee](https://buymeacoffee.com/daveshine)
- [PayPal donation](https://paypal.me/deckerweb)
- [Join my **newsletter** for DECKERWEB WordPress Plugins](https://eepurl.com/gbAUUn)

---

## Installation 

#### **Quick Install**
1. **Download ZIP:** [**quick-edit-featured-image.zip**](https://github.com/deckerweb/quick-edit-featured-image/releases/latest/download/quick-edit-featured-image.zip)
2. Upload via WordPress Plugins > Add New > Upload Plugin
3. Once activated, you’ll see the new admin column "Image" in the Post list table for example.

### Tested Compatibility
- **WordPress**: 6.7.2 / 6.8 Beta
- **PHP**: 8.0 – 8.3

---

## Updates 

1) Alternative 1: Just download a new [ZIP file](https://github.com/deckerweb/quick-edit-featured-image/releases/latest/download/quick-edit-featured-image.zip) (see above), upload and override existing version. Done.

2) Alternative 2: Use the (free) [**_Git Updater_ plugin**](https://git-updater.com/) and get updates automatically.

3) Alternative 3: Upcoming! – In future I will built-in our own deckerweb updater. This is currently being worked on for my plugins. Stay tuned!

---

## Description 

#### How this Plugin Works

1. Adds a new column to the List Table for Posts, Pages, Post Types – if these support Featured Image and are public (post type needs support for `thumbnail` in WordPress terms)
2. Adds the Featured Image to **Quick Edit** (inline edit) – this saves a lot of clicks & time to set or remove a featured image for a lot of posts (no longer opening the post, setup, save, close tab or going back in browser ...)
3. Placeholder icon image for all Posts/ Post Types that have no featured image yet – clicking on icon opens _Quick Edit_

**Saves time for admins, site builders and editors!**

Note, the following post types are disabled by default because they either have thumbnail support or are internal
* _WooCommerce_ `product` (if WooCommerce is active)
* _Meta Box_ (including _AOI_ as well as _Lite_ plugin suites)  `meta-box`, `mb-post-type`, `mb-taxonomy`, `mb-relationship', `mb-settings-page`, `mb-views`

---

## Custom Tweaks

#### via Constant:

To exclude a post type from the adding of the Featured Image column, just add a constant to your `wp-config.php` file, to a functions.php (of theme or child theme) or via a code snippet plugin. Here's an example – define the constant and add an array of post type slugs (note the square brackets which forming the array!):
```
define( 'QEFI_DISABLED_TYPES', [ 'woohoo-post-type', 'book', 'download-manager-plugin' ] );
```

That would result in **NO** Featured Image column and **NO** quick edit feature for these post type slugs: `woohoo-post-type`, `book` and `download-manager-plugin`

NOTE: The declaration needs every post type slug to be in single quotes, comma separated and the whole thing enclosed in square brackets like in the example above. 


#### via Filter (for developers):

Developers can use the filter `'ddw/quick_edit/post_types_disable'` --> is defined in class method `post_types_disable()`
This can be used to define which post types should not be supported (or should still be supported ...).

Typical usage:
```
add_filter( 'ddw/quick_edit/post_types_disable', 'prefix_your_custom_function' );
function prefix_your_custom_function( $post_types_disable ) {

	// do your stuff
	
	return (array) $post_types_disable;
}
```

---

## Frequently Asked Questions 

### Why not using an admin columns plugin? 
Good question. But these type of plugins usually only tweak the appearance of the post type list table and add an image column. Most of them do not offer the quick editing.


### Why did you create this plugin? 
I saw and found the code snippet to achieve this feature. It was amazing and I know I wanted that for myself and all client sites. When looking at the code snippet I wanted some enhancements so the decision was made to make a plugin out of it.


### Why is this plugin not on wordpress.org plugin repository? 
Because the restrictions there for plugin authors are becoming more and more. It would be possible, yes, but I don't want that anymore. The same for limited support forums for plugin authors on .org. I have decided to leave this whole thing behind me.


---

## Changelog 

**The Releases**

### 🎉 v1.2.0 – 2025-04-06
* New: Installable and updateable via [Git Updater plugin](https://git-updater.com/)
* Improved: Script localization
* Update: `.pot` file, plus packaged German translations, now including new `l10n.php` files!

### 🎉 v1.1.0 – 2025-03-28
* New: Transformed code into class-based approach (more future-proof)
* New: Add info to Site Health Debug, useful for our constants for custom tweaking
* New: Added `.pot` file (to translate plugin into your language), plus packaged German translations
* Plugin: Add meta links on WP Plugins page

### 🎉 v1.0.0 – 2025-03-14
* Initial release – _Yeah!_
* Custom disabling for post types via constant or filter
* Plugin support: Disabled by default for _WooCommerce_ and _Meta Box_ post types

---

## Plugin Scope / Disclaimer

This plugin comes as is.

_Disclaimer 1:_ So far I will support the plugin for breaking errors to keep it working. Otherwise support will be very limited. Also, it will NEVER be released to WordPress.org Plugin Repository for a lot of reasons (ah, thanks, Matt!).

_Disclaimer 2:_ All of the above might change. I do all this stuff only in my spare time.

_Most of all:_ Have fun building great sites!!! ;-)

---

Icon used in promo graphics: [© Remix Icon](https://remixicon.com/)

Readme & Plugin Copyright: © 2025, David Decker – DECKERWEB.de