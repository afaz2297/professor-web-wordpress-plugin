<?php
/**
 * Header: top bar, logo, menu with Services mega dropdown, phone, and the full-screen mobile menu.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_Header extends PW_Widget_Base {

	public function get_name() {
		return 'pw-header';
	}

	public function get_title() {
		return __( 'PW Header', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-header pw-eicon';
	}

	/** Highlights the current page, so it must not be cached. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	public static function menu_options() {
		$out = array( '' => __( '— Use the "Main menu" location —', 'professor-web' ) );
		foreach ( wp_get_nav_menus() as $menu ) {
			$out[ $menu->term_id ] = $menu->name;
		}
		return $out;
	}

	protected function register_controls() {
		$this->start_controls_section( 'brand', array( 'label' => __( 'Logo & Name', 'professor-web' ) ) );
		$this->add_control( 'logo', array(
			'label'       => __( 'Logo', 'professor-web' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => __( 'Leave empty to use the logo from Appearance → Customize → Site Identity.', 'professor-web' ),
		) );
		$this->add_control( 'show_name', array(
			'label'   => __( 'Show name & tagline next to the logo', 'professor-web' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'name', array(
			'label'       => __( 'Name', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => get_bloginfo( 'name' ),
			'description' => __( 'Empty = Site Title.', 'professor-web' ),
			'condition'   => array( 'show_name' => 'yes' ),
		) );
		$this->add_control( 'tagline', array(
			'label'       => __( 'Tagline', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => get_bloginfo( 'description' ),
			'description' => __( 'Empty = Site Tagline.', 'professor-web' ),
			'condition'   => array( 'show_name' => 'yes' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'menu_section', array( 'label' => __( 'Menu', 'professor-web' ) ) );
		$this->add_control( 'menu', array(
			'label'       => __( 'Menu', 'professor-web' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => self::menu_options(),
			'default'     => '',
			'description' => __( 'Edit links in Appearance → Menus. A menu item with sub-items becomes the dropdown: each card uses the page\'s featured image and the menu item\'s description.', 'professor-web' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'extras', array( 'label' => __( 'Phone & Top Bar', 'professor-web' ) ) );
		$this->add_control( 'show_phone', array(
			'label'       => __( 'Show phone number (desktop)', 'professor-web' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => __( 'Shown as text on desktop; becomes a click to call button on tablets. Number and labels: Professor Web → Settings.', 'professor-web' ),
		) );
		$this->add_control( 'show_topbar', array(
			'label'   => __( 'Show top bar', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'settings',
			'options' => array(
				'settings' => __( 'As set in Professor Web settings', 'professor-web' ),
				'yes'      => __( 'Show', 'professor-web' ),
				'no'       => __( 'Hide', 'professor-web' ),
			),
		) );
		$this->end_controls_section();
	}

	/** Menu items as a parent → children tree. */
	private function menu_tree( $menu_id ) {
		if ( ! $menu_id ) {
			$locations = get_nav_menu_locations();
			$menu_id   = $locations['pw-primary'] ?? 0;
		}
		$items = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();
		if ( ! $items ) {
			return array();
		}
		_wp_menu_item_classes_by_context( $items );
		$tree = array();
		foreach ( $items as $item ) {
			if ( ! $item->menu_item_parent ) {
				$tree[ $item->ID ] = array( 'item' => $item, 'children' => array() );
			}
		}
		foreach ( $items as $item ) {
			if ( $item->menu_item_parent && isset( $tree[ $item->menu_item_parent ] ) ) {
				$tree[ $item->menu_item_parent ]['children'][] = $item;
			}
		}
		return $tree;
	}

	private function is_current( $item ) {
		$classes = (array) $item->classes;
		return in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true );
	}

	private function item_attrs( $item, $class ) {
		$attrs = array( 'class' => $class, 'href' => $item->url );
		if ( $item->target ) {
			$attrs['target'] = $item->target;
			$attrs['rel']    = 'noopener';
		}
		if ( $this->is_current( $item ) ) {
			$attrs['aria-current'] = 'page';
		}
		return pw_attrs( $attrs );
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$logo     = pw_logo_url( $s['logo'] ?? null );
		$name     = $s['name'] ?: get_bloginfo( 'name' );
		$tagline  = $s['tagline'] ?: get_bloginfo( 'description' );
		$tree     = $this->menu_tree( $s['menu'] ?? '' );
		$phone    = pw_setting( 'phone_display' );
		$email    = pw_setting( 'email' );
		$fb       = pw_setting( 'facebook' );
		$address  = pw_address_inline();
		$topbar   = 'settings' === ( $s['show_topbar'] ?? 'settings' ) ? (bool) pw_setting( 'topbar' ) : 'yes' === $s['show_topbar'];
		$uid      = 'pw-' . $this->get_id();
		$home_url = home_url( '/' );

		$this->open();
		?>
		<header class="site-header<?php echo $topbar ? '' : ' no-topbar'; ?>" data-pw-header>
			<?php if ( $topbar ) : ?>
				<div class="topbar">
					<div class="container topbar__inner">
						<p class="topbar__item"><?php echo pw_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $address ); ?></p>
						<div class="topbar__right">
							<?php if ( $email ) : ?>
								<a class="topbar__item" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo pw_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( antispambot( $email ) ); ?></a>
							<?php endif; ?>
							<?php if ( $fb ) : ?>
								<a class="topbar__social" href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( __( '%s on Facebook', 'professor-web' ), $name ) ); ?>"><?php echo pw_icon( 'fb' ); // phpcs:ignore ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="container nav">
				<a class="brand" href="<?php echo esc_url( $home_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s home', 'professor-web' ), $name ) ); ?>">
					<img class="brand__logo" src="<?php echo esc_url( $logo ); ?>" width="88" height="64" alt="<?php echo esc_attr( sprintf( __( '%s logo', 'professor-web' ), $name ) ); ?>">
					<?php if ( 'yes' === $s['show_name'] ) : ?>
						<span class="brand__text">
							<span class="brand__name"><?php echo esc_html( $name ); ?></span>
							<?php if ( $tagline ) : ?><span class="brand__tag"><?php echo esc_html( $tagline ); ?></span><?php endif; ?>
						</span>
					<?php endif; ?>
				</a>

				<?php if ( $tree ) : ?>
					<nav class="nav__menu" aria-label="<?php esc_attr_e( 'Main', 'professor-web' ); ?>">
						<ul>
							<?php foreach ( $tree as $node ) : $item = $node['item']; ?>
								<?php if ( $node['children'] ) :
									$mega_id = $uid . '-mega-' . $item->ID;
									$active  = false;
									foreach ( $node['children'] as $child ) {
										$active = $active || $this->is_current( $child );
									}
									?>
									<li class="has-mega">
										<button class="nav__link nav__trigger<?php echo $active ? ' is-current' : ''; ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $mega_id ); ?>"><?php echo esc_html( $item->title ); ?> <span class="nav__caret" aria-hidden="true"></span></button>
										<div class="mega" id="<?php echo esc_attr( $mega_id ); ?>">
											<div class="mega__grid" style="--mega-cols:<?php echo (int) min( 4, count( $node['children'] ) ); ?>">
												<?php foreach ( $node['children'] as $i => $child ) : ?>
													<a<?php echo $this->item_attrs( $child, 'mega__card' ); // phpcs:ignore ?>>
														<span class="mega__img"><?php
														if ( in_array( $child->object, array( 'page', 'post' ), true ) && has_post_thumbnail( $child->object_id ) ) {
															echo get_the_post_thumbnail( $child->object_id, 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) );
														}
														?></span>
														<span class="mega__num"><?php echo esc_html( pw_num( $i + 1 ) ); ?></span>
														<span class="mega__title"><?php echo esc_html( $child->title ); ?></span>
														<?php if ( $child->description ) : ?><span class="mega__desc"><?php echo esc_html( $child->description ); ?></span><?php endif; ?>
													</a>
												<?php endforeach; ?>
											</div>
										</div>
									</li>
								<?php else : ?>
									<li><a<?php echo $this->item_attrs( $item, 'nav__link' . ( $this->is_current( $item ) ? ' is-current' : '' ) ); // phpcs:ignore ?>><?php echo esc_html( $item->title ); ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_phone'] && $phone ) : ?>
					<p class="nav__phone"><?php echo pw_icon( 'phone' ); // phpcs:ignore ?><span><small><?php echo esc_html( pw_setting( 'phone_label' ) ); ?></small><?php echo esc_html( $phone ); ?></span></p>
					<a class="btn btn--gold btn--sm nav__call" href="<?php echo esc_attr( pw_phone_href() ); ?>"><?php echo pw_icon( 'phone', 'btn__icon' ); // phpcs:ignore ?><span><?php echo esc_html( $phone ); ?></span></a>
				<?php endif; ?>

				<button class="burger" aria-label="<?php esc_attr_e( 'Open menu', 'professor-web' ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-pop">
					<span></span><span></span><span></span>
				</button>
			</div>
		</header>

		<div class="popmenu" id="<?php echo esc_attr( $uid ); ?>-pop" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'professor-web' ); ?>" hidden>
			<div class="popmenu__grain" aria-hidden="true"></div>
			<?php echo pw_icon( 'saw', 'popmenu__saw' ); // phpcs:ignore ?>
			<div class="popmenu__head">
				<img src="<?php echo esc_url( $logo ); ?>" width="99" height="72" alt="<?php echo esc_attr( $name ); ?>">
				<button class="popmenu__close" aria-label="<?php esc_attr_e( 'Close menu', 'professor-web' ); ?>"><span></span><span></span></button>
			</div>
			<nav class="popmenu__nav" aria-label="<?php esc_attr_e( 'Mobile', 'professor-web' ); ?>">
				<?php
				$n = 0;
				foreach ( $tree as $node ) {
					$links = $node['children'] ? $node['children'] : array( $node['item'] );
					// A dropdown parent that links somewhere real keeps its own entry too.
					if ( $node['children'] && ! in_array( $node['item']->url, array( '', '#' ), true ) ) {
						array_unshift( $links, $node['item'] );
					}
					foreach ( $links as $link ) {
						$n++;
						echo '<a' . $this->item_attrs( $link, $this->is_current( $link ) ? 'is-current' : '' ) . '><small>' . esc_html( pw_num( $n ) ) . '</small>' . esc_html( $link->title ) . '</a>'; // phpcs:ignore
					}
				}
				?>
			</nav>
			<div class="popmenu__foot">
				<?php if ( $phone ) : ?>
					<a class="btn btn--gold" href="<?php echo esc_attr( pw_phone_href() ); ?>"><?php echo pw_icon( 'phone', 'btn__icon' ); // phpcs:ignore ?><?php echo esc_html( pw_setting( 'call_label' ) ); ?></a>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<a class="popmenu__mail" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
				<?php endif; ?>
				<?php if ( $fb ) : ?>
					<a class="popmenu__fb" href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><?php echo pw_icon( 'fb' ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
		$this->close();
	}
}
