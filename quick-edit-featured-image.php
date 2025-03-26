<?php
/*
Plugin Name:  Quick Edit Featured Image
Plugin URI:   https://github.com/deckerweb/quick-edit-featured-image
Description:  This lightweight plugin allows to set and remove a Featured Image via the Quick Edit action screen in Post Type List Tables within the WordPress Admin. Out of the box this works for Posts, Pages and any public Post Type which supports Featured Images.
Version:      1.1.0
Author:       David Decker – DECKERWEB
Author URI:   https://deckerweb.de/
Text Domain:  quick-edit-featured-image
Domain Path:  /languages/
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Requires WP:  6.7
Requires PHP: 7.4
Copyright:    © 2025, David Decker – DECKERWEB
*/

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly.

if ( ! class_exists( 'DDW_Quick_Edit_Featured_Image' ) ) :

class DDW_Quick_Edit_Featured_Image {

	/** Class constants & variables */
	private const VERSION = '1.1.0';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		
		add_filter( 'manage_posts_columns',     array( $this, 'featured_image_column' ) );
		add_filter( 'manage_pages_columns',     array( $this, 'featured_image_column' ) );
		add_filter( 'manage_edit-post_columns', array( $this, 'featured_image_column' ) );
		add_filter( 'manage_edit-page_columns', array( $this, 'featured_image_column' ) );
		//add_filter( 'manage_edit-{post_type}_columns', array( $this, 'featured_image_column' ), 10, 2 );
		
