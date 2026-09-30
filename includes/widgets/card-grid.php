<?php
/**
 * Card Grid: section heading (normal or quote style) + icon cards or photo cards + optional closing note.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class PW_Widget_Card_Grid extends PW_Widget_Base {

	public function get_name() {
		return 'pw-card-grid';
	}

	public function get_title() {
		return __( 'PW Card Grid', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'head', array( 'label' => __( 'Heading', 'professor-web' ) ) );
		$this->add_control( 'head_style', array(
			'label'   => __( 'Heading style', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'heading',
			'options' => array(
				'heading' => __( 'Normal heading', 'professor-web' ),
				'quote'   => __( 'Large quote', 'professor-web' ),
			),
		) );
		$this->add_eyebrow_heading( array( 'eyebrow' => 'Why Choose Us', 'heading' => 'Our {Services}' ) );
		$this->add_control( 'intro', array(
			'label' => __( 'Intro text', 'professor-web' ),
			'type'  => Controls_Manager::TEXTAREA,
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'cards_section', array( 'label' => __( 'Cards', 'professor-web' ) ) );
		$this->add_control( 'card_style', array(
			'label'   => __( 'Card style', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'icon',
			'options' => array(
				'icon'  => __( 'Icon card', 'professor-web' ),
				'image' => __( 'Photo card', 'professor-web' ),
			),
		) );
		$rep = new Repeater();
		$rep->add_control( 'icon', array( 'label' => __( 'Icon', 'professor-web' ), 'type' => Controls_Manager::SELECT, 'options' => pw_icon_options(), 'default' => 'award', 'description' => __( 'Used by icon cards.', 'professor-web' ) ) );
		$rep->add_control( 'image', array( 'label' => __( 'Photo', 'professor-web' ), 'type' => Controls_Manager::MEDIA, 'description' => __( 'Used by photo cards.', 'professor-web' ) ) );
		$rep->add_control( 'title', array( 'label' => __( 'Title', 'professor-web' ), 'type' => Controls_Manager::TEXT, 'default' => 'Fully Qualified', 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => __( 'Text', 'professor-web' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4 ) );
		$rep->add_control( 'link', array( 'label' => __( 'Link (optional)', 'professor-web' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'cards', array(
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ title }}}',
			'default'     => array(
				array( 'icon' => 'award', 'title' => 'Fully Qualified', 'text' => 'Founded in 1996 by fully qualified carpenters.' ),
			),
		) );
		$this->add_control( 'columns', array(
			'label'   => __( 'Cards per row (desktop)', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '4',
			'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
		) );
		$this->add_control( 'numbers', array(
			'label'   => __( 'Show numbers', 'professor-web' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'link_text', array(
			'label'   => __( 'Card link text', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'Learn more',
		) );
		$this->add_control( 'footnote', array(
			'label'     => __( 'Note under the cards', 'professor-web' ),
			'type'      => Controls_Manager::WYSIWYG,
			'separator' => 'before',
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'professor-web' ) ) );
		$this->add_background_control( 'paper' );
		$this->add_control( 'anchor', array( 'label' => __( 'Section ID', 'professor-web' ), 'type' => Controls_Manager::TEXT ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$dark  = 'dark' === $s['background'];
		$quote = 'quote' === $s['head_style'];
		$class = 'why section ' . $this->background_class( $s['background'] ) . ' why--' . $s['card_style'];

		$this->open();
		echo '<section class="' . esc_attr( $class ) . '"' . ( $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '' ) . '><div class="container">';

		if ( $s['eyebrow'] || $s['heading'] || $s['intro'] ) {
			echo '<div class="why__head">';
			$this->render_eyebrow( $s['eyebrow'], $dark, ' data-reveal="up"' );
			if ( $quote && $s['heading'] ) {
				echo '<blockquote class="why__quote" data-reveal="up" data-delay="80">&ldquo;' . pw_hl( $s['heading'] ) . '&rdquo;</blockquote>'; // phpcs:ignore
			} else {
				$this->render_heading( $s, 'h2 why__title', ' data-reveal="up" data-delay="80"' );
			}
			if ( $s['intro'] ) {
				echo '<p class="why__sub" data-reveal="up" data-delay="160">' . esc_html( $s['intro'] ) . '</p>';
			}
			echo '</div>';
		}

		echo '<div class="why__grid" style="--cols:' . (int) $s['columns'] . '">';
		foreach ( (array) $s['cards'] as $i => $card ) {
			$delay = ( $i % (int) $s['columns'] ) * 100;
			$reveal = ' data-reveal="up"' . ( $delay ? ' data-delay="' . $delay . '"' : '' );
			$num    = 'yes' === $s['numbers'] ? pw_num( $i + 1 ) : '';
			$href   = $card['link']['url'] ?? '';

			if ( 'image' === $s['card_style'] ) {
				echo '<article class="svc-card"' . $reveal . '><div class="svc-card__img">' . pw_img( $card['image'], 'medium_large', array( 'alt' => $card['title'] ) ); // phpcs:ignore
				if ( $num ) {
					echo '<span class="svc-card__n">' . esc_html( $num ) . '</span>';
				}
				echo '</div><div class="svc-card__body"><h3>' . esc_html( $card['title'] ) . '</h3>';
			} else {
				echo '<article class="why-card"' . $reveal . '>'; // phpcs:ignore
				if ( $num ) {
					echo '<span class="why-card__n">' . esc_html( $num ) . '</span>';
				}
				echo pw_icon( $card['icon'], 'why-card__icon' ) . '<h3>' . esc_html( $card['title'] ) . '</h3>'; // phpcs:ignore
			}
			if ( $card['text'] ) {
				echo '<p>' . esc_html( $card['text'] ) . '</p>';
			}
			if ( $href ) {
				echo '<a class="card-link"' . pw_link_attrs( $card['link'] ) . '>' . esc_html( $s['link_text'] ) . ' ' . pw_icon( 'arrow' ) . '</a>'; // phpcs:ignore
			}
			echo 'image' === $s['card_style'] ? '</div></article>' : '</article>';
		}
		echo '</div>';

		if ( trim( wp_strip_all_tags( (string) $s['footnote'] ) ) ) {
			echo '<div class="why__foot rte" data-reveal="up">' . pw_icon( 'saw', 'why__foot-saw' ) . $this->rich( $s['footnote'] ) . '</div>'; // phpcs:ignore
		}

		echo '</div></section>';
		$this->close();
	}
}
