<?php
/**
 * Shared base for every Professor Web widget.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

abstract class PW_Widget_Base extends Widget_Base {

	public function get_categories() {
		return array( 'professor-web' );
	}

	public function get_keywords() {
		return array( 'professor', 'jolly', 'joiner', 'pw' );
	}

	public function get_custom_help_url() {
		return admin_url( 'admin.php?page=professor-web' );
	}

	/** Our markup brings its own wrappers. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/** Open the scoping wrapper every stylesheet rule hangs off. */
	protected function open( $extra_class = '' ) {
		echo '<div class="pw' . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' ) . '">';
	}

	protected function close() {
		echo '</div>';
	}

	/* ---------- Reusable control groups ---------- */

	protected function heading_help() {
		return __( 'Put words in {curly brackets} to show them in gold.', 'professor-web' );
	}

	protected function add_eyebrow_heading( $defaults, $with_text = false ) {
		$this->add_control( 'eyebrow', array(
			'label'       => __( 'Small label', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => $defaults['eyebrow'] ?? '',
			'label_block' => true,
			'dynamic'     => array( 'active' => true ),
		) );
		$this->add_control( 'heading', array(
			'label'       => __( 'Heading', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'default'     => $defaults['heading'] ?? '',
			'description' => $this->heading_help(),
			'dynamic'     => array( 'active' => true ),
		) );
		$this->add_control( 'heading_tag', array(
			'label'   => __( 'Heading level', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => $defaults['heading_tag'] ?? 'h2',
			'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => __( 'Not a heading', 'professor-web' ) ),
		) );
		if ( $with_text ) {
			$this->add_control( 'text', array(
				'label'   => __( 'Text', 'professor-web' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => $defaults['text'] ?? '',
			) );
		}
	}

	protected function add_button( $prefix, $label, $defaults = array(), $styles = true ) {
		$this->add_control( $prefix . '_heading', array(
			'label'     => $label,
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		) );
		$this->add_control( $prefix . '_text', array(
			'label'       => __( 'Button text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => $defaults['text'] ?? '',
			'description' => __( 'Leave empty to hide the button.', 'professor-web' ),
			'label_block' => true,
		) );
		$this->add_control( $prefix . '_link', array(
			'label'   => __( 'Link', 'professor-web' ),
			'type'    => Controls_Manager::URL,
			'default' => array( 'url' => $defaults['url'] ?? '' ),
			'dynamic' => array( 'active' => true ),
		) );
		if ( $styles ) {
			$this->add_control( $prefix . '_style', array(
				'label'   => __( 'Style', 'professor-web' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['style'] ?? 'auto',
				'options' => array(
					'auto'  => __( 'Automatic (matches background)', 'professor-web' ),
					'gold'  => __( 'Gold', 'professor-web' ),
					'dark'  => __( 'Dark', 'professor-web' ),
					'ghost' => __( 'Outline (on dark backgrounds)', 'professor-web' ),
				),
			) );
		}
	}

	protected function add_background_control( $default = 'paper', $label = null ) {
		$this->add_control( 'background', array(
			'label'   => $label ?: __( 'Background', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => $default,
			'options' => array(
				'paper' => __( 'Light', 'professor-web' ),
				'cream' => __( 'Cream with wood texture', 'professor-web' ),
				'dark'  => __( 'Dark with wood texture', 'professor-web' ),
			),
		) );
	}

	protected function background_class( $bg ) {
		return array(
			'paper' => 'bg-paper',
			'cream' => 'wood-light',
			'dark'  => 'wood-dark is-dark',
		)[ $bg ] ?? 'bg-paper';
	}

	/**
	 * Render a button from a control prefix. $on_dark decides what "auto" means.
	 */
	protected function render_button( $s, $prefix, $on_dark = false, $size = '', $arrow = true ) {
		$text = trim( (string) ( $s[ $prefix . '_text' ] ?? '' ) );
		if ( '' === $text ) {
			return;
		}
		$style = $s[ $prefix . '_style' ] ?? 'auto';
		if ( 'auto' === $style ) {
			$style = $on_dark ? 'gold' : 'dark';
		}
		$class = 'btn btn--' . $style . ( $size ? ' btn--' . $size : '' );
		echo '<a class="' . esc_attr( $class ) . '"' . pw_link_attrs( $s[ $prefix . '_link' ] ?? '' ) . '>' . esc_html( $text );
		if ( $arrow && 'ghost' !== $style ) {
			echo ' ' . pw_icon( 'arrow', 'btn__arrow' ); // phpcs:ignore
		}
		echo '</a>';
	}

	/** Click-to-call button: only shown on touch devices (tablet & phone). */
	protected function render_call_button( $label, $style = 'ghost', $size = '' ) {
		echo '<a class="btn btn--' . esc_attr( $style ) . ( $size ? ' btn--' . esc_attr( $size ) : '' ) . ' touch-only" href="' . esc_attr( pw_phone_href() ) . '">' . pw_icon( 'phone', 'btn__icon' ) . esc_html( $label ) . '</a>'; // phpcs:ignore
	}

	protected function render_heading( $s, $class = 'h2', $attrs = '' ) {
		$tag = in_array( $s['heading_tag'] ?? 'h2', array( 'h1', 'h2', 'h3', 'div' ), true ) ? $s['heading_tag'] : 'h2';
		if ( '' === trim( (string) ( $s['heading'] ?? '' ) ) ) {
			return;
		}
		echo '<' . $tag . ' class="' . esc_attr( $class ) . '"' . $attrs . '>' . pw_hl( $s['heading'] ) . '</' . $tag . '>'; // phpcs:ignore
	}

	protected function render_eyebrow( $text, $light = false, $attrs = '' ) {
		if ( '' === trim( (string) $text ) ) {
			return;
		}
		echo '<p class="eyebrow' . ( $light ? ' eyebrow--light' : '' ) . '"' . $attrs . '><span class="eyebrow__rule"></span>' . esc_html( $text ) . '</p>'; // phpcs:ignore
	}

	protected function rich( $html ) {
		return $this->parse_text_editor( $html );
	}
}
