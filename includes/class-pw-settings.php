<?php
/**
 * Site-wide settings (Professor Web → Settings) and the admin screen.
 */

defined( 'ABSPATH' ) || exit;

class PW_Settings {

	const OPTION = 'pw_settings';

	private static $instance;
	private static $cache;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'update_option_' . self::OPTION, array( $this, 'flush_cache' ) );
	}

	/**
	 * Field definitions grouped by tab. type: text|email|url|textarea|toggle|color|range
	 */
	public static function fields() {
		return array(
			'business' => array(
				'title'  => __( 'Business Details', 'professor-web' ),
				'icon'   => 'dashicons-store',
				'intro'  => __( 'Used in the header, footer, contact section, mobile call bar and anywhere the [pw_phone], [pw_email] and [pw_address] shortcodes appear. Change them once here and the whole site updates.', 'professor-web' ),
				'fields' => array(
					'phone_display' => array( 'label' => __( 'Phone number (as shown)', 'professor-web' ), 'type' => 'text', 'default' => '07368 535 794', 'help' => __( 'How the number is written on the site.', 'professor-web' ) ),
					'phone_tel'     => array( 'label' => __( 'Phone number (for click to call)', 'professor-web' ), 'type' => 'text', 'default' => '+447368535794', 'help' => __( 'International format with no spaces, e.g. +447368535794. Leave empty to build it from the number above.', 'professor-web' ) ),
					'email'         => array( 'label' => __( 'Email address', 'professor-web' ), 'type' => 'email', 'default' => 'glynne@thejollyjoiner.co.uk' ),
					'address'       => array( 'label' => __( 'Address', 'professor-web' ), 'type' => 'textarea', 'default' => "111 Chessington Crescent,\nTrentham, Stoke on Trent\nST4 8DP", 'help' => __( 'One line per row. The header bar shows it on a single line.', 'professor-web' ) ),
					'facebook'      => array( 'label' => __( 'Facebook page link', 'professor-web' ), 'type' => 'url', 'default' => 'https://www.facebook.com/profile.php?id=61594537709354' ),
					'service_area'  => array( 'label' => __( 'Service area line', 'professor-web' ), 'type' => 'text', 'default' => 'Stoke on Trent based · Serving the wider Midlands' ),
				),
			),
			'header'   => array(
				'title'  => __( 'Header', 'professor-web' ),
				'icon'   => 'dashicons-align-wide',
				'intro'  => __( 'The header layout itself is edited in Elementor (Templates → Theme Builder → Header). These options control the small details.', 'professor-web' ),
				'fields' => array(
					'topbar'         => array( 'label' => __( 'Show the top info bar (desktop)', 'professor-web' ), 'type' => 'toggle', 'default' => 1 ),
					'phone_label'    => array( 'label' => __( 'Label above the phone number (desktop)', 'professor-web' ), 'type' => 'text', 'default' => 'Call us' ),
					'call_label'     => array( 'label' => __( 'Click to call button text (tablet & mobile menu)', 'professor-web' ), 'type' => 'text', 'default' => 'Click to Call' ),
					'hide_on_scroll' => array( 'label' => __( 'Hide the header while scrolling down', 'professor-web' ), 'type' => 'toggle', 'default' => 1, 'help' => __( 'It slides back in as soon as the visitor scrolls up.', 'professor-web' ) ),
				),
			),
			'footer'   => array(
				'title'  => __( 'Footer & Mobile Bar', 'professor-web' ),
				'icon'   => 'dashicons-editor-insertmore',
				'intro'  => __( 'Text used by the footer widget, plus the floating buttons.', 'professor-web' ),
				'fields' => array(
					'footer_about'  => array( 'label' => __( 'Footer description', 'professor-web' ), 'type' => 'textarea', 'default' => 'Bespoke joinery, fitting and property services in Stoke on Trent, backed by more than 30 years of fully qualified experience.' ),
					'copyright'     => array( 'label' => __( 'Copyright line', 'professor-web' ), 'type' => 'text', 'default' => '© {year} The Jolly Joiner. All rights reserved.', 'help' => __( '{year} is replaced with the current year automatically.', 'professor-web' ) ),
					'action_bar'    => array( 'label' => __( 'Show the Call / Email bar at the bottom on phones', 'professor-web' ), 'type' => 'toggle', 'default' => 1 ),
					'action_call'   => array( 'label' => __( 'Call button text', 'professor-web' ), 'type' => 'text', 'default' => 'Call Now' ),
					'action_email'  => array( 'label' => __( 'Email button text', 'professor-web' ), 'type' => 'text', 'default' => 'Email Us' ),
					'to_top'        => array( 'label' => __( 'Show the back to top button', 'professor-web' ), 'type' => 'toggle', 'default' => 1 ),
				),
			),
			'brand'    => array(
				'title'  => __( 'Brand Colours', 'professor-web' ),
				'icon'   => 'dashicons-art',
				'intro'  => __( 'Colours taken from the logo. Every Professor Web widget uses these, so a change here updates the whole site.', 'professor-web' ),
				'fields' => array(
					'c_gold'       => array( 'label' => __( 'Gold (buttons, highlights)', 'professor-web' ), 'type' => 'color', 'default' => '#b8862f' ),
					'c_gold_light' => array( 'label' => __( 'Light gold (on dark backgrounds)', 'professor-web' ), 'type' => 'color', 'default' => '#d6a650' ),
					'c_gold_dark'  => array( 'label' => __( 'Dark gold (small labels)', 'professor-web' ), 'type' => 'color', 'default' => '#8f6720' ),
					'c_ink'        => array( 'label' => __( 'Near black (dark sections)', 'professor-web' ), 'type' => 'color', 'default' => '#14110e' ),
					'c_cream'      => array( 'label' => __( 'Cream (wood sections)', 'professor-web' ), 'type' => 'color', 'default' => '#f5efe5' ),
					'c_paper'      => array( 'label' => __( 'Page background', 'professor-web' ), 'type' => 'color', 'default' => '#fbf8f3' ),
					'c_text'       => array( 'label' => __( 'Body text', 'professor-web' ), 'type' => 'color', 'default' => '#2a2520' ),
					'c_muted'      => array( 'label' => __( 'Soft text', 'professor-web' ), 'type' => 'color', 'default' => '#6c6258' ),
					'grain_light'  => array( 'label' => __( 'Wood texture strength (light sections)', 'professor-web' ), 'type' => 'range', 'default' => 10, 'min' => 0, 'max' => 30 ),
					'grain_dark'   => array( 'label' => __( 'Wood texture strength (dark sections)', 'professor-web' ), 'type' => 'range', 'default' => 7, 'min' => 0, 'max' => 30 ),
				),
			),
			'advanced' => array(
				'title'  => __( 'Performance', 'professor-web' ),
				'icon'   => 'dashicons-performance',
				'intro'  => __( 'Speed and motion options.', 'professor-web' ),
				'fields' => array(
					'animations'  => array( 'label' => __( 'Scroll animations', 'professor-web' ), 'type' => 'toggle', 'default' => 1, 'help' => __( 'Fades, image reveals, counters and parallax. Visitors who ask their device for reduced motion never see them either way.', 'professor-web' ) ),
					'local_fonts' => array( 'label' => __( 'Serve fonts from this website', 'professor-web' ), 'type' => 'toggle', 'default' => 1, 'help' => __( 'Bebas Neue and Lato are bundled with the plugin, so no visitor data is sent to Google (better for UK GDPR and faster). Also stops Elementor loading Google Fonts.', 'professor-web' ) ),
				),
			),
		);
	}

	public static function defaults() {
		$out = array();
		foreach ( self::fields() as $tab ) {
			foreach ( $tab['fields'] as $key => $field ) {
				$out[ $key ] = $field['default'];
			}
		}
		return $out;
	}

	public static function get() {
		if ( null === self::$cache ) {
			$saved       = get_option( self::OPTION, array() );
			self::$cache = wp_parse_args( is_array( $saved ) ? $saved : array(), self::defaults() );
		}
		return self::$cache;
	}

	public function flush_cache() {
		self::$cache = null;
	}

	public static function install_defaults() {
		if ( false === get_option( self::OPTION ) ) {
			add_option( self::OPTION, self::defaults() );
		}
	}

	public function menu() {
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-50 -50 100 100"><path fill="#a7aaad" fill-rule="evenodd" d="' . pw_saw_path() . '"/></svg>' );
		add_menu_page( __( 'Professor Web', 'professor-web' ), __( 'Professor Web', 'professor-web' ), 'manage_options', 'professor-web', array( $this, 'page' ), $icon, 59 );
	}

	public function register() {
		register_setting( 'pw_settings_group', self::OPTION, array( 'sanitize_callback' => array( $this, 'sanitize' ) ) );
	}

	public function sanitize( $input ) {
		$clean = array();
		foreach ( self::fields() as $tab ) {
			foreach ( $tab['fields'] as $key => $field ) {
				$value = $input[ $key ] ?? '';
				switch ( $field['type'] ) {
					case 'toggle':
						$clean[ $key ] = empty( $value ) ? 0 : 1;
						break;
					case 'email':
						$clean[ $key ] = sanitize_email( $value );
						break;
					case 'url':
						$clean[ $key ] = esc_url_raw( $value );
						break;
					case 'textarea':
						$clean[ $key ] = sanitize_textarea_field( $value );
						break;
					case 'color':
						$clean[ $key ] = sanitize_hex_color( $value ) ?: $field['default'];
						break;
					case 'range':
						$clean[ $key ] = max( $field['min'], min( $field['max'], (int) $value ) );
						break;
					default:
						$clean[ $key ] = sanitize_text_field( $value );
				}
			}
		}
		// Elementor caches its CSS files; make sure colour changes show straight away.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		return $clean;
	}

	public function assets( $hook ) {
		if ( 'toplevel_page_professor-web' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'pw-admin', PW_URL . 'assets/css/admin.css', array(), PW_VERSION );
		wp_enqueue_script( 'pw-admin', PW_URL . 'assets/js/admin.js', array( 'wp-color-picker', 'jquery' ), PW_VERSION, true );
	}

	public function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$values = self::get();
		$tabs   = self::fields();
		?>
		<div class="wrap pw-admin">
			<header class="pw-admin__head">
				<div class="pw-admin__brand">
					<span class="pw-admin__mark"><svg viewBox="-50 -50 100 100" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="<?php echo esc_attr( pw_saw_path() ); ?>"/></svg></span>
					<div>
						<h1><?php esc_html_e( 'Professor Web', 'professor-web' ); ?></h1>
						<p><?php esc_html_e( 'Site settings for The Jolly Joiner', 'professor-web' ); ?> · v<?php echo esc_html( PW_VERSION ); ?></p>
					</div>
				</div>
				<div class="pw-admin__links">
					<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View site', 'professor-web' ); ?></a>
					<?php if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) : ?>
						<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=theme' ) ); ?>"><?php esc_html_e( 'Header & Footer templates', 'professor-web' ); ?></a>
					<?php endif; ?>
				</div>
			</header>
			<?php // WordPress moves admin notices to this marker, so they sit below the banner, not inside it. ?>
			<hr class="wp-header-end">

			<?php settings_errors(); ?>

			<form method="post" action="options.php" class="pw-admin__form">
				<?php settings_fields( 'pw_settings_group' ); ?>

				<nav class="pw-tabs" role="tablist">
					<?php $first = true; foreach ( $tabs as $id => $tab ) : ?>
						<button type="button" class="pw-tab<?php echo $first ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $id ); ?>" role="tab">
							<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span><?php echo esc_html( $tab['title'] ); ?>
						</button>
					<?php $first = false; endforeach; ?>
					<button type="button" class="pw-tab" data-tab="help" role="tab"><span class="dashicons dashicons-editor-help"></span><?php esc_html_e( 'How to edit', 'professor-web' ); ?></button>
				</nav>

				<?php $first = true; foreach ( $tabs as $id => $tab ) : ?>
					<section class="pw-panel<?php echo $first ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr( $id ); ?>">
						<div class="pw-card">
							<h2><?php echo esc_html( $tab['title'] ); ?></h2>
							<p class="pw-card__intro"><?php echo esc_html( $tab['intro'] ); ?></p>
							<div class="pw-fields<?php echo 'brand' === $id ? ' pw-fields--grid' : ''; ?>">
								<?php foreach ( $tab['fields'] as $key => $field ) : $this->field( $key, $field, $values[ $key ] ); endforeach; ?>
							</div>
						</div>
					</section>
				<?php $first = false; endforeach; ?>

				<section class="pw-panel" data-panel="help">
					<?php $this->help(); ?>
				</section>

				<div class="pw-admin__save">
					<?php submit_button( __( 'Save settings', 'professor-web' ), 'primary large', 'submit', false ); ?>
					<span class="pw-admin__hint"><?php esc_html_e( 'Changes apply to every page straight away.', 'professor-web' ); ?></span>
				</div>
			</form>
		</div>
		<?php
	}

	private function field( $key, $field, $value ) {
		$name = self::OPTION . '[' . $key . ']';
		$id   = 'pw-' . $key;
		echo '<div class="pw-field pw-field--' . esc_attr( $field['type'] ) . '">';
		if ( 'toggle' === $field['type'] ) {
			echo '<label class="pw-switch" for="' . esc_attr( $id ) . '">';
			echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0">';
			echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( 1, (int) $value, false ) . '>';
			echo '<span class="pw-switch__track" aria-hidden="true"></span><span class="pw-switch__label">' . esc_html( $field['label'] ) . '</span></label>';
		} else {
			echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
			switch ( $field['type'] ) {
				case 'textarea':
					echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="3">' . esc_textarea( $value ) . '</textarea>';
					break;
				case 'color':
					echo '<input type="text" class="pw-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $field['default'] ) . '">';
					break;
				case 'range':
					echo '<div class="pw-range"><input type="range" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" min="' . (int) $field['min'] . '" max="' . (int) $field['max'] . '" value="' . (int) $value . '"><output>' . (int) $value . '%</output></div>';
					break;
				default:
					echo '<input type="' . esc_attr( $field['type'] ) . '" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
			}
		}
		if ( ! empty( $field['help'] ) ) {
			echo '<p class="pw-help">' . esc_html( $field['help'] ) . '</p>';
		}
		echo '</div>';
	}

	private function help() {
		$widgets = array(
			__( 'Header', 'professor-web' )           => __( 'Logo, menu with the Services dropdown, phone number and the full-screen mobile menu. The links come from Appearance → Menus.', 'professor-web' ),
			__( 'Footer', 'professor-web' )           => __( 'Logo, footer columns (picked from your menus), contact details and the large name at the bottom.', 'professor-web' ),
			__( 'Home Hero', 'professor-web' )        => __( 'Full-screen hero with rotating background videos, heading, buttons and the spinning badge.', 'professor-web' ),
			__( 'Page Hero', 'professor-web' )        => __( 'Top banner for inner pages: background photo or wood texture, breadcrumb, heading and buttons.', 'professor-web' ),
			__( 'Content Block', 'professor-web' )    => __( 'Image and text side by side. Three layouts: framed photo, photo collage, or heading and text columns. Supports lists, tags and buttons.', 'professor-web' ),
			__( 'Section Heading', 'professor-web' )  => __( 'Small label, big heading and intro text, with optional jump links.', 'professor-web' ),
			__( 'Card Grid', 'professor-web' )        => __( 'Service or feature cards with an icon or a photo.', 'professor-web' ),
			__( 'Stats Strip', 'professor-web' )      => __( 'Counters such as "30+ years" and "1996".', 'professor-web' ),
			__( 'Scrolling Ribbon', 'professor-web' ) => __( 'The gold ribbon with scrolling service names.', 'professor-web' ),
			__( 'Call to Action', 'professor-web' )   => __( 'Dark "Get in touch" panel with buttons and contact details.', 'professor-web' ),
			__( 'Contact Details', 'professor-web' )  => __( 'Contact cards for the Contact page. Put it next to an Elementor Form.', 'professor-web' ),
		);
		?>
		<div class="pw-card">
			<h2><?php esc_html_e( 'How to edit the website', 'professor-web' ); ?></h2>
			<ol class="pw-steps">
				<li><?php echo wp_kses_post( __( 'Open any page and click <strong>Edit with Elementor</strong>. Click a section to change its text, photos, links or lists in the left panel.', 'professor-web' ) ); ?></li>
				<li><?php echo wp_kses_post( __( 'In headings, put words inside <code>{curly brackets}</code> to show them in gold, e.g. <code>Built to {Last}</code>.', 'professor-web' ) ); ?></li>
				<li><?php echo wp_kses_post( __( 'The menu is edited in <strong>Appearance → Menus</strong>. For the Services dropdown cards, the photo is the page\'s <strong>Featured image</strong> and the short text is the menu item\'s <strong>Description</strong> (turn on "Description" in Screen Options).', 'professor-web' ) ); ?></li>
				<li><?php echo wp_kses_post( __( 'Phone, email, address and colours live on this screen, so the whole site stays consistent.', 'professor-web' ) ); ?></li>
			</ol>
		</div>
		<div class="pw-card">
			<h2><?php esc_html_e( 'Professor Web widgets in Elementor', 'professor-web' ); ?></h2>
			<p class="pw-card__intro"><?php esc_html_e( 'Find them in the Elementor panel under the "Professor Web" category.', 'professor-web' ); ?></p>
			<dl class="pw-widgets">
				<?php foreach ( $widgets as $name => $desc ) : ?>
					<div><dt><?php echo esc_html( $name ); ?></dt><dd><?php echo esc_html( $desc ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		</div>
		<div class="pw-card">
			<h2><?php esc_html_e( 'Shortcodes', 'professor-web' ); ?></h2>
			<p class="pw-card__intro"><?php esc_html_e( 'Use these in any text box to show the details from this screen.', 'professor-web' ); ?></p>
			<table class="widefat striped pw-codes">
				<tbody>
					<tr><td><code>[pw_phone]</code></td><td><?php esc_html_e( 'Phone number: click to call on phones and tablets, plain text on desktop.', 'professor-web' ); ?></td></tr>
					<tr><td><code>[pw_email]</code></td><td><?php esc_html_e( 'Email address as a link.', 'professor-web' ); ?></td></tr>
					<tr><td><code>[pw_address]</code></td><td><?php esc_html_e( 'Postal address.', 'professor-web' ); ?></td></tr>
					<tr><td><code>[pw_year]</code></td><td><?php esc_html_e( 'The current year.', 'professor-web' ); ?></td></tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}
