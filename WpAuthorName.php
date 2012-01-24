<?php
/*
Plugin Name: WpAuthorName
Plugin URI: http://divioseo.fr
Description: choose a author name
Author: beunwa
Author URI: http://divioseo.fr
Version: 1
Stable tag: 1
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/



function wpan_create_post_meta_box() {
	add_meta_box( 'wpan-meta-box', 'Change author name', 'wpan_post_meta_box', 'post', 'side', 'high' );
}

function wpan_post_meta_box( $object, $box ) { 	
	$author_name = get_post_meta( $object->ID, '_wpan_author_name', true );
	if($author_name == ''){
		$cu = wp_get_current_user();
		$author_name = $cu->display_name;
	}
	echo '<p>
		<label>Choisissez un nom d\'auteur :</label>
		<input type="text" name="_wpan_author_name" value="'.$author_name.'" />
		</p>
		<input type="hidden" name="_wpan_meta_box_nonce" value="'.wp_create_nonce( plugin_basename( __FILE__ ) ).'" />';
}

function wpan_save_post_meta_box( $post_id, $post ) {

	if ( !wp_verify_nonce( $_POST['_wpan_meta_box_nonce'], plugin_basename( __FILE__ ) ) )
		return $post_id;
	
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	$meta_value = get_post_meta( $post_id, '_wpan_author_name', true );
	$new_meta_value = stripslashes( $_POST['_wpan_author_name'] );
	
	if ( $new_meta_value && $meta_value == '')
		add_post_meta( $post_id, '_wpan_author_name', $new_meta_value, true );

	elseif ( $new_meta_value != $meta_value )
		update_post_meta( $post_id, '_wpan_author_name', $new_meta_value );

	elseif ( $new_meta_value == '' && $meta_value )
		delete_post_meta( $post_id, '_wpan_author_name', $meta_value );		
}

function wpan_the_author($name){
	global $post;
	$author_name = get_post_meta( $post->ID, '_wpan_author_name', true );
	if($author_name == '') $author_name = $name;
	return $author_name;
}

function wpan_the_author_display($name){
	global $post;
	$author_name = get_post_meta( $post->ID, '_wpan_author_name', true );
	if($author_name == '') $author_name = $name;
	echo $author_name;
}

add_action('the_author_display_name', 'wpan_the_author_display' );

add_action('get_the_author_display_name', 'wpan_the_author' );

add_action( 'the_author', 'wpan_the_author' );

add_action( 'admin_menu', 'wpan_create_post_meta_box' );

add_action( 'save_post', 'wpan_save_post_meta_box', 10, 2 );

?>
