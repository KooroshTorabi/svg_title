<?php

if (!defined( 'WP_UNINSTALL_PLUGIN')) {
	exit();
}

function svgt_delete_plugin() {
	global $wpdb;

	delete_option('svgt');
	delete_user_meta(get_current_user_id(), 'svgt_hide_welcome_panel_on');

	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'svgt_data',
			'post_status' => 'any',
		)
	);

	foreach ($posts as $post) {
		wp_delete_post($post->ID, true);
	}
}

svgt_delete_plugin();