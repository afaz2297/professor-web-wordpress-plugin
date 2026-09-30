<?php
/**
 * SVG icon sprite. Icons are referenced with <use href="#i-name"> so each one is sent once per page.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Line icons (48×48, stroke = currentColor) that editors can pick in widget controls.
 */
function pw_icon_library() {
	$s = 'fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'award'     => array( __( 'Award / Qualified', 'professor-web' ), '<circle cx="24" cy="18" r="12" ' . $s . '/><path d="M17 28l-4 16 11-6 11 6-4-16" ' . $s . '/><path d="M19 18l3.5 3.5L29 15" ' . $s . '/>' ),
		'clock'     => array( __( 'Clock / Reliable', 'professor-web' ), '<circle cx="24" cy="24" r="18" ' . $s . '/><path d="M24 13v11l7 5" ' . $s . '/>' ),
		'tools'     => array( __( 'Tools / Multi trade', 'professor-web' ), '<path d="M8 40l16-16" ' . $s . '/><path d="M22 10l6-4 8 8-4 6-3-3-9 9-4-4 9-9z" ' . $s . '/><path d="M30 28l10 10a3 3 0 0 1-4 4L26 32" ' . $s . '/><path d="M10 8a7 7 0 0 0 8 10" ' . $s . '/>' ),
		'shield'    => array( __( 'Shield / Built to last', 'professor-web' ), '<path d="M24 5l15 6v11c0 10-6.5 17-15 21-8.5-4-15-11-15-21V11z" ' . $s . '/><path d="M17 24l5 5 9-10" ' . $s . '/>' ),
		'home'      => array( __( 'House', 'professor-web' ), '<path d="M6 22L24 7l18 15" ' . $s . '/><path d="M11 18v22h26V18" ' . $s . '/><path d="M20 40V28h8v12" ' . $s . '/>' ),
		'door'      => array( __( 'Door', 'professor-web' ), '<path d="M12 42V6h24v36" ' . $s . '/><path d="M6 42h36" ' . $s . '/><path d="M17 11h14v12H17zM17 28h14v9H17z" ' . $s . '/><circle cx="31" cy="26" r="1.2" fill="currentColor"/>' ),
		'floor'     => array( __( 'Flooring', 'professor-web' ), '<path d="M24 8L42 17 24 26 6 17z" ' . $s . '/><path d="M6 24l18 9 18-9M6 31l18 9 18-9" ' . $s . '/>' ),
		'kitchen'   => array( __( 'Kitchen / Cabinets', 'professor-web' ), '<path d="M6 20h36v22H6zM6 20l3-8h30l3 8" ' . $s . '/><path d="M24 20v22M15 29v4M33 29v4" ' . $s . '/><path d="M16 12V6M32 12V6" ' . $s . '/>' ),
		'roof'      => array( __( 'Roof / Structural', 'professor-web' ), '<path d="M4 26L24 8l20 18" ' . $s . '/><path d="M10 21v19h28V21" ' . $s . '/><path d="M10 30l28 0M17 40V30M31 40V30M24 30l-7-10M24 30l7-10" ' . $s . '/>' ),
		'wrench'    => array( __( 'Repairs / Maintenance', 'professor-web' ), '<path d="M29 7a10 10 0 0 0-9 14L7 34a4 4 0 0 0 6 6l13-13a10 10 0 0 0 14-9l-6 6-6-2-2-6z" ' . $s . '/>' ),
		'ruler'     => array( __( 'Ruler / Made to measure', 'professor-web' ), '<path d="M6 32L32 6l10 10-26 26z" ' . $s . '/><path d="M14 24l4 4M20 18l3 3M26 12l4 4M11 29l2 2M17 23l2 2M23 17l2 2" ' . $s . '/>' ),
		'table'     => array( __( 'Table', 'professor-web' ), '<path d="M4 17h40v6H4z" ' . $s . '/><path d="M9 23v19M39 23v19M9 32h30" ' . $s . '/>' ),
		'window'    => array( __( 'Window', 'professor-web' ), '<path d="M9 5h30v33H9z" ' . $s . '/><path d="M24 5v33M9 21h30" ' . $s . '/><path d="M5 43h38M7 38h34" ' . $s . '/>' ),
		'trim'      => array( __( 'Skirting / Mouldings', 'professor-web' ), '<path d="M6 8v34h36" ' . $s . '/><path d="M6 34h30c3 0 3-4 6-4M6 38h36" ' . $s . '/><path d="M14 8v18M22 8v18" ' . $s . '/>' ),
		'wardrobe'  => array( __( 'Wardrobe / Built in', 'professor-web' ), '<path d="M8 4h32v38H8z" ' . $s . '/><path d="M24 4v38M8 42v3M40 42v3" ' . $s . '/><path d="M20 20v6M28 20v6" ' . $s . '/>' ),
		'shop'      => array( __( 'Retail / Shopfitting', 'professor-web' ), '<path d="M6 18l3-11h30l3 11" ' . $s . '/><path d="M6 18c0 3 2.5 5 5.5 5S17 21 17 18c0 3 2.5 5 5.5 5h3c3 0 5.5-2 5.5-5 0 3 2.5 5 5.5 5S42 21 42 18" ' . $s . '/><path d="M9 23v19h30V23M20 42V31h8v11" ' . $s . '/>' ),
		'building'  => array( __( 'Building / Commercial', 'professor-web' ), '<path d="M8 42V8h20v34M28 18h12v24" ' . $s . '/><path d="M4 42h40M14 15h2M20 15h2M14 23h2M20 23h2M14 31h2M20 31h2M33 26h2M33 33h2" ' . $s . '/>' ),
		'warehouse' => array( __( 'Warehouse / Industrial', 'professor-web' ), '<path d="M4 18L24 7l20 11v24H4z" ' . $s . '/><path d="M12 42V26h24v16M12 32h24M12 37h24" ' . $s . '/>' ),
		'handshake' => array( __( 'Handshake / Contractor support', 'professor-web' ), '<path d="M4 20l8-6 8 3 6-3 8 3 10-3" ' . $s . '/><path d="M12 14v14l10 8a3 3 0 0 0 4-4M20 30l6 5a3 3 0 0 0 4-4l-7-6M28 29l4 3a3 3 0 0 0 4-4l-9-8-5 3a3 3 0 0 1-4-4l5-4" ' . $s . '/><path d="M36 17v11" ' . $s . '/>' ),
		'flame'     => array( __( 'Insurance / Damage repair', 'professor-web' ), '<path d="M24 43c-8 0-13-5-13-12 0-8 7-11 8-19 4 3 7 7 7 12 2-1 3-3 3-5 4 3 8 7 8 12 0 7-5 12-13 12z" ' . $s . '/><path d="M24 43c-3 0-5-2-5-5 0-3 3-5 5-8 2 3 5 5 5 8 0 3-2 5-5 5z" ' . $s . '/>' ),
		'people'    => array( __( 'People / Landlords', 'professor-web' ), '<circle cx="17" cy="15" r="6" ' . $s . '/><circle cx="33" cy="17" r="5" ' . $s . '/><path d="M5 40c0-7 5-12 12-12s12 5 12 12M29 29c7 0 14 4 14 11" ' . $s . '/>' ),
		'pin'       => array( __( 'Location', 'professor-web' ), '<path d="M24 44s14-12.4 14-24a14 14 0 0 0-28 0c0 11.6 14 24 14 24z" ' . $s . '/><circle cx="24" cy="20" r="5" fill="currentColor"/>' ),
		'hammer'    => array( __( 'Hammer', 'professor-web' ), '<path d="M26 14L10 38a3 3 0 0 0 5 3l16-24" ' . $s . '/><path d="M18 11l8-5c5-2 11 0 14 4l-7 1-4 6-11-6z" ' . $s . '/>' ),
		'saw'       => array( __( 'Saw blade (logo)', 'professor-web' ), '' ),
	);
}

