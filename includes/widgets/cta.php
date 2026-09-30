<?php
/**
 * Call to Action: dark wood panel with heading, text, buttons and contact list.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_CTA extends PW_Widget_Base {

	public function get_name() {
		return 'pw-cta';
	}

	public function get_title() {
		return __( 'PW Call to Action', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-call-to-action pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'professor-web' ) ) );
		$this->add_eyebrow_heading( array(
			'eyebrow' => 'Get in Touch',
			'heading' => 'Ready to Start {Your Project?}',
			'text'    => '<p>Whether you need a one off repair, ongoing property maintenance or a bespoke piece made for your home, contact us today to find out more.</p>',
		), true );
		$this->add_button( 'btn1', __( 'Main button', 'professor-web' ), array( 'text' => 'Contact Us', 'url' => '/contact/', 'style' => 'gold' ) );
		$this->add_control( 'call_label', array(
			'label'       => __( 'Click to call button text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Call Now',
			'description' => __( 'Shows on phones and tablets only. Leave empty to hide.', 'professor-web' ),
			'separator'   => 'before',
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'list', array( 'label' => __( 'Contact List', 'professor-web' ) ) );
		$this->add_control( 'show_list', array(
			'label'       => __( 'Show phone, email & address', 'professor-web' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => __( 'Details come from Professor Web → Settings.', 'professor-web' ),
		) );
		$this->add_control( 'phone_label', array( 'label' => __( 'Phone label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Call us', 'condition' => array( 'show_list' => 'yes' ) ) );
		$this->add_control( 'email_label', array( 'label' => __( 'Email label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Email us', 'condition' => array( 'show_list' => 'yes' ) ) );
		$this->add_control( 'address_label', array( 'label' => __( 'Address label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Find us', 'condition' => array( 'show_list' => 'yes' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'professor-web' ) ) );
		$this->add_control( 'space_above', array(
			'label'   => __( 'Space above', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'none',
			'options' => array(
				'none'   => __( 'None (follows a light section)', 'professor-web' ),
				'normal' => __( 'Normal', 'professor-web' ),
			),
		) );
		$this->add_control( 'anchor', array( 'label' => __( 'Section ID', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'contact' ) );
		$this->end_controls_section();
	}

	/** The phone, email and address list (shared with the Contact Details widget). */
	public static function contact_list( $labels ) {
		$phone = pw_setting( 'phone_display' );
		$email = pw_setting( 'email' );
		$addr  = pw_address_inline();
		$out   = '<ul class="cta__list">';
		if ( $phone ) {
			$out .= '<li><a href="' . esc_attr( pw_phone_href() ) . '"><span class="cta__ic">' . pw_icon( 'phone' ) . '</span><span><small>' . esc_html( $labels['phone'] ) . '</small>' . esc_html( $phone ) . '</span></a></li>';
		}
		if ( $email ) {
			$out .= '<li><a href="mailto:' . esc_attr( antispambot( $email ) ) . '"><span class="cta__ic">' . pw_icon( 'mail' ) . '</span><span><small>' . esc_html( $labels['email'] ) . '</small>' . esc_html( antispambot( $email ) ) . '</span></a></li>';
		}
		if ( $addr ) {
			$out .= '<li><div><span class="cta__ic">' . pw_icon( 'pin' ) . '</span><span><small>' . esc_html( $labels['address'] ) . '</small>' . esc_html( $addr ) . '</span></div></li>';
		}
		if ( ! empty( $labels['facebook'] ) && pw_setting( 'facebook' ) ) {
			$out .= '<li><a href="' . esc_url( pw_setting( 'facebook' ) ) . '" target="_blank" rel="noopener"><span class="cta__ic">' . pw_icon( 'fb' ) . '</span><span><small>' . esc_html( $labels['facebook'] ) . '</small>' . esc_html__( 'Follow us on Facebook', 'professor-web' ) . '</span></a></li>';
		}
		return $out . '</ul>';
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->open();
		echo '<section class="cta section' . ( 'normal' === $s['space_above'] ? ' cta--spaced' : '' ) . '"' . ( $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '' ) . '><div class="container">';
		echo '<div class="cta__card wood-dark' . ( 'yes' === $s['show_list'] ? '' : ' cta__card--solo' ) . '" data-reveal="up">';
		echo pw_icon( 'saw', 'cta__saw', array( 'data-spin' => '0.18' ) ); // phpcs:ignore
		echo '<div class="cta__main">';
		$this->render_eyebrow( $s['eyebrow'], true );
		$this->render_heading( $s, 'h2' );
		if ( trim( wp_strip_all_tags( (string) $s['text'] ) ) ) {
			echo '<div class="cta__text rte">' . $this->rich( $s['text'] ) . '</div>'; // phpcs:ignore
		}
		echo '<div class="cta__actions">';
		$this->render_button( $s, 'btn1', true, 'lg' );
		if ( $s['call_label'] ) {
			$this->render_call_button( $s['call_label'], 'ghost', 'lg' );
		}
		echo '</div></div>';
		if ( 'yes' === $s['show_list'] ) {
			echo self::contact_list( array( 'phone' => $s['phone_label'], 'email' => $s['email_label'], 'address' => $s['address_label'] ) ); // phpcs:ignore
		}
		echo '</div></div></section>';
		$this->close();
	}
}
