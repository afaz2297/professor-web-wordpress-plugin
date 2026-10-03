<?php
/**
 * Front-end assets, icon sprite, brand colours and shortcodes.
 */

defined( 'ABSPATH' ) || exit;

class PW_Frontend {

	private static $instance;
	private $sprite_printed = false;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
		add_action( 'wp_head', array( $this, 'head' ), 1 );
		add_action( 'wp_body_open', array( $this, 'sprite' ), 1 );
		add_action( 'wp_footer', array( $this, 'sprite' ), 1 );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// Hello's "Skip to content" link points at #content, which Elementor full-width pages don't have.
		add_filter( 'hello_elementor_skip_link_url', function () {
			return '#pw-content';
		} );
		add_action( 'elementor/theme/after_do_header', function () {
			echo '<div id="pw-content" tabindex="-1"></div>';
		} );

		if ( pw_setting( 'local_fonts' ) ) {
			// Fonts are bundled with the plugin, so Elementor does not need to call Google.
			add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
		}

		add_shortcode( 'pw_phone', array( $this, 'sc_phone' ) );
		add_shortcode( 'pw_email', array( $this, 'sc_email' ) );
		add_shortcode( 'pw_address', array( $this, 'sc_address' ) );
		add_shortcode( 'pw_year', function () {
			return esc_html( wp_date( 'Y' ) );
		} );
	}

	public function enqueue() {
		// Hello Elementor's own header/footer styles target .site-header too; our header replaces them.
		wp_dequeue_style( 'hello-elementor-header-footer' );

		$deps = wp_style_is( 'elementor-frontend', 'registered' ) ? array( 'elementor-frontend' ) : array();
		wp_enqueue_style( 'professor-web', PW_URL . 'assets/css/professor-web.css', $deps, $this->ver( 'assets/css/professor-web.css' ) );
		wp_add_inline_style( 'professor-web', $this->brand_css() );

		wp_enqueue_script( 'professor-web', PW_URL . 'assets/js/professor-web.js', array(), $this->ver( 'assets/js/professor-web.js' ), true );
		wp_localize_script( 'professor-web', 'PW_CONFIG', array(
			'animations'   => (bool) pw_setting( 'animations' ),
			'hideOnScroll' => (bool) pw_setting( 'hide_on_scroll' ),
		) );
	}

	/** Cache-busting version that changes whenever the file changes. */
	private function ver( $rel ) {
		$path = PW_DIR . $rel;
		return file_exists( $path ) ? PW_VERSION . '.' . filemtime( $path ) : PW_VERSION;
	}

	/** Runs before any stylesheet so reveal animations never flash. */
	public function head() {
		echo "<script>document.documentElement.classList.add('js');</script>\n";
		if ( pw_setting( 'local_fonts' ) ) {
			echo '<link rel="preload" href="' . esc_url( PW_URL . 'assets/fonts/bebas-neue-400.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
			echo '<link rel="preload" href="' . esc_url( PW_URL . 'assets/fonts/lato-400.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
	}

	public function sprite() {
		if ( $this->sprite_printed ) {
			return;
		}
		$this->sprite_printed = true;
		echo pw_sprite(); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG markup.
	}

	public function body_class( $classes ) {
		if ( ! pw_setting( 'animations' ) ) {
			$classes[] = 'pw-no-anim';
		}
		return $classes;
	}

	/** Brand colours from the settings screen, as CSS variables. */
	private function brand_css() {
		$map = array(
			'--gold'       => 'c_gold',
			'--gold-light' => 'c_gold_light',
			'--gold-dark'  => 'c_gold_dark',
			'--ink'        => 'c_ink',
			'--cream'      => 'c_cream',
			'--paper'      => 'c_paper',
			'--text'       => 'c_text',
			'--muted'      => 'c_muted',
		);
		$css = ':root{';
		foreach ( $map as $var => $key ) {
			$value = sanitize_hex_color( pw_setting( $key ) );
			if ( $value ) {
				$css .= $var . ':' . $value . ';';
			}
		}
		$css .= '--grain-light-o:' . ( (int) pw_setting( 'grain_light' ) / 100 ) . ';';
		$css .= '--grain-dark-o:' . ( (int) pw_setting( 'grain_dark' ) / 100 ) . ';';
		$css .= '}';
		return $css;
	}

	public function sc_phone( $atts ) {
		$atts = shortcode_atts( array( 'icon' => 'no' ), $atts );
		$icon = 'yes' === $atts['icon'] ? pw_icon( 'phone' ) : '';
		return '<a class="pw-tel" href="' . esc_attr( pw_phone_href() ) . '">' . $icon . esc_html( pw_setting( 'phone_display' ) ) . '</a>';
	}

	public function sc_email() {
		$email = pw_setting( 'email' );
		return '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
	}

	public function sc_address( $atts ) {
		$atts  = shortcode_atts( array( 'inline' => 'no' ), $atts );
		$lines = pw_lines( pw_setting( 'address' ) );
		$glue  = 'yes' === $atts['inline'] ? ' ' : '<br>';
		return implode( $glue, array_map( 'esc_html', $lines ) );
	}
}
