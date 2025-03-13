# Quick Edit Featured Image

This lightweight plugin allows to set and remove a Featured Image via the Quick Edit action screen in Post Type List Tables within the WordPress Admin. Out of the box this works for Posts, Pages and any public Post Type which supports Featured Images. (Can be disabled indiviually via code snippet or filter.)

### Tested Compatibility
- **WordPress**: 6.7.2
- **PHP**: 8.3+

---

[Support Project](#support-the-project) | [Installation](#installation) | [How Plugin Works](#how-this-plugin-works) | [Custom Tweaks](#custom-tweaks) | [Changelog](#changelog--releases) | [Plugin Scope / Disclaimer](#plugin-scope--disclaimer)

---

## Support the Project

If you find this project helpful, consider showing your support by buying me a coffee! Your contribution helps me keep developing and improving this plugin.

Enjoying the plugin? Feel free to treat me to a cup of coffee ☕🙂 through the following options:

- [![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/W7W81BNTZE)
- [Buy me a coffee](https://buymeacoffee.com/daveshine)

---

## Installation

**Quick Install**
1. Download [quick-edit-featured-image.zip](https://github.com/deckerweb/quick-edit-featured-image/releases/latest/download/quick-edit-featured-image.zip)
2. Upload via WordPress Plugins > Add New > Upload Plugin
3. Once activated, you’ll see the new admin column "Image" in the Post list table for example.

---

## How this Plugin Works

1. Adds a new column to the List Table for Posts, Pages, Post Types – if these support featured image and are public
2. Adds the featured image to "Quick Edit" (inline edit) – this saves a lot of clicks & time to set or remove a featured image for a lot of posts (no longer opening the post, setup, save, close tab or going back in browser ...)
3. Placeholder icon image for all Posts/ Post Types that have no featured image yet – clicking on icon opens quick edit

Saves time for admins, site builders and editors.

---

## Custom Tweaks

#### via Constant:

To exclude a post type from the adding of the Featured Image column, just add a constant to your `wp-config.php` file, to a functions.php (of theme or child theme) or via a code snippet plugin. Here's an example – define the constant and add an array of post type slugs (note the square brackets which forming the array!):
```
define( 'QEFI_DISABLED_TYPES', [ 'woohoo-post-type', 'book', 'download-manager-plugin' ] );
```

That would result in **NO** Featured Image column and quick edit feature for these post type slug: `woohoo-post-type`, `book` and `download-manager-plugin`


#### via Filter (for developers):

Developers can use the filter `'ddw/quick_edit/post_types_disable'` --> is defined in function `ddw_qefi_post_types_disable()`
This can be used to define which post types should not be supported (or should still be supported ...).

---

## Changelog / Releases

### 1.0.0
(currently being worked on ...)

---

## Plugin Scope / Disclaimer

This plugin comes as is.

_Disclaimer 1:_ So far I will support the plugin for breaking errors to keep it working. Otherwise support will be very limited. Also, it will NEVER be released to WordPress.org Plugin Repository for a lot of reasons (ah, thanks, Matt!).

_Disclaimer 2:_ All of the above might change. I do all this stuff only in my spare time.

_Most of all:_ Have fun building great sites!!! ;-)