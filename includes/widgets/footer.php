<?php
/**
 * Footer: brand column, two menu columns, contact details, giant name, legal links,
 * plus the phone action bar and back-to-top button.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_Footer extends PW_Widget_Base {

	public function get_name() {
		return 'pw-footer';
	}

	public function get_title() {
		return __( 'PW Footer', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-footer pw-eicon';
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	private function menu_select( $id, $label, $description = '' ) {
		$options = array( '' => __( '— None —', 'professor-web' ) );
		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}
		$this->add_control( $id, array(
			'label'       => $label,
			'type'        => Controls_Manager::SELECT,
			'options'     => $options,
			'default'     => '',
			'description' => $description,
		) );
	}

	protected function register_controls() {
		$this->start_controls_section( 'brand', array( 'label' => __( 'Brand Column', 'professor-web' ) ) );
		$this->add_control( 'logo', array(
			'label'       => __( 'Logo', 'professor-web' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => __( 'Leave empty to use the Site Identity logo.', 'professor-web' ),
		) );
		$this->add_control( 'name', array(
			'label'       => __( 'Name', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => get_bloginfo( 'name' ),
		) );
		$this->add_control( 'about', array(
			'label'       => __( 'Description', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'placeholder' => pw_setting( 'footer_about' ),
			'description' => __( 'Empty = the text from Professor Web → Settings → Footer.', 'professor-web' ),
		) );
		$this->add_control( 'fb_label', array(
			'label'   => __( 'Facebook link text', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Follow us on Facebook', 'professor-web' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'columns', array( 'label' => __( 'Link Columns', 'professor-web' ) ) );
		$this->add_control( 'col1_title', array( 'label' => __( 'Column 1 title', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Explore' ) );
		$this->menu_select( 'col1_menu', __( 'Column 1 menu', 'professor-web' ), __( 'Links are edited in Appearance → Menus.', 'professor-web' ) );
		$this->add_control( 'col2_title', array( 'label' => __( 'Column 2 title', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Services', 'separator' => 'before' ) );
		$this->menu_select( 'col2_menu', __( 'Column 2 menu', 'professor-web' ) );
		$this->add_control( 'col3_title', array( 'label' => __( 'Contact column title', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Get in Touch', 'separator' => 'before' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'bottom', array( 'label' => __( 'Bottom Bar', 'professor-web' ) ) );
		$this->add_control( 'giant', array(
			'label'       => __( 'Large name', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => get_bloginfo( 'name' ),
			'description' => __( 'The outlined name that fills with gold. Type "none" to hide it.', 'professor-web' ),
		) );
		$this->menu_select( 'legal_menu', __( 'Legal links menu', 'professor-web' ), __( 'E.g. Privacy Policy.', 'professor-web' ) );
		$this->add_control( 'top_label', array( 'label' => __( 'Back to top text', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Back to top' ) );
		$this->end_controls_section();
	}

	private function menu_links( $menu_id ) {
		$items = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();
		$out   = '';
		foreach ( (array) $items as $item ) {
			$attrs = array( 'href' => $item->url );
			if ( $item->target ) {
				$attrs['target'] = $item->target;
				$attrs['rel']    = 'noopener';
			}
			$out .= '<li><a' . pw_attrs( $attrs ) . '>' . esc_html( $item->title ) . '</a></li>';
		}
		return $out;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$name  = $s['name'] ?: get_bloginfo( 'name' );
		$about = $s['about'] ?: pw_setting( 'footer_about' );
		$giant = $s['giant'] ?: $name;
		$phone = pw_setting( 'phone_display' );
		$email = pw_setting( 'email' );
		$fb    = pw_setting( 'facebook' );
		$lines = pw_lines( pw_setting( 'address' ) );
		$copy  = str_replace( '{year}', wp_date( 'Y' ), pw_setting( 'copyright' ) );

		$this->open();
		?>
		<div class="footer wood-dark is-dark">
			<div class="container footer__grid">
				<div class="footer__brand">
					<img class="footer__logo" src="<?php echo esc_url( pw_logo_url( $s['logo'] ?? null ) ); ?>" width="160" height="116" alt="<?php echo esc_attr( sprintf( __( '%s logo', 'professor-web' ), $name ) ); ?>" loading="lazy">
					<p class="footer__name"><?php echo esc_html( $name ); ?></p>
					<?php if ( $about ) : ?><p class="footer__txt"><?php echo esc_html( $about ); ?></p><?php endif; ?>
					<?php if ( $fb ) : ?>
						<a class="footer__social" href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener"><?php echo pw_icon( 'fb' ); // phpcs:ignore ?><?php echo esc_html( $s['fb_label'] ); ?></a>
					<?php endif; ?>
				</div>

				<?php foreach ( array( 1, 2 ) as $c ) :
					$links = $this->menu_links( $s[ 'col' . $c . '_menu' ] ?? '' );
					if ( ! $links ) {
						continue;
					}
					?>
					<div class="footer__col">
						<h3 class="footer__h"><?php echo esc_html( $s[ 'col' . $c . '_title' ] ); ?></h3>
						<ul><?php echo $links; // phpcs:ignore ?></ul>
					</div>
				<?php endforeach; ?>

				<div class="footer__col footer__contact">
					<h3 class="footer__h"><?php echo esc_html( $s['col3_title'] ); ?></h3>
					<ul>
						<?php if ( $phone ) : ?><li><a href="<?php echo esc_attr( pw_phone_href() ); ?>"><?php echo pw_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
						<?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo pw_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( antispambot( $email ) ); ?></a></li><?php endif; ?>
						<?php if ( $lines ) : ?><li><span><?php echo pw_icon( 'pin' ); // phpcs:ignore ?><span><?php echo implode( '<br>', array_map( 'esc_html', $lines ) ); // phpcs:ignore ?></span></span></li><?php endif; ?>
					</ul>
				</div>
			</div>

			<?php if ( 'none' !== strtolower( trim( $giant ) ) ) : ?>
				<div class="footer__giant" aria-hidden="true"><span><?php echo esc_html( $giant ); ?></span></div>
			<?php endif; ?>

			<div class="container footer__bottom">
				<p><?php echo esc_html( $copy ); ?></p>
				<?php $legal = $this->menu_links( $s['legal_menu'] ?? '' ); ?>
				<?php if ( $legal ) : ?>
					<nav class="footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'professor-web' ); ?>"><ul><?php echo $legal; // phpcs:ignore ?></ul></nav>
				<?php endif; ?>
				<a class="footer__top" href="#" data-pw-top><?php echo esc_html( $s['top_label'] ); ?> <?php echo pw_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>

		<?php if ( pw_setting( 'action_bar' ) && ( $phone || $email ) ) : ?>
			<div class="actionbar" data-pw-actionbar>
				<?php if ( $phone ) : ?><a href="<?php echo esc_attr( pw_phone_href() ); ?>" class="actionbar__call"><?php echo pw_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( pw_setting( 'action_call' ) ); ?></a><?php endif; ?>
				<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo pw_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( pw_setting( 'action_email' ) ); ?></a><?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( pw_setting( 'to_top' ) ) : ?>
			<a class="totop" href="#" data-pw-top data-pw-totop aria-label="<?php esc_attr_e( 'Back to top', 'professor-web' ); ?>">
				<svg class="totop__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="21"/></svg>
				<?php echo pw_icon( 'arrow', 'totop__arrow' ); // phpcs:ignore ?>
			</a>
		<?php endif; ?>
		<?php
		$this->close();
	}
}
