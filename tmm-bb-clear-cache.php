<?php
/*
Plugin Name: Clear the Beaver Builder Cache
Description: Adds a "Clear Cache" button to the WordPress admin bar.
Version: 1.0
Author: The Mighty Mo! Design Co.
Author URL: https://www.themightymo.com
Creation Date: Dec. 1, 2024
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Add the Clear Cache button to the admin bar
add_action( 'admin_bar_menu', 'ccab_add_clear_cache_button', 100 );

function ccab_add_clear_cache_button( $wp_admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $args = array(
        'id'    => 'ccab_clear_cache',
        'title' => __( 'Clear BB Cache', 'clear-cache-admin-bar' ),
        'href'  => wp_nonce_url( admin_url( 'admin-post.php?action=ccab_clear_cache' ), 'ccab_clear_cache' ),
        'meta'  => array(
            'class' => 'ccab-clear-cache',
        ),
    );
    $wp_admin_bar->add_node( $args );
}

// Handle the cache clearing action
add_action( 'admin_post_ccab_clear_cache', 'ccab_clear_cache' );

function ccab_clear_cache() {
    if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'ccab_clear_cache' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'clear-cache-admin-bar' ) );
    }

    // Clear the cache logic here
    // Example: Clear Beaver Builder cache
    if ( class_exists( 'FLBuilderModel' ) ) {
        FLBuilderModel::delete_asset_cache();
    }

    wp_redirect( admin_url() );
    exit;
}