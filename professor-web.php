<?php
/**
 * Plugin Name:       Professor Web
 * Plugin URI:        https://github.com/afaz2297/professor-web
 * Update URI:        false
 * Description:       Custom Elementor widgets, site settings and design system for The Jolly Joiner website. Built by Professor Web.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Professor Web
 * Author URI:        https://professorweb.co.uk/
 * License:           GPL-2.0-or-later
 * Text Domain:       professor-web
 */

defined( 'ABSPATH' ) || exit;

define( 'PW_VERSION', '1.0.0' );
define( 'PW_FILE', __FILE__ );
define( 'PW_DIR', plugin_dir_path( __FILE__ ) );
define( 'PW_URL', plugin_dir_url( __FILE__ ) );

require_once PW_DIR . 'includes/helpers.php';
require_once PW_DIR . 'includes/icons.php';
require_once PW_DIR . 'includes/class-pw-settings.php';
require_once PW_DIR . 'includes/class-pw-frontend.php';
require_once PW_DIR . 'includes/class-pw-elementor.php';

PW_Settings::instance();
PW_Frontend::instance();
PW_Elementor::instance();

register_activation_hook( __FILE__, array( 'PW_Settings', 'install_defaults' ) );

add_action( 'after_setup_theme', function () {
	register_nav_menus( array(
		'pw-primary' => __( 'Main menu (Professor Web header)', 'professor-web' ),
	) );
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=professor-web' ) ) . '">' . esc_html__( 'Settings', 'professor-web' ) . '</a>' );
	return $links;
} );
