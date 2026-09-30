<?php
/**
 * Scrolling ribbon of service names, separated by small saw blades.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_Marquee extends PW_Widget_Base {

	public function get_name() {
		return 'pw-marquee';
	}

	public function get_title() {
		return __( 'PW Scrolling Ribbon', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-animation-text pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Ribbon', 'professor-web' ) ) );
		$this->add_control( 'items', array(
			'label'       => __( 'Words', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 9,
			'default'     => "Bespoke Joinery\nDoors & Frames\nFlooring\nKitchen Fitting\nHandcrafted Tables\nStructural Joinery\nShopfitting\nProperty Maintenance\nInsurance Repairs",
			'description' => __( 'One per line.', 'professor-web' ),
		) );
		$this->add_control( 'speed', array(
			'label'       => __( 'Seconds for one loop', 'professor-web' ),
			'type'        => Controls_Manager::NUMBER,
			'min'         => 10,
			'max'         => 120,
			'default'     => 48,
			'description' => __( 'Higher = slower.', 'professor-web' ),
		) );
		$this->add_control( 'tone', array(
			'label'   => __( 'Colour', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'gold',
			'options' => array(
				'gold' => __( 'Gold ribbon', 'professor-web' ),
				'dark' => __( 'Dark ribbon', 'professor-web' ),
			),
		) );
		$edges = array(
			'none'  => __( 'Automatic (page background)', 'professor-web' ),
			'paper' => __( 'Light', 'professor-web' ),
			'cream' => __( 'Cream', 'professor-web' ),
			'dark'  => __( 'Dark', 'professor-web' ),
		);
		$this->add_control( 'edge_top', array(
			'label'       => __( 'Section colour above', 'professor-web' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'none',
			'options'     => $edges,
			'separator'   => 'before',
			'description' => __( 'The ribbon is tilted, so match the sections above and below to hide the little gaps at its edges.', 'professor-web' ),
		) );
		$this->add_control( 'edge_bottom', array(
			'label'   => __( 'Section colour below', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'none',
			'options' => $edges,
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$words = pw_lines( $s['items'] );
		if ( ! $words ) {
			return;
		}
		$class  = 'marquee' . ( 'dark' === $s['tone'] ? ' marquee--dark' : '' );
		$colors = array( 'paper' => 'var(--paper)', 'cream' => 'var(--cream)', 'dark' => 'var(--ink)' );
		$style  = '--marquee-dur:' . (int) $s['speed'] . 's;';
		$style .= '--mq-top:' . ( $colors[ $s['edge_top'] ?? 'none' ] ?? 'transparent' ) . ';';
		$style .= '--mq-bottom:' . ( $colors[ $s['edge_bottom'] ?? 'none' ] ?? 'transparent' ) . ';';

		$this->open();
		echo '<div class="' . esc_attr( $class ) . '" aria-hidden="true" style="' . esc_attr( $style ) . '">';
		echo '<div class="marquee__band"><div class="marquee__track">';
		// Rendered twice so the loop is seamless.
		for ( $pass = 0; $pass < 2; $pass++ ) {
			foreach ( $words as $word ) {
				echo '<span>' . esc_html( $word ) . '</span>' . pw_icon( 'saw' ); // phpcs:ignore
			}
		}
		echo '</div></div></div>';
		$this->close();
	}
}
