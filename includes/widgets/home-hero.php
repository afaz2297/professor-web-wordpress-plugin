<?php
/**
 * Home hero: rotating background videos, animated heading, buttons, spinning badge.
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class PW_Widget_Home_Hero extends PW_Widget_Base {

	public function get_name() {
		return 'pw-home-hero';
	}

	public function get_title() {
		return __( 'PW Home Hero', 'professor-web' );
	}

	public function get_icon() {
		return 'eicon-slider-video pw-eicon';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Text & Buttons', 'professor-web' ) ) );
		$this->add_control( 'eyebrow', array(
			'label'       => __( 'Small label', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Est. 1996 · Stoke on Trent',
			'label_block' => true,
		) );
		$this->add_control( 'line1', array(
			'label'   => __( 'Heading – first line (white)', 'professor-web' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => '30 Years of Craftsmanship.',
		) );
		$this->add_control( 'line2', array(
			'label'   => __( 'Heading – second line (gold)', 'professor-web' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'One Perfect Finish.',
		) );
		$this->add_control( 'lead', array(
			'label'   => __( 'Intro text', 'professor-web' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => 'Fully qualified joinery, bespoke furniture and professional property services, proudly serving Stoke on Trent and the wider Midlands.',
		) );
		$this->add_button( 'btn1', __( 'First button', 'professor-web' ), array( 'text' => 'Discover More', 'url' => '/about/', 'style' => 'gold' ) );
		$this->add_button( 'btn2', __( 'Second button', 'professor-web' ), array( 'text' => 'Contact Us', 'url' => '/contact/', 'style' => 'ghost' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'media', array( 'label' => __( 'Background Videos', 'professor-web' ) ) );
		$rep = new Repeater();
		$rep->add_control( 'video', array(
			'label'       => __( 'Video (MP4)', 'professor-web' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => array( 'video' ),
			'description' => __( 'Keep it short (10–20 s), silent and compressed (under 5 MB).', 'professor-web' ),
		) );
		$rep->add_control( 'poster', array(
			'label'       => __( 'Still image', 'professor-web' ),
			'type'        => Controls_Manager::MEDIA,
			'description' => __( 'Shown while the video loads, and instead of it for visitors who prefer reduced motion.', 'professor-web' ),
		) );
		$rep->add_control( 'label', array(
			'label'   => __( 'Name (for screen readers)', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Workshop video', 'professor-web' ),
		) );
		$this->add_control( 'videos', array(
			'label'       => __( 'Videos', 'professor-web' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ label }}}',
			'default'     => array(),
		) );
		$this->add_control( 'slide_seconds', array(
			'label'   => __( 'Seconds per video', 'professor-web' ),
			'type'    => Controls_Manager::NUMBER,
			'min'     => 4,
			'max'     => 30,
			'default' => 9,
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'extras', array( 'label' => __( 'Badge & Scroll Cue', 'professor-web' ) ) );
		$this->add_control( 'badge', array(
			'label'   => __( 'Spinning badge text', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '30 Years of Craftsmanship • Fully Qualified • Est. 1996 •',
			'description' => __( 'Leave empty to hide the badge. About 55 characters fits the circle best.', 'professor-web' ),
			'label_block' => true,
		) );
		$this->add_control( 'show_reels', array(
			'label'   => __( 'Show 01 / 02 video indicators', 'professor-web' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );
		$this->add_control( 'scroll_label', array(
			'label'       => __( 'Scroll cue text', 'professor-web' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Scroll',
			'description' => __( 'Leave empty to hide.', 'professor-web' ),
		) );
		$this->add_control( 'scroll_target', array(
			'label'   => __( 'Scroll cue jumps to', 'professor-web' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '#intro',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$videos = array_values( array_filter( (array) $s['videos'], function ( $v ) {
			return pw_media_url( $v['video'] ?? array() ) || pw_media_url( $v['poster'] ?? array() );
		} ) );
		$badge_id = 'pw-badge-' . $this->get_id();

		$this->open();
		?>
		<section class="hero" id="top" data-pw-hero data-slide="<?php echo (int) $s['slide_seconds'] * 1000; ?>">
			<div class="hero__media" aria-hidden="true">
				<?php foreach ( $videos as $i => $v ) : ?>
					<video class="hero__video<?php echo 0 === $i ? ' is-active' : ''; ?>" muted playsinline loop preload="<?php echo 0 === $i ? 'auto' : 'none'; ?>"
						poster="<?php echo esc_url( pw_media_url( $v['poster'], 'full' ) ); ?>"
						data-src="<?php echo esc_url( pw_media_url( $v['video'] ) ); ?>"></video>
				<?php endforeach; ?>
				<div class="hero__shade"></div>
				<div class="hero__grain"></div>
			</div>

			<div class="container hero__inner">
				<?php if ( $s['eyebrow'] ) : ?>
					<p class="eyebrow eyebrow--light hero__eyebrow"><span class="eyebrow__rule"></span><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h1 class="hero__title">
					<?php if ( $s['line1'] ) : ?><span class="line"><span><?php echo esc_html( $s['line1'] ); ?></span></span><?php endif; ?>
					<?php if ( $s['line2'] ) : ?><span class="line line--gold"><span><?php echo esc_html( $s['line2'] ); ?></span></span><?php endif; ?>
				</h1>
				<?php if ( $s['lead'] ) : ?><p class="hero__lead"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<div class="hero__actions">
					<?php $this->render_button( $s, 'btn1', true, 'lg' ); ?>
					<?php $this->render_button( $s, 'btn2', true, 'lg' ); ?>
				</div>
			</div>

			<?php if ( trim( (string) $s['badge'] ) ) : ?>
				<div class="hero__badge" aria-hidden="true">
					<svg class="hero__badge-text" viewBox="0 0 200 200">
						<defs><path id="<?php echo esc_attr( $badge_id ); ?>" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs>
						<text><textPath href="#<?php echo esc_attr( $badge_id ); ?>"><?php echo esc_html( $s['badge'] ); ?></textPath></text>
					</svg>
					<?php echo pw_icon( 'saw', 'hero__badge-saw' ); // phpcs:ignore ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $s['show_reels'] && count( $videos ) > 1 ) : ?>
				<div class="hero__reels" role="group" aria-label="<?php esc_attr_e( 'Background video', 'professor-web' ); ?>">
					<?php foreach ( $videos as $i => $v ) : ?>
						<button class="reel<?php echo 0 === $i ? ' is-active' : ''; ?>" data-reel="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show %s', 'professor-web' ), $v['label'] ) ); ?>"><span class="reel__num"><?php echo esc_html( pw_num( $i + 1 ) ); ?></span><span class="reel__bar"><i></i></span></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $s['scroll_label'] ) : ?>
				<a class="hero__scroll" href="<?php echo esc_attr( $s['scroll_target'] ); ?>" aria-label="<?php esc_attr_e( 'Scroll to content', 'professor-web' ); ?>"><span><?php echo esc_html( $s['scroll_label'] ); ?></span><i></i></a>
			<?php endif; ?>
		</section>
		<?php
		$this->close();
	}
}
