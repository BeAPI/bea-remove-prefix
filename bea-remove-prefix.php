<?php
/*
Plugin Name: WPMS - Remove prefix(s)
Version: 1.0
Plugin URI: http://www.beapi.fr
Description: Remove prefix(s) for main website of WordPress Network Installation.
Author: BeAPI
Author URI: http://www.beapi.fr

Copyright 2016 - BeAPI Team (technique@beapi.fr)
*/

/**
 * Define the wanted slugs to work on
 *
 * / ! \ depency to php 5.4 for [] array
 * / ! \ depency to php 7.O for directly defining const with value
 *
 * @author Maxime CULEA
 */
const BEA_CHECK_SLUG = [ 'wp', 'blog' ];

/**
 * Check if the current structure permalink begins with BEA_CHECK_SLUG defined slug(s)
 * - yes : removes that and return the stripped structure
 * - no : returns the default $structure_permalink
 *
 * @param $structure_permalink
 *
 * @return string : $structure_permalink
 *
 * @author Maxime CULEA
 */
function wpms_remove_prefix_wp( $structure_permalink ) {
	foreach ( BEA_CHECK_SLUG as $slug ) {
		$slug = sprintf( '/%s/', $slug );

		// Stricly check if /%s/{something_else} begins with /%s/
		if ( substr( $structure_permalink, 0, strlen( $slug ) ) == $slug ) {
			// As it can match only one slug, directly returns the first guess one
			// Returns the same $structure_permalink without /%s first characters
			return substr( $structure_permalink, strlen( $slug ) - 1, strlen( $structure_permalink ) );
		}
	}

	// return default one
	return $structure_permalink;
}
// Check permalink update,category base update and tag base update
add_filter( 'pre_update_option_' . 'category_base', 'wpms_remove_prefix_wp' );
add_filter( 'pre_update_option_' . 'tag_base', 'wpms_remove_prefix_wp' );
add_filter( 'pre_update_option_' . 'permalink_structure', 'wpms_remove_prefix_wp' );

/**
 * Manage to replace unwanted slug(s) as defined in BEA_CHECK_SLUG with corresponding WP-Config constant
 * For now, check into :
 * - WP_SITEURL
 * - WP_HOME
 * 
 * @author Maxime CULEA
 */
function bea_update_ms_first_site_options() {
	/**
	 * High depency with wp-multi-network plugin as working in a Multi Network
	 *
	 * @see : https://github.com/stuttter/wp-multi-network/blob/master/wp-multi-network/includes/functions.php#L149
	 *
	 * Multiple options depending on cases :
	 * - multi network : working as now
	 * - multi sites : you could just get the `is_main_site_for_network()` & `get_main_network_id()` methods for quicker implementation
	 * - single site : just loop to update options
	 *
	 * @author Maxime CULEA
	 */
	if ( ! function_exists( 'is_main_site_for_network' ) || ! is_main_site_for_network( get_main_network_id() ) ) {
		return;
	}

	// Loop on wanted options to be updated
	foreach ( [ 'siteurl', 'home' ] as $option_name ) {
		// Define the possibly matching constant
		$var = sprintf( 'WP_%s', strtoupper( $option_name ) );

		// Get once the option
		$option = get_option( $option_name );

		// Loop on all wanted slug
		foreach ( BEA_CHECK_SLUG as $slug ) {

			// Check if contains slug
			if ( strpos( $option, sprintf( '/%s', $slug ) ) && defined( $var ) ) {

				// Update it with the matching
				update_option( $option_name, constant( $var ) );

				// Break to the first foreach to directly loop on other options
				break(1);
			}
		}
	}
}
add_action( 'admin_head', 'bea_update_ms_first_site_options' );
