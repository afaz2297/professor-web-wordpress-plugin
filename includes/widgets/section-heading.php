<?php
/**
 * Section Heading: label, heading, intro text and optional jump links.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class PW_Widget_Section_Heading extends PW_Widget_Base {

	public function get_name() {
		return 'pw-section-heading';
	}

	public function get_title() {
		return __( 'PW Section Heading', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-heading pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Heading', 'professor-web' ) ) );
		$this->add_eyebrow_heading( array( 'eyebrow' => 'What We Do', 'heading' => 'Skilled Joinery for {Homes & Businesses}' ) );
		$this->add_control( 'intro', array(
			'label'   => __( 'Intro text', 'professor-web' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => 'Four specialist services, one standard of workmanship. Choose a service below to see how we can help.',
		) );
		$rep = new Repeater();
		$rep->add_control( 'label', array( 'label' => __( 'Label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Residential' ) );
		$rep->add_control( 'link', array( 'label' => __( 'Link', 'professor-web' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#residential' ) ) );
		$this->add_control( 'tabs', array(
			'label'       => __( 'Jump links', 'professor-web' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ label }}}',
			'default'     => array(),
			'separator'   => 'before',
			'description' => __( 'Numbered pills under the heading. Link to a section ID (e.g. #residential) or a page.', 'professor-web' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'professor-web' ) ) );
		$this->add_control( 'align', array(
			'label'   => __( 'Alignment', 'professor-web' ),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'center',
			'toggle'  => false,
			'options' => array(
				'left'   => array( 'title' => __( 'Left', 'professor-web' ), 'icon' => 'eicon-text-align-left' ),
				'center' => array( 'title' => __( 'Centre', 'professor-web' ), 'icon' => 'eicon-text-align-center' ),
			),
		) );
		$this->add_background_control( 'paper' );
		$this->add_control( 'space_below', array(
			'label'       => __( 'Space below', 'professor-web' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'none',
			'options'     => array(
				'none'   => __( 'None – the next block continues this section', 'professor-web' ),
				'normal' => __( 'Normal section spacing', 'professor-web' ),
			),
		) );
		$this->add_control( 'anchor', array(
			'label'   => __( 'Section ID', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$dark  = 'dark' === $s['background'];
		$class = 'services section ' . $this->background_class( $s['background'] ) . ( 'normal' === $s['space_below'] ? ' services--padded' : '' );

		$this->open();
		echo '<section class="' . esc_attr( $class ) . '"' . ( $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '' ) . '><div class="container">';
		echo '<header class="section-head' . ( 'left' === $s['align'] ? ' section-head--left' : '' ) . '">';
		$this->render_eyebrow( $s['eyebrow'], $dark, ' data-reveal="up"' );
		$this->render_heading( $s, 'h2', ' data-reveal="up" data-delay="80"' );
		if ( $s['intro'] ) {
			echo '<p class="section-head__txt" data-reveal="up" data-delay="160">' . esc_html( $s['intro'] ) . '</p>';
		}
		if ( ! empty( $s['tabs'] ) ) {
			echo '<nav class="svc-tabs" aria-label="' . esc_attr( pw_plain( $s['heading'] ) ) . '" data-reveal="up" data-delay="220">';
			foreach ( $s['tabs'] as $i => $tab ) {
				echo '<a' . pw_link_attrs( $tab['link'] ) . '><small>' . esc_html( pw_num( $i + 1 ) ) . '</small>' . esc_html( $tab['label'] ) . '</a>'; // phpcs:ignore
			}
			echo '</nav>';
		}
		echo '</header></div></section>';
		$this->close();
	}
}
