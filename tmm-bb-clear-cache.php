<?php
/*
Plugin Name: Clear the Beaver Builder Cache
Description: Adds a "Clear Cache" button to the WordPress admin bar.
Version: 1.3
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
        error_log( 'Permission check failed in ccab_clear_cache.' );
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'clear-cache-admin-bar' ) );
    }

    // Clear the Beaver Builder cache
    if ( class_exists( 'FLBuilderModel' ) ) {
        try {
            if ( method_exists( 'FLBuilderModel', 'delete_all_asset_cache' ) ) {
                FLBuilderModel::delete_all_asset_cache();
                error_log( 'FLBuilderModel::delete_all_asset_cache() called successfully.' );
            } else {
                error_log( 'Method delete_all_asset_cache does not exist in FLBuilderModel.' );
            }

            if ( method_exists( 'FLBuilderModel', 'delete_all_transients' ) ) {
                FLBuilderModel::delete_all_transients();
                error_log( 'FLBuilderModel::delete_all_transients() called successfully.' );
            } else {
                error_log( 'Method delete_all_transients does not exist in FLBuilderModel.' );
            }

            if ( method_exists( 'FLBuilderModel', 'delete_all_css' ) ) {
                FLBuilderModel::delete_all_css();
                error_log( 'FLBuilderModel::delete_all_css() called successfully.' );
            } else {
                error_log( 'Method delete_all_css does not exist in FLBuilderModel.' );
            }

            if ( method_exists( 'FLBuilderModel', 'delete_all_js' ) ) {
                FLBuilderModel::delete_all_js();
                error_log( 'FLBuilderModel::delete_all_js() called successfully.' );
            } else {
                error_log( 'Method delete_all_js does not exist in FLBuilderModel.' );
            }

            if ( method_exists( 'FLBuilderModel', 'delete_all_cache' ) ) {
                FLBuilderModel::delete_all_cache();
                error_log( 'FLBuilderModel::delete_all_cache() called successfully.' );
            } else {
                error_log( 'Method delete_all_cache does not exist in FLBuilderModel.' );
            }

            // Delete the cache directory
            $cache_dir = FLBuilderModel::get_cache_dir();
            if ( is_dir( $cache_dir['path'] ) ) {
                $files = glob( $cache_dir['path'] . '/*' );
                foreach ( $files as $file ) {
                    if ( is_file( $file ) ) {
                        unlink( $file );
                    }
                }
                error_log( 'Beaver Builder cache directory cleared successfully.' );
            } else {
                error_log( 'Beaver Builder cache directory does not exist.' );
            }

            add_action( 'admin_notices', 'ccab_cache_cleared_notice' );
            error_log( 'Beaver Builder cache cleared successfully.' );
        } catch ( Exception $e ) {
            error_log( 'Error clearing Beaver Builder cache: ' . $e->getMessage() );
            wp_die( __( 'An error occurred while clearing the cache.', 'clear-cache-admin-bar' ) );
        }
    } else {
        error_log( 'FLBuilderModel class does not exist.' );
        wp_die( __( 'Beaver Builder is not installed or activated.', 'clear-cache-admin-bar' ) );
    }

    // Redirect to the current page
    $referer = wp_get_referer();
    if ( $referer ) {
        wp_redirect( $referer );
    } else {
        wp_redirect( admin_url() );
    }
    exit;
}

function ccab_cache_cleared_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Beaver Builder cache cleared successfully.', 'clear-cache-admin-bar' ); ?></p>
    </div>
    <?php
}