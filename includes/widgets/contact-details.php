<?php
/**
 * Contact Details panel for the Contact page (sits beside an Elementor Form).
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class PW_Widget_Contact_Details extends PW_Widget_Base {

	public function get_name() {
		return 'pw-contact-details';
	}

	public function get_title() {
		return __( 'PW Contact Details', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-envelope pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'professor-web' ) ) );
		$this->add_eyebrow_heading( array(
			'eyebrow' => 'Contact Details',
			'heading' => 'Let\'s Talk About {Your Project}',
			'text'    => '<p>Call, email or send a message and we will get back to you as soon as we can.</p>',
		), true );
		$this->add_control( 'phone_label', array( 'label' => __( 'Phone label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Call us', 'separator' => 'before' ) );
		$this->add_control( 'email_label', array( 'label' => __( 'Email label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Email us' ) );
		$this->add_control( 'address_label', array( 'label' => __( 'Address label', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Find us' ) );
		$this->add_control( 'facebook_label', array(
			'label'       => __( 'Facebook label', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Social',
			'description' => __( 'Leave empty to hide the Facebook row.', 'professor-web' ),
		) );
		$this->add_control( 'call_label', array(
			'label'       => __( 'Click to call button text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Call Now',
			'description' => __( 'Phones and tablets only. Leave empty to hide.', 'professor-web' ),
		) );
		$this->add_control( 'area', array(
			'label'       => __( 'Area line', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => pw_setting( 'service_area' ),
			'description' => __( 'Empty = the service area from Professor Web → Settings.', 'professor-web' ),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$area = $s['area'] ?: pw_setting( 'service_area' );

		$this->open();
		echo '<div class="contact-card wood-dark is-dark" data-reveal="up">';
		echo pw_icon( 'saw', 'contact-card__saw', array( 'data-spin' => '0.15' ) ); // phpcs:ignore
		$this->render_eyebrow( $s['eyebrow'], true );
		$this->render_heading( $s, 'h2' );
		if ( trim( wp_strip_all_tags( (string) $s['text'] ) ) ) {
			echo '<div class="cta__text rte">' . $this->rich( $s['text'] ) . '</div>'; // phpcs:ignore
		}
		echo PW_Widget_CTA::contact_list( array( // phpcs:ignore
			'phone'    => $s['phone_label'],
			'email'    => $s['email_label'],
			'address'  => $s['address_label'],
			'facebook' => $s['facebook_label'],
		) );
		if ( $s['call_label'] ) {
			echo '<div class="contact-card__call">';
			$this->render_call_button( $s['call_label'], 'gold', 'lg' );
			echo '</div>';
		}
		if ( $area ) {
			echo '<p class="contact-card__area">' . pw_icon( 'pin' ) . esc_html( $area ) . '</p>'; // phpcs:ignore
		}
		echo '</div>';
		$this->close();
	}
}