/**
 * Select-control options for the icon library.
 */
function pw_icon_options() {
	$out = array();
	foreach ( pw_icon_library() as $key => $icon ) {
		$out[ $key ] = $icon[0];
	}
	return $out;
}

/**
 * Circular saw blade (echoes the blade in the logo): 22 teeth, hollow ring and centre hole, drawn with evenodd.
 */
function pw_saw_path() {
	$teeth = 22;
	$big   = 48;
	$small = 39;
	$pt    = function ( $radius, $angle ) {
		return round( $radius * cos( $angle ), 2 ) . ',' . round( $radius * sin( $angle ), 2 );
	};
	$d = '';
	for ( $i = 0; $i < $teeth; $i++ ) {
		$a  = ( $i / $teeth ) * M_PI * 2;
		$b  = ( ( $i + 0.62 ) / $teeth ) * M_PI * 2;
		$c  = ( ( $i + 0.8 ) / $teeth ) * M_PI * 2;
		$d .= ( $i ? 'L' : 'M' ) . $pt( $small, $a ) . 'L' . $pt( $big, $b ) . 'L' . $pt( $big - 4, $c );
	}
	$d     .= 'Z';
	$circle = function ( $r ) {
		return 'M' . ( -$r ) . ',0A' . $r . ',' . $r . ' 0 1,0 ' . $r . ',0A' . $r . ',' . $r . ' 0 1,0 ' . ( -$r ) . ',0Z';
	};
	return $d . $circle( 30 ) . $circle( 27.5 ) . $circle( 9 );
}

/**
 * The full sprite, printed once near the top of <body>.
 */
function pw_sprite() {
	$out  = '<svg class="sprite" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">';
	$out .= '<symbol id="i-saw" viewBox="-50 -50 100 100"><path fill="currentColor" fill-rule="evenodd" d="' . pw_saw_path() . '"/></symbol>';
	$out .= '<symbol id="i-arrow" viewBox="0 0 24 24"><path d="M4 12h15M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>';
	$out .= '<symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.6 10.8a15.2 15.2 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z" fill="currentColor"/></symbol>';
	$out .= '<symbol id="i-mail" viewBox="0 0 24 24"><path d="M3 5h18v14H3z M3 6l9 7 9-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></symbol>';
	$out .= '<symbol id="i-fb" viewBox="0 0 24 24"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.3A22 22 0 0 0 14.6 2C12.2 2 10.6 3.5 10.6 6.1v2.4H8v3.6h2.6V22H14v-9.9h2.7l.4-3.6z" fill="currentColor"/></symbol>';
	$out .= '<symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.2 4.2L19 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>';
	foreach ( pw_icon_library() as $key => $icon ) {
		if ( 'saw' === $key ) {
			continue;
		}
		// "pin" keeps the original 24px glyph used by the header and stats strip.
		if ( 'pin' === $key ) {
			$out .= '<symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2.6" fill="currentColor"/></symbol>';
			continue;
		}
		$out .= '<symbol id="i-' . esc_attr( $key ) . '" viewBox="0 0 48 48">' . $icon[1] . '</symbol>';
	}
	$out .= '</svg>';
	return $out;
}
