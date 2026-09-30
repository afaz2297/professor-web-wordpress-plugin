<?php
/**
 * Registers the "Professor Web" Elementor category and widgets.
 */

defined( 'ABSPATH' ) || exit;

class PW_Elementor {

	private static $instance;

	/** file => class */
	const WIDGETS = array(
		'header'          => 'PW_Widget_Header',
		'footer'          => 'PW_Widget_Footer',
		'home-hero'       => 'PW_Widget_Home_Hero',
		'page-hero'       => 'PW_Widget_Page_Hero',
		'content-block'   => 'PW_Widget_Content_Block',
		'section-heading' => 'PW_Widget_Section_Heading',
		'card-grid'       => 'PW_Widget_Card_Grid',
		'stats'           => 'PW_Widget_Stats',
		'marquee'         => 'PW_Widget_Marquee',
		'cta'             => 'PW_Widget_CTA',
		'contact-details' => 'PW_Widget_Contact_Details',
	);

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'widgets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_css' ) );
		add_action( 'admin_notices', array( $this, 'missing_elementor_notice' ) );
	}

	public function category( $manager ) {
		$manager->add_category( 'professor-web', array(
			'title' => __( 'Professor Web', 'professor-web' ),
			'icon'  => 'eicon-apps',
		) );
	}

	public function widgets( $manager ) {
		require_once PW_DIR . 'includes/widgets/class-pw-widget-base.php';
		foreach ( self::WIDGETS as $file => $class ) {
			require_once PW_DIR . 'includes/widgets/' . $file . '.php';
			$manager->register( new $class() );
		}
	}

	/** Gold icons for our widgets in the Elementor panel. */
	public function editor_css() {
		wp_add_inline_style( 'elementor-editor', '.elementor-element .icon .pw-eicon, #elementor-panel-category-professor-web .icon i{color:#b8862f}' );
	}

	public function missing_elementor_notice() {
		if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Professor Web needs Elementor to be installed and active.', 'professor-web' ) . '</p></div>';
	}
}
