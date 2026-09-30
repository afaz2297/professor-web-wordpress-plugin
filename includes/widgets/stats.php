<?php
/**
 * Stats strip: counters (30+, 1996) and icon facts in a white card.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class PW_Widget_Stats extends PW_Widget_Base {

	public function get_name() {
		return 'pw-stats';
	}

	public function get_title() {
		return __( 'PW Stats Strip', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-counter pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Stats', 'professor-web' ) ) );

		$rep = new Repeater();
		$rep->add_control( 'kind', array(
			'label'   => __( 'Type', 'professor-web' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'number',
			'options' => array(
				'number' => __( 'Counting number', 'professor-web' ),
				'icon'   => __( 'Icon with title', 'professor-web' ),
			),
		) );
		$rep->add_control( 'number', array(
			'label'     => __( 'Number', 'professor-web' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 30,
			'condition' => array( 'kind' => 'number' ),
		) );
		$rep->add_control( 'from', array(
			'label'       => __( 'Count up from', 'professor-web' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'description' => __( 'For a year, start close to it (e.g. 1960 → 1996).', 'professor-web' ),
			'condition'   => array( 'kind' => 'number' ),
		) );
		$rep->add_control( 'suffix', array(
			'label'     => __( 'Gold suffix', 'professor-web' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '+',
			'condition' => array( 'kind' => 'number' ),
		) );
		$rep->add_control( 'icon', array(
			'label'     => __( 'Icon', 'professor-web' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => pw_icon_options(),
			'default'   => 'award',
			'condition' => array( 'kind' => 'icon' ),
		) );
		$rep->add_control( 'title', array(
			'label'     => __( 'Title', 'professor-web' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Master Craftsmen',
			'condition' => array( 'kind' => 'icon' ),
		) );
		$rep->add_control( 'label', array(
			'label'       => __( 'Small text', 'professor-web' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'default'     => "Years of hands on\njoinery experience",
			'description' => __( 'A new line here becomes a line break on larger screens.', 'professor-web' ),
		) );
		$this->add_control( 'items', array(
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ kind === "number" ? number + (suffix || "") : title }}}',
			'default'     => array(
				array( 'kind' => 'number', 'number' => 30, 'from' => 0, 'suffix' => '+', 'label' => "Years of hands on\njoinery experience" ),
				array( 'kind' => 'number', 'number' => 1996, 'from' => 1960, 'suffix' => '', 'label' => "Established by fully\nqualified carpenters" ),
				array( 'kind' => 'icon', 'icon' => 'award', 'title' => 'Master Craftsmen', 'label' => "Fully qualified &\nmulti trade" ),
				array( 'kind' => 'icon', 'icon' => 'pin', 'title' => 'Stoke on Trent', 'label' => "Serving the wider\nMidlands" ),
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'layout', array( 'label' => __( 'Layout', 'professor-web' ) ) );
		$this->add_control( 'overlap', array(
			'label'        => __( 'Rise into the section above', 'professor-web' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'description'  => __( 'On the home page the card overlaps the bottom of the hero. Turn off to use it anywhere else.', 'professor-web' ),
		) );
		$this->add_background_control( 'cream', __( 'Background (when not overlapping)', 'professor-web' ) );
		$this->add_control( 'anchor', array(
			'label'   => __( 'Section ID', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'intro',
		) );
		$this->end_controls_section();
	}

	private function label_html( $text ) {
		return implode( ' <br>', array_map( 'esc_html', pw_lines( $text ) ) );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$overlap = 'yes' === $s['overlap'];
		$class   = 'stats' . ( $overlap ? '' : ' stats--flat ' . $this->background_class( $s['background'] ) );

		$this->open();
		echo '<section class="' . esc_attr( $class ) . '"' . ( $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '' ) . ' aria-label="' . esc_attr__( 'At a glance', 'professor-web' ) . '">';
		echo '<div class="container"><div class="stats__card" data-reveal="up">';
		foreach ( (array) $s['items'] as $item ) {
			if ( 'icon' === $item['kind'] ) {
				echo '<div class="stat stat--icon">' . pw_icon( $item['icon'], 'stat__icon' ) . '<p class="stat__label"><strong>' . esc_html( $item['title'] ) . '</strong>' . $this->label_html( $item['label'] ) . '</p></div>'; // phpcs:ignore
			} else {
				$num  = (int) $item['number'];
				$from = (int) $item['from'];
				echo '<div class="stat"><p class="stat__value"><span data-count="' . esc_attr( $num ) . '"' . ( $from ? ' data-from="' . esc_attr( $from ) . '"' : '' ) . '>' . esc_html( $num ) . '</span>';
				if ( '' !== (string) $item['suffix'] ) {
					echo '<em>' . esc_html( $item['suffix'] ) . '</em>';
				}
				echo '</p><p class="stat__label">' . $this->label_html( $item['label'] ) . '</p></div>'; // phpcs:ignore
			}
		}
		echo '</div></div></section>';
		$this->close();
	}
}
