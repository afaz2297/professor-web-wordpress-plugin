<?php
/**
 * Content Block: image + text in three layouts (framed photo, photo collage, heading/text columns).
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class PW_Widget_Content_Block extends PW_Widget_Base {

	public function get_name() {
		return 'pw-content-block';
	}

	public function get_title() {
		return __( 'PW Content Block', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-image-box pw-eicon';
	}

	protected function register_controls() {
		/* Layout */
		$this->start_controls_section( 'layout_section', array( 'label' => __( 'Layout', 'professor-web' ) ) );
		$this->add_control( 'layout', array(
			'label'   => __( 'Layout', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'framed',
			'options' => array(
				'framed'  => __( 'Framed photo (with gold frame & number)', 'professor-web' ),
				'collage' => __( 'Photo collage (two photos & stamp)', 'professor-web' ),
				'columns' => __( 'Heading & text columns (no photo)', 'professor-web' ),
			),
		) );
		$this->add_control( 'image_side', array(
			'label'     => __( 'Photo side', 'professor-web' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'left',
			'toggle'    => false,
			'options'   => array(
				'left'  => array( 'title' => __( 'Left', 'professor-web' ), 'icon' => 'eicon-h-align-left' ),
				'right' => array( 'title' => __( 'Right', 'professor-web' ), 'icon' => 'eicon-h-align-right' ),
			),
			'condition' => array( 'layout!' => 'columns' ),
		) );
		$this->add_background_control( 'paper' );
		$this->add_control( 'anchor', array(
			'label'       => __( 'Section ID', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'description' => __( 'For jump links, e.g. "residential" makes #residential scroll here.', 'professor-web' ),
		) );
		$this->add_control( 'tuck', array(
			'label'       => __( 'Tuck under the stats strip above', 'professor-web' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => __( 'Only for a block placed straight after an overlapping Stats Strip (home page).', 'professor-web' ),
			'condition'   => array( 'layout' => 'collage' ),
		) );
		$this->end_controls_section();

		/* Photos */
		$this->start_controls_section( 'photos', array(
			'label'     => __( 'Photos', 'professor-web' ),
			'condition' => array( 'layout!' => 'columns' ),
		) );
		$this->add_control( 'image', array(
			'label' => __( 'Main photo', 'professor-web' ),
			'type'  => Controls_Manager::MEDIA,
		) );
		$this->add_control( 'image2', array(
			'label'     => __( 'Second (small) photo', 'professor-web' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => array( 'layout' => 'collage' ),
		) );
		$this->add_control( 'stamp_big', array(
			'label'     => __( 'Stamp – big text', 'professor-web' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '1996',
			'condition' => array( 'layout' => 'collage' ),
		) );
		$this->add_control( 'stamp_small', array(
			'label'       => __( 'Stamp – small text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Established',
			'description' => __( 'Clear both stamp fields to hide the stamp.', 'professor-web' ),
			'condition'   => array( 'layout' => 'collage' ),
		) );
		$this->add_control( 'number', array(
			'label'       => __( 'Big outlined number', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => __( 'E.g. 01. Leave empty for none.', 'professor-web' ),
			'condition'   => array( 'layout' => 'framed' ),
		) );
		$this->end_controls_section();

		/* Text */
		$this->start_controls_section( 'text_section', array( 'label' => __( 'Text', 'professor-web' ) ) );
		$this->add_eyebrow_heading( array( 'eyebrow' => 'Residential', 'heading' => 'Professional Joinery for Your Home' ), true );
		$this->add_control( 'large_text', array(
			'label'   => __( 'Larger intro text', 'professor-web' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		) );
		$this->end_controls_section();

		/* Lists */
		$this->start_controls_section( 'lists', array( 'label' => __( 'Lists & Tags', 'professor-web' ) ) );
		$rep = new Repeater();
		$rep->add_control( 'title', array( 'label' => __( 'Title', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Dining Tables', 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => __( 'Text', 'professor-web' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ) );
		$this->add_control( 'features', array(
			'label'       => __( 'Numbered list', 'professor-web' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ title }}}',
			'default'     => array(),
		) );
		$this->add_control( 'ticks', array(
			'label'       => __( 'Tick list', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 4,
			'description' => __( 'One item per line.', 'professor-web' ),
			'separator'   => 'before',
		) );
		$this->add_control( 'chips_label', array(
			'label'     => __( 'Tags – label above', 'professor-web' ),
			'type'      => Controls_Manager::TEXT,
			'separator' => 'before',
			'label_block' => true,
		) );
		$this->add_control( 'chips', array(
			'label'       => __( 'Tags', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 4,
			'description' => __( 'One tag per line.', 'professor-web' ),
		) );
		$this->add_control( 'text_after', array(
			'label'     => __( 'Text after the lists', 'professor-web' ),
			'type'      => Controls_Manager::WYSIWYG,
			'separator' => 'before',
		) );
		$this->end_controls_section();

		/* Buttons */
		$this->start_controls_section( 'buttons', array( 'label' => __( 'Buttons', 'professor-web' ) ) );
		$this->add_button( 'btn1', __( 'Main button', 'professor-web' ), array( 'text' => 'Learn More', 'url' => '#' ) );
		$this->add_button( 'btn2', __( 'Second button', 'professor-web' ), array( 'style' => 'auto' ) );
		$this->add_control( 'call', array(
			'label'       => __( 'Add a click to call button', 'professor-web' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => __( 'Only shows on phones and tablets.', 'professor-web' ),
			'separator'   => 'before',
		) );
		$this->end_controls_section();
	}

	/** Everything in the text column, with staggered reveal delays. */
	private function render_body( $s, $dark ) {
		$delay = 0;
		$next  = function () use ( &$delay ) {
			$attr   = ' data-reveal="up"' . ( $delay ? ' data-delay="' . $delay . '"' : '' );
			$delay += 70;
			return $attr;
		};

		$this->render_eyebrow( $s['eyebrow'], $dark, $next() );
		$this->render_heading( $s, 'h2', $next() );

		if ( trim( wp_strip_all_tags( (string) $s['text'] ) ) ) {
			echo '<div class="rte' . ( 'yes' === $s['large_text'] ? ' lead' : '' ) . '"' . $next() . '>' . $this->rich( $s['text'] ) . '</div>'; // phpcs:ignore
		}

		if ( ! empty( $s['features'] ) ) {
			echo '<ol class="features"' . $next() . '>';
			foreach ( $s['features'] as $i => $f ) {
				echo '<li class="feature"><span class="feature__n">' . esc_html( pw_num( $i + 1 ) ) . '</span><span class="feature__t">' . esc_html( $f['title'] ) . '</span>';
				if ( $f['text'] ) {
					echo '<span class="feature__d">' . esc_html( $f['text'] ) . '</span>';
				}
				echo '</li>';
			}
			echo '</ol>';
		}

		$ticks = pw_lines( $s['ticks'] );
		if ( $ticks ) {
			echo '<ul class="ticks"' . $next() . '>'; // phpcs:ignore
			foreach ( $ticks as $t ) {
				echo '<li>' . pw_icon( 'check' ) . esc_html( $t ) . '</li>'; // phpcs:ignore
			}
			echo '</ul>';
		}

		$chips = pw_lines( $s['chips'] );
		if ( $chips ) {
			echo '<div class="chips-wrap"' . $next() . '>'; // phpcs:ignore
			if ( $s['chips_label'] ) {
				echo '<p class="chips-label">' . esc_html( $s['chips_label'] ) . '</p>';
			}
			echo '<ul class="chips' . ( $dark ? ' chips--light' : '' ) . '">';
			foreach ( $chips as $c ) {
				echo '<li>' . esc_html( $c ) . '</li>';
			}
			echo '</ul></div>';
		}

		if ( trim( wp_strip_all_tags( (string) $s['text_after'] ) ) ) {
			echo '<div class="rte rte--after"' . $next() . '>' . $this->rich( $s['text_after'] ) . '</div>'; // phpcs:ignore
		}

		$has_btn = trim( (string) $s['btn1_text'] ) || trim( (string) $s['btn2_text'] ) || 'yes' === $s['call'];
		if ( $has_btn ) {
			echo '<div class="block-actions"' . $next() . '>'; // phpcs:ignore
			$this->render_button( $s, 'btn1', $dark );
			$this->render_button( $s, 'btn2', $dark );
			if ( 'yes' === $s['call'] ) {
				$this->render_call_button( pw_setting( 'action_call' ), $dark ? 'ghost' : 'outline' );
			}
			echo '</div>';
		}
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$dark   = 'dark' === $s['background'];
		$bg     = $this->background_class( $s['background'] );
		$flip   = 'right' === $s['image_side'];
		$anchor = $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '';
		$alt    = pw_plain( $s['heading'] );

		$this->open();

		if ( 'collage' === $s['layout'] ) {
			$class = 'about section ' . $bg . ( $flip ? ' about--flip' : '' ) . ( 'yes' === $s['tuck'] ? '' : ' about--flat' );
			echo '<section class="' . esc_attr( $class ) . '"' . $anchor . '><div class="container about__grid"><div class="about__media">'; // phpcs:ignore
			echo '<figure class="about__main" data-reveal="clip">' . pw_img( $s['image'], 'large', array( 'data-parallax' => '0.08', 'alt' => $alt ) ) . '</figure>'; // phpcs:ignore
			if ( ! empty( $s['image2']['url'] ) || ! empty( $s['image2']['id'] ) ) {
				echo '<figure class="about__small" data-reveal="clip" data-delay="250">' . pw_img( $s['image2'], 'medium_large' ) . '</figure>'; // phpcs:ignore
			}
			if ( $s['stamp_big'] || $s['stamp_small'] ) {
				echo '<div class="about__stamp" data-reveal="zoom" data-delay="450"><span class="about__stamp-year">' . esc_html( $s['stamp_big'] ) . '</span><span class="about__stamp-txt">' . esc_html( $s['stamp_small'] ) . '</span></div>';
			}
			echo pw_icon( 'saw', 'about__saw', array( 'data-spin' => '0.12' ) ) . '</div><div class="about__body">'; // phpcs:ignore
			$this->render_body( $s, $dark );
			echo '</div></div></section>';

		} elseif ( 'columns' === $s['layout'] ) {
			echo '<section class="textcols section ' . esc_attr( $bg ) . '"' . $anchor . '><div class="container textcols__grid"><div class="textcols__head">'; // phpcs:ignore
			$this->render_eyebrow( $s['eyebrow'], $dark, ' data-reveal="up"' );
			$this->render_heading( $s, 'h2', ' data-reveal="up" data-delay="80"' );
			echo pw_icon( 'saw', 'textcols__saw', array( 'data-spin' => '0.15' ) ) . '</div><div class="textcols__body">'; // phpcs:ignore
			$copy            = $s;
			$copy['eyebrow'] = '';
			$copy['heading'] = '';
			$this->render_body( $copy, $dark );
			echo '</div></div></section>';

		} else {
			$class = 'service ' . $bg . ( $flip ? ' service--flip' : '' ) . ( $dark ? ' service--dark' : '' );
			echo '<article class="' . esc_attr( $class ) . '"' . $anchor . '><div class="container service__grid"><div class="service__media">'; // phpcs:ignore
			echo '<span class="service__frame" aria-hidden="true"></span>';
			echo '<figure class="service__img" data-reveal="clip">' . pw_img( $s['image'], 'large', array( 'data-parallax' => '0.1', 'alt' => $alt ) ) . '</figure>'; // phpcs:ignore
			if ( '' !== trim( (string) $s['number'] ) ) {
				echo '<span class="service__num" aria-hidden="true">' . esc_html( $s['number'] ) . '</span>';
			}
			echo '</div><div class="service__body">';
			$this->render_body( $s, $dark );
			echo '</div></div></article>';
		}

		$this->close();
	}
}
