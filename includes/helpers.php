<?php
/**
 * Small template helpers shared by the widgets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read one value from the Professor Web settings (falls back to the built-in default).
 */
function pw_setting( $key ) {
	$settings = PW_Settings::get();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
}

/**
 * Escape a heading and turn {curly braces} into the gold highlight.
 * "Skilled Joinery for {Homes & Businesses}" → Skilled Joinery for <span class="gold">Homes &amp; Businesses</span>
 */
function pw_hl( $text ) {
	$text = esc_html( (string) $text );
	return preg_replace( '/\{([^}]*)\}/', '<span class="gold">$1</span>', $text );
}

/**
 * Plain text version of a highlighted heading (for alt text, aria labels…).
 */
function pw_plain( $text ) {
	return str_replace( array( '{', '}' ), '', (string) $text );
}

/**
 * Split a textarea into trimmed, non-empty lines.
 */
function pw_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/**
 * Build href/target/rel attributes from an Elementor URL control value.
 */
function pw_link_attrs( $link, $extra = array() ) {
	$url   = is_array( $link ) ? ( $link['url'] ?? '' ) : (string) $link;
	$attrs = array( 'href' => $url ? $url : '#' );
	if ( is_array( $link ) ) {
		if ( ! empty( $link['is_external'] ) ) {
			$attrs['target'] = '_blank';
			$attrs['rel']    = 'noopener';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$attrs['rel'] = trim( ( $attrs['rel'] ?? '' ) . ' nofollow' );
		}
	}
	return pw_attrs( array_merge( $attrs, $extra ) );
}

/**
 * Render an attribute array as a string.
 */
function pw_attrs( $attrs ) {
	$out = '';
	foreach ( $attrs as $name => $value ) {
		if ( null === $value || false === $value ) {
			continue;
		}
		$value = ( 'href' === $name || 'src' === $name ) ? esc_url( $value ) : esc_attr( $value );
		$out  .= ' ' . $name . '="' . $value . '"';
	}
	return $out;
}

/**
 * Output an <img> for an Elementor media control value, using WordPress responsive images when possible.
 */
function pw_img( $media, $size = 'large', $attrs = array() ) {
	$id  = is_array( $media ) ? (int) ( $media['id'] ?? 0 ) : (int) $media;
	$url = is_array( $media ) ? ( $media['url'] ?? '' ) : '';
	$attrs = array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attrs );

	if ( $id && wp_attachment_is_image( $id ) ) {
		// The Media Library alt text wins; the passed alt is only a fallback.
		$library_alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		if ( '' !== $library_alt || ! isset( $attrs['alt'] ) ) {
			$attrs['alt'] = $library_alt;
		}
		return wp_get_attachment_image( $id, $size, false, $attrs );
	}
	if ( $url ) {
		$attrs['src'] = $url;
		$attrs['alt'] = $attrs['alt'] ?? '';
		return '<img' . pw_attrs( $attrs ) . '>';
	}
	return '';
}

/**
 * URL for a media control (image or video).
 */
function pw_media_url( $media, $size = 'full' ) {
	if ( ! is_array( $media ) ) {
		return '';
	}
	if ( ! empty( $media['id'] ) ) {
		$src = wp_attachment_is_image( $media['id'] ) ? wp_get_attachment_image_url( $media['id'], $size ) : wp_get_attachment_url( $media['id'] );
		if ( $src ) {
			return $src;
		}
	}
	return $media['url'] ?? '';
}

/**
 * Address on one line (the settings keep their own commas, e.g. "…Stoke on Trent ST4 8DP").
 */
function pw_address_inline() {
	return implode( ' ', pw_lines( pw_setting( 'address' ) ) );
}

/**
 * tel: link built from the phone number in settings.
 */
function pw_phone_href() {
	$tel = pw_setting( 'phone_tel' );
	if ( ! $tel ) {
		$tel = preg_replace( '/[^0-9+]/', '', pw_setting( 'phone_display' ) );
	}
	return 'tel:' . $tel;
}

/**
 * Inline reference to an icon in the sprite.
 */
function pw_icon( $name, $class = '', $attrs = array() ) {
	return '<svg' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . pw_attrs( $attrs ) . ' aria-hidden="true"><use href="#i-' . esc_attr( $name ) . '"/></svg>';
}

/**
 * Two-digit counter (01, 02 …).
 */
function pw_num( $n ) {
	return str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
}

/**
 * Logo URL: widget override → Site Identity logo → bundled fallback.
 */
function pw_logo_url( $media = null ) {
	$url = pw_media_url( $media, 'medium' );
	if ( $url ) {
		return $url;
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( $src ) {
			return $src;
		}
	}
	return PW_URL . 'assets/img/logo.png';
}