		add_action( 'manage_posts_custom_column', array( $this, 'column_display_featured_image' ), 10, 2 );
		add_action( 'manage_pages_custom_column', array( $this, 'column_display_featured_image' ), 10, 2 );
		//add_action( 'manage_{post_type}_custom_column', array( $this, 'column_display_featured_image' ), 10, 2 );
		
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_featured_image' ), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_inline_styles_scripts' ) );
	}
	
	/**
	 * Get current Post Type in WordPress Admin Area
	 *
	 * @link https://wp-mix.com/get-current-post-type-wordpress/
	 *
	 * @global $post, $typenow, $current_screen
	 *
	 * @return string|null  String of post type slug if any detected, otherwise 'null'.
	 */
	private function get_current_post_type() {
		
		global $post, $typenow, $current_screen;
		
		if ( $post && $post->post_type ) return $post->post_type;
		
		elseif ( $typenow ) return $typenow;
		
		elseif ( $current_screen && $current_screen->post_type ) return $current_screen->post_type;
		
		elseif ( isset( $_REQUEST[ 'post_type' ] ) ) return sanitize_key( $_REQUEST[ 'post_type' ] );
		
		return null;
	}
	
	/**
	 * Get the Post Type slugs for which the plugin should NOT add anything.
	 *
	 * @return array $post_types_disable  Array of Post Type slugs.
	 */
	private function post_types_disable() {
		
		$wc_product = class_exists( 'WooCommerce' ) ? 'product' : '';
		
		$disabled_by_default = [ 'mb-post-type', 'mb-taxonomy', 'meta-box', 'mb-relationship', 'mb-settings-page', 'mb-views', $wc_product ];
		$disabled_by_user    = defined( 'QEFI_DISABLED_TYPES' ) ? (array) QEFI_DISABLED_TYPES : [];
		
		$post_types_disable = apply_filters(
			'ddw/quick_edit/post_types_disable',
			array_merge( $disabled_by_default, $disabled_by_user )
		);
		
		return $post_types_disable;
	}
	
	/**
	 * Reusable and translateable strings.
	 * Preset for German locales --> saves the use of a translation file ... :-)
	 *
	 * @uses get_user_locale()  If the user has a locale set to a non-empty string then it will be returned. Otherwise it returns the locale of get_locale().
	 * @uses get_post_type_object()
	 * @uses $this->get_current_post_type()
	 *
	 * @param string $type  Key of the string type to output.
	 * @return string $string  Key of used language string.
	 */
	private function image_strings( $type ) {
		
		$german = [ 'de_DE', 'de_DE_formal', 'de_AT', 'de_CH', 'de_LU' ];
		$locale = get_user_locale();
		
		$post_type            = get_post_type_object( $this->get_current_post_type() );
		$label_featured_image = $post_type->labels->featured_image;
		//$label_set_featured_image    = $post_type->labels->set_featured_image;
		//$label_remove_featured_image = $post_type->labels->remove_featured_image;
		
		$image                 = ( in_array( $locale, $german ) ) ? 'Bild' : __( 'Image', 'quick-edit-featured-image' );
		$featured_image        = ( in_array( $locale, $german ) ) ? $label_featured_image : __( 'Featured Image', 'quick-edit-featured-image' );
		$set_featured_image    = ( in_array( $locale, $german ) ) ? $label_featured_image . ' festlegen' : __( 'Set Featured Image', 'quick-edit-featured-image' );
		$remove_featured_image = ( in_array( $locale, $german ) ) ? $label_featured_image . ' entfernen' : __( 'Remove Featured Image', 'quick-edit-featured-image' );
		$image_ok              = ( in_array( $locale, $german ) ) ? $label_featured_image . ' ist gesetzt' : __( 'Featured Image is set', 'quick-edit-featured-image' );
		$placeholder           = ( in_array( $locale, $german ) ) ? 'Derzeit kein ' . $label_featured_image . ' festgelegt' : __( 'No Featured Image yet', 'quick-edit-featured-image' );
		
		/** Check string type */
		switch ( sanitize_key( $type ) ) {
		
			case 'image':
				$string = $image;
				break;
			case 'featured_image':
				$string = $featured_image;
				break;
			case 'set_featured_image':
				$string = $set_featured_image;
				break;
			case 'remove_featured_image':
				$string = $remove_featured_image;
				break;
			case 'image_ok':
				$string = $image_ok;
				break;
			case 'placeholder':
				$string = $placeholder;
				break;
			default:
				$type = '';
		
		}  // end switch
		
		return $string;
	}
	
	/**
	 * Add the Featured Image column to List Table. Bring it to 2nd place.
	 *
	 * @uses $this->get_current_post_type()
	 * @uses $this->post_types_disable()
	 * @uses $this->image_strings()
	 *
	 * @param  array $columns  Array of all list table column IDs.
	 * @return array $columns  Array of list table columns.
	 */
	public function featured_image_column( $columns ) {
		
		/** Bail early if not enabled for Post Type */
		if ( in_array( $this->get_current_post_type(), $this->post_types_disable() )
			|| ! is_post_type_viewable( $this->get_current_post_type() )
			|| ! post_type_supports( $this->get_current_post_type(), 'thumbnail' )
		) {
			return $columns;
		}
		
		$columns = array_slice( $columns, 0, 1, TRUE )
		+ array( 'qefi_featured_image' => esc_html( $this->image_strings( 'image' ) ) )  // our new column
		+ array_slice( $columns, 1, NULL, TRUE );
	
		return $columns;
	}
	
	/**
	 * Display the content of the Featured Image column.
	 *
	 * @uses $this->get_current_post_type()
	 * @uses $this->post_types_disable()
	 * @uses $this->image_strings()
	 *
	 * @param string $column_name  ID of the list table column.
	 * @param int    $post_id      ID of the current post.
	 */
	public function column_display_featured_image( $column_name, $post_id ) {
	
		/** Bail early if not enabled for Post Type */
		if ( in_array( $this->get_current_post_type(), $this->post_types_disable() )
			|| ! is_post_type_viewable( $this->get_current_post_type() )
			|| ! post_type_supports( $this->get_current_post_type(), 'thumbnail' )
		) {
			return;
		}
	
		$edit        = esc_url( get_edit_post_link( $post_id ) );
		$image_ok    = esc_html( $this->image_strings( 'image_ok' ) );
		$placeholder = esc_html( $this->image_strings( 'placeholder' ) );
		
		$id  = absint( get_post_thumbnail_id( $post_id ) );
		$url = esc_url( wp_get_attachment_image_url( $id ) );
	
		if ( 'qefi_featured_image' === $column_name ) {
	
			if ( has_post_thumbnail( $post_id ) ) {
				
				/** When Featured Image is set, display it */
				?>
					<a href="<?php echo $edit; ?>">
						<img data-id="<?php echo $id ?>" src="<?php echo $url ?>" title="<?php echo $image_ok; ?>" />
					</a>
				<?php
	
			} else {
				
				/** When no Featured Image is set yet, display a placeholder image icon */
				?>
					<span class="button-link editinline">
						<img data-id="-1" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0icmdiYSgxNzMsMTg0LDE5NCwxKSI+PHBhdGggZD0iTTIxIDE1VjE4SDI0VjIwSDIxVjIzSDE5VjIwSDE2VjE4SDE5VjE1SDIxWk0yMS4wMDgyIDNDMjEuNTU2IDMgMjIgMy40NDQ5NSAyMiAzLjk5MzRWMTNIMjBWNUg0VjE4Ljk5OUwxNCA5TDE3IDEyVjE0LjgyOUwxNCAxMS44Mjg0TDYuODI3IDE5SDE0VjIxSDIuOTkxOEMyLjQ0NDA1IDIxIDIgMjAuNTU1MSAyIDIwLjAwNjZWMy45OTM0QzIgMy40NDQ3NiAyLjQ1NTMxIDMgMi45OTE4IDNIMjEuMDA4MlpNOCA3QzkuMTA0NTcgNyAxMCA3Ljg5NTQzIDEwIDlDMTAgMTAuMTA0NiA5LjEwNDU3IDExIDggMTFDNi44OTU0MyAxMSA2IDEwLjEwNDYgNiA5QzYgNy44OTU0MyA2Ljg5NTQzIDcgOCA3WiI+PC9wYXRoPjwvc3ZnPg==" title="<?php echo $placeholder; ?>" />
					</span>
				<?php
				
			}  // end if
			
		}  // end if
	}
	
	/**
	 * Bring the Featured Image action within the Quick Edit view.
	 *
	 * @uses $this->get_current_post_type()
	 * @uses $this->post_types_disable()
	 * @uses $this->image_strings()
	 *
	 * @param string $column_name  ID of the list table column.
	 * @param string $post_type    Slug ID of the post type.
	 */
	function quick_edit_featured_image( $column_name, $post_type ) {
	
		/** Bail early if not enabled for Post Type */
		if ( in_array( $this->get_current_post_type(), $this->post_types_disable() )
			|| ! is_post_type_viewable( $this->get_current_post_type() )
			|| ! post_type_supports( $this->get_current_post_type(), 'thumbnail' )
		) {
			return;
		}
	
		/** Bail early if there's not our column */
		if ( 'qefi_featured_image' !== $column_name ) {
			return;
		}
		
		?>
			<fieldset id="qefi_featured_image" class="inline-edit-col-left">
				<div class="inline-edit-col">
					<label>
						<span class="title"><?php echo esc_html( $this->image_strings( 'featured_image' ) ); ?></span>
						<div>
							<a href="#" class="button qefi-upload-img"><?php echo esc_html( $this->image_strings( 'set_featured_image' ) ); ?></a>
							<input type="hidden" name="_thumbnail_id" value="" />
						</div>
						<a href="#" class="submitdelete qefi-remove-img"><?php echo esc_html( $this->image_strings( 'remove_featured_image' ) ); ?></a>
					</label>
				</div>
			</fieldset>
		<?php	
	}
	
	/**
	 * Add additional inline styles and our script on the admin pages.
	 *
	 * @uses $this->get_current_post_type()
	 * @uses $this->post_types_disable()
	 * @uses $this->image_strings()
	 *
	 * @param string $hook  Admin screen hook handle to check for.
	 */
	function admin_inline_styles_scripts( $hook ) {
	
		/** Bail early if not enabled for Post Type */
		if ( in_array( $this->get_current_post_type(), $this->post_types_disable() )
			|| ! is_post_type_viewable( $this->get_current_post_type() )
			|| ! post_type_supports( $this->get_current_post_type(), 'thumbnail' )
		) {
			return;
		}
	
		/**
		 * For WordPress Admin Area – create the styles
		 *   Style handle: 'list-tables' (WordPress Core)
		 */
		$inline_css = sprintf(
			'
			th.manage-column.column-qefi_featured_image {
				text-align: center;
				width: 6rem;
				max-width: %s;
			}
			td.qefi_featured_image.column-qefi_featured_image a {
				cursor: pointer;
			}
			
			#qefi_featured_image .inline-edit-col {
				margin-top: 1rem;
			}
			
			#qefi_featured_image .inline-edit-col .title {
				padding-right: .7rem;
			}
			
			.inline-edit-col .qefi-upload-img :not(img) {
				display: block;
				padding-top: .7rem;
			}
			
			.inline-edit-col a.qefi-upload-img:focus,
			.inline-edit-col a.qefi-upload-img a:focus,
			.inline-edit-col a.qefi-upload-img img:focus,
			a.qefi-upload-img:focus,
			td.qefi_featured_image.column-qefi_featured_image a:focus {
				box-shadow: none;
				outline: none;
				text-decoration: none !important;
			}
			
			td.qefi_featured_image.column-qefi_featured_image img {
				border: 2px solid transparent;
				padding: 2px;
				width: %s;
				max-width: 5.25rem;
				height: auto;
				max-height: 4.85rem;
			}
			
			td.qefi_featured_image.column-qefi_featured_image img:hover {
				border: 2px solid #ddd;
			}
			
			td.qefi_featured_image.column-qefi_featured_image img:active,
			td.qefi_featured_image.column-qefi_featured_image img:active {
				border: 2px solid #2271b1;
			}
			
			#qefi_featured_image .qefi-upload-img img {
				width: %s;
				max-width: 5.25rem;
				height: auto;
				max-height: 4.85rem;
			}
			
			.submitdelete.qefi-remove-img {
				color: #b32d2e;
			}
			',
			'7%',
			'100%',
			'50%'
		);
	
		/** Add inline styles to the WP Admin stylesheet */
		wp_add_inline_style( 'list-tables', $inline_css );
		
		/** Register jQery script */
		wp_register_script(
			'qefi-featured-image',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/js/qefi.js',
			array( 'jquery' ),
			'',
			TRUE
		);
		
		/** Add only in Edit context */
		if ( 'edit.php' === $hook ) {
			
			/** Maybe enable WordPress Media Uploader */
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			
			/** Finally, enqueue the script */
			wp_enqueue_script( 'qefi-featured-image' );
			
			$script_strings = array( esc_html( $this->image_strings( 'set_featured_image' ) ) );
			
			/** Localize strings in the script */
			wp_localize_script(
				'qefi-featured-image',
				'qefi_strings',
				$script_strings
			);
			
		}  // end if
	}
	
}  // end of class

