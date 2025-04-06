/**
 * jQuery script which handles the WP Media Uploader connection
 */
jQuery(function($){

	/** Add Featured Image */
	$('body').on( 'click', '.qefi-upload-img', function( event ) {
		event.preventDefault();

		const button = $(this);
		const customUploader = wp.media({
			title: qefi_strings.set_featured_image,	// 'Set featured image'
			library : { type : 'image' },
			button: { text: qefi_strings.set_featured_image },
		}).on( 'select', () => {
			const attachment = customUploader.state().get('selection').first().toJSON();
			button.removeClass('button').html( '<img src="' + attachment.url + '" />').next().val(attachment.id).parent().next().show();
		}).open();

	});

	/** Remove Featured image */
	$('body').on('click', '.qefi-remove-img', function( event ) {
		event.preventDefault();
		$(this).hide().prev().find( '[name="_thumbnail_id"]').val('-1').prev().html( qefi_strings.set_featured_image ).addClass('button');
	});

	const $wp_inline_edit = inlineEditPost.edit;

	inlineEditPost.edit = function( id ) {
		$wp_inline_edit.apply( this, arguments );
		let postId = 0;
		if( typeof( id ) == 'object' ) {
			postId = parseInt( this.getId( id ) );
		}

		if ( postId > 0 ) {
			const editRow = $( '#edit-' + postId )
			const postRow = $( '#post-' + postId )
			const featuredImage = $( '.column-qefi_featured_image', postRow ).html()
			const featuredImageId = $( '.column-qefi_featured_image', postRow ).find('img').data('id')

			if( featuredImageId != -1 ) {

				$( ':input[name="_thumbnail_id"]', editRow ).val( featuredImageId ); // ID
				$( '.qefi-upload-img', editRow ).html( featuredImage ).removeClass( 'button' ); // image HTML
				$( '.qefi-remove-img', editRow ).show(); // the remove link

			}
		}
	}
});