<?php

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$delete = (bool) get_option( 'wordpify_delete_on_uninstall', false );

if ( ! $delete ) {
	return;
}

// Delete all wordpify_product posts.
$posts = get_posts( [
	'post_type'      => 'wordpify_product',
	'posts_per_page' => -1,
	'post_status'    => 'any',
	'fields'         => 'ids',
] );

foreach ( $posts as $post_id ) {
	wp_delete_post( (int) $post_id, true );
}

// Delete all wordpify_collection terms.
$terms = get_terms( [
	'taxonomy'   => 'wordpify_collection',
	'hide_empty' => false,
	'fields'     => 'ids',
] );

if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term_id ) {
		wp_delete_term( (int) $term_id, 'wordpify_collection' );
	}
}

// Delete options.
delete_option( 'wordpify_connection' );
delete_option( 'wordpify_sync_settings' );
delete_option( 'wordpify_version' );
delete_option( 'wordpify_delete_on_uninstall' );

// Delete user meta.
global $wpdb;
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
		$wpdb->esc_like( 'wordpify_' ) . '%'
	)
);