/** Start instance of Class */
new DDW_Quick_Edit_Featured_Image();
	
endif;


if ( ! function_exists( 'ddw_qefi_pluginrow_meta' ) ) :
	
add_filter( 'plugin_row_meta', 'ddw_qefi_pluginrow_meta', 10, 2 );
/**
 * Add plugin related links to plugin page.
 *
 * @param array  $ddwp_meta (Default) Array of plugin meta links.
 * @param string $ddwp_file File location of plugin.
 * @return array $ddwp_meta (Modified) Array of plugin links/ meta.
 */
function ddw_qefi_pluginrow_meta( $ddwp_meta, $ddwp_file ) {
 
	 if ( ! current_user_can( 'install_plugins' ) ) return $ddwp_meta;
 
	 /** Get current user */
	 $user = wp_get_current_user();
	 
	 /** Build Newsletter URL */
	 $url_nl = sprintf(
		 'https://deckerweb.us2.list-manage.com/subscribe?u=e09bef034abf80704e5ff9809&amp;id=380976af88&amp;MERGE0=%1$s&amp;MERGE1=%2$s',
		 esc_attr( $user->user_email ),
		 esc_attr( $user->user_firstname )
	 );
	 
	 /** List additional links only for this plugin */
	 if ( $ddwp_file === trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . basename( __FILE__ ) ) {
		 $ddwp_meta[] = sprintf(
			 '<a class="button button-inline" href="https://ko-fi.com/deckerweb" target="_blank" rel="nofollow noopener noreferrer" title="%1$s">❤ <b>%1$s</b></a>',
			 esc_html_x( 'Donate', 'Plugins page listing', 'quick-edit-featured-image' )
		 );
 
		 $ddwp_meta[] = sprintf(
			 '<a class="button-primary" href="%1$s" target="_blank" rel="nofollow noopener noreferrer" title="%2$s">⚡ <b>%2$s</b></a>',
			 $url_nl,
			 esc_html_x( 'Join our Newsletter', 'Plugins page listing', 'quick-edit-featured-image' )
		 );
	 }  // end if
 
	 return apply_filters( 'ddw/admin_extras/pluginrow_meta', $ddwp_meta );
 
 }  // end function
 
 endif;