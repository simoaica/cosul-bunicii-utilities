<?php
/**
 * Cosul Bunicii Utilities
 *
 * @package       CBUTILS
 * @author        Dan Simoaica
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Cosul Bunicii Utilities
 * Plugin URI:    https://simoaica.ro
 * Description:   Utilities stuff for cosulbunicii.ro
 * Version:       1.0.0
 * Author:        Dan Simoaica
 * Author URI:    https://simoaica.ro
 * Text Domain:   cosul-bunicii-utilities
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Find all produse CPT who are visible and available
 * and build Cosul Bunicii Glossary with titles from
 * those CPT, when a post is saved or updated
 */

add_action( 'wp_insert_post', 'cbutils_update_glossary' );

function cbutils_update_glossary() {

    global $wpdb;
    $custom_post_type = 'produse'; // define your custom post type slug here

    // A sql query to return all produse custom post titles
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $custom_post_type ), ARRAY_A );
    $glossarries = $wpdb->get_results( "SELECT * FROM cos_jet_post_types  WHERE status = 'glossary'", ARRAY_A );


	$produse_in_cos =[];

	foreach( $results as $index => $post ) {
        if(get_post_meta( $post['ID'], 'is-active', true ) === 'yes' && get_post_meta( $post['ID'], 'is-visible', true ) === 'yes') {
            $produse_in_cos[] = $post['post_title'];
        }
    }

    $glossary_cos = [];

    foreach( $produse_in_cos as $produs ) {
        $glossary["value"] = $produs;
        $glossary["label"] = $produs;
        $glossary["is_checked"] = false;
        $glossary_cos[] = $glossary;
    }

    $wpdb->update( 'cos_jet_post_types', array( 'meta_fields' => serialize($glossary_cos) ), array( 'status' => 'glossary' ) );
 }