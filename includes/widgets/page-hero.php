<?php
/**
 * Page Hero: inner page banner with photo (or wood texture), breadcrumb, heading, intro and buttons.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_Page_Hero extends PW_Widget_Base {

	public function get_name() {
		return 'pw-page-hero';
	}

	public function get_title() {
		return __( 'PW Page Hero', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-banner pw-eicon';
	}

	/** The breadcrumb depends on the current page. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'professor-web' ) ) );
		$this->add_control( 'eyebrow', array(
			'label'       => __( 'Small label', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Residential Joinery',
			'label_block' => true,
		) );
		$this->add_control( 'heading', array(
			'label'       => __( 'Page heading', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'default'     => 'Residential Carpentry {Built on Experience}',
			'description' => $this->heading_help() . ' ' . __( 'Leave empty to use the page title.', 'professor-web' ),
		) );
		$this->add_control( 'lead', array(
			'label' => __( 'Intro text', 'professor-web' ),
			'type'  => Controls_Manager::TEXTAREA,
		) );
		$this->add_control( 'breadcrumb', array(
			'label'   => __( 'Show breadcrumb', 'professor-web' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_button( 'btn1', __( 'Main button', 'professor-web' ), array( 'text' => 'Get a Quote', 'url' => '/contact/', 'style' => 'gold' ) );
		$this->add_button( 'btn2', __( 'Second button', 'professor-web' ), array( 'style' => 'ghost' ) );
		$this->add_control( 'call', array(
			'label'       => __( 'Add a click to call button', 'professor-web' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => __( 'Only shows on phones and tablets.', 'professor-web' ),
			'separator'   => 'before',
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'look', array( 'label' => __( 'Background', 'professor-web' ) ) );
		$this->add_control( 'image', array(
			'label'       => __( 'Background photo', 'professor-web' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => __( 'Leave empty for the dark wood texture.', 'professor-web' ),
		) );
		$this->add_control( 'focus', array(
			'label'   => __( 'Photo focus', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'center center',
			'options' => array(
				'center top'    => __( 'Top', 'professor-web' ),
				'center center' => __( 'Centre', 'professor-web' ),
				'center bottom' => __( 'Bottom', 'professor-web' ),
				'left center'   => __( 'Left', 'professor-web' ),
				'right center'  => __( 'Right', 'professor-web' ),
			),
		) );
		$this->add_control( 'badge', array(
			'label'       => __( 'Spinning badge text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '30 Years of Craftsmanship • Fully Qualified • Est. 1996 •',
			'label_block' => true,
			'description' => __( 'Leave empty to hide.', 'professor-web' ),
		) );
		$this->add_control( 'size', array(
			'label'   => __( 'Height', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'tall',
			'options' => array(
				'tall'    => __( 'Tall', 'professor-web' ),
				'compact' => __( 'Compact', 'professor-web' ),
			),
		) );
		$this->end_controls_section();
	}

	private function crumbs( $title ) {
		$items = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'professor-web' ) . '</a>' );
		$post  = get_queried_object();
		if ( $post instanceof WP_Post ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
				$items[] = '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
			}
			$title = get_the_title( $post );
		}
		$items[] = '<span aria-current="page">' . esc_html( $title ) . '</span>';
		return '<nav class="crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'professor-web' ) . '">' . implode( '<i aria-hidden="true">/</i>', $items ) . '</nav>';
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$heading = trim( (string) $s['heading'] ) ? $s['heading'] : get_the_title();
		$img     = pw_media_url( $s['image'], 'full' );
		$class   = 'page-hero' . ( $img ? '' : ' page-hero--texture' ) . ( 'compact' === $s['size'] ? ' page-hero--compact' : '' );
		$badge   = 'pw-pbadge-' . $this->get_id();

		$this->open();
		echo '<section class="' . esc_attr( $class ) . '">';
		echo '<div class="page-hero__media" aria-hidden="true">';
		if ( $img ) {
			echo pw_img( $s['image'], 'full', array( // phpcs:ignore
				'alt'           => '',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'data-parallax' => '0.12',
				'style'         => 'object-position:' . esc_attr( $s['focus'] ),
			) );
		}
		echo '<div class="page-hero__shade"></div><div class="page-hero__grain"></div></div>';
		echo pw_icon( 'saw', 'page-hero__saw', array( 'data-spin' => '0.1' ) ); // phpcs:ignore

		echo '<div class="container page-hero__inner">';
		if ( 'yes' === $s['breadcrumb'] ) {
			echo '<div class="page-hero__crumbs">' . $this->crumbs( pw_plain( $heading ) ) . '</div>'; // phpcs:ignore
		}
		if ( $s['eyebrow'] ) {
			echo '<p class="eyebrow eyebrow--light page-hero__eyebrow"><span class="eyebrow__rule"></span>' . esc_html( $s['eyebrow'] ) . '</p>';
		}
		echo '<h1 class="page-hero__title"><span class="line"><span>' . pw_hl( $heading ) . '</span></span></h1>'; // phpcs:ignore
		if ( $s['lead'] ) {
			echo '<p class="page-hero__lead">' . esc_html( $s['lead'] ) . '</p>';
		}
		if ( trim( (string) $s['btn1_text'] ) || trim( (string) $s['btn2_text'] ) || 'yes' === $s['call'] ) {
			echo '<div class="page-hero__actions">';
			$this->render_button( $s, 'btn1', true, 'lg' );
			$this->render_button( $s, 'btn2', true, 'lg' );
			if ( 'yes' === $s['call'] ) {
				$this->render_call_button( pw_setting( 'action_call' ), 'ghost', 'lg' );
			}
			echo '</div>';
		}
		echo '</div>';

		if ( trim( (string) $s['badge'] ) ) {
			echo '<div class="hero__badge page-hero__badge" aria-hidden="true"><svg class="hero__badge-text" viewBox="0 0 200 200"><defs><path id="' . esc_attr( $badge ) . '" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs><text><textPath href="#' . esc_attr( $badge ) . '">' . esc_html( $s['badge'] ) . '</textPath></text></svg>' . pw_icon( 'saw', 'hero__badge-saw' ) . '</div>'; // phpcs:ignore
		}
		echo '</section>';
		$this->close();
	}
}
