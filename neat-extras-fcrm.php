<?php
/**
 * Neat Extras for FluentCRM
 *
 * @package           neatextrasfcrm
 * @author            Nazar Hotsa
 * @copyright         2024 Nazar Hotsa
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Neat Extras for FluentCRM
 * Plugin URI:        https://github.com/bugnumber9/neat-extras-fcrm
 * Description:       Extend FluentCRM with smart codes (merge codes) offering spintax support and random number generator.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.1
 * Author:            Nazar Hotsa
 * Author URI:        https://www.linkedin.com/in/nazar-hotsa/
 * Text Domain:       neat-extras-fcrm
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires Plugins:  fluent-crm
 */

/*
Portions copyright 2020 Björn Ebbrecht
Other portions copyright as indicated by authors in the relevant files

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require __DIR__ . '/vendor/autoload.php';

// str_contains was introduced in PHP 8. This is a polyfill for PHP 7.
if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Check if substring is contained in string
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	function str_contains( $haystack, $needle ) {
		return ( strpos( $haystack, $needle ) !== false );
	}
}


use bjoernffm\Spintax\Parser;


// Smart codes
function nef_smart_codes() {
	$key = 'nef';
	$title = 'Neat Smart Codes';
	$shortCodes = [
		'SPIN' => 'Spintax',
		'RAND_NUM' => 'Random Number'
	];

	$callback = function ( $code, $valueKey, $defaultValue, $subscriber ) {

		if ( str_contains( $valueKey, 'SPIN' ) ) {

			// Regular expression to match the content inside SPIN()
			$pattern = '/nef\.SPIN\(\s*(.*?)\s*\)/';

			// Variable to hold the extracted text
			$content = '';

			// Perform the regex match
			if ( preg_match( $pattern, $code, $matches ) ) {
				$content = $matches[1]; // The first captured group, which is the content inside SPIN()
			}

			if ( $content ) {
				// Fire up spintax
				$spintax = Parser::parse( $content );
				return $spintax->generate();
			}
			// Usage: {{nef.SPIN(Schrödinger’s Cat is {dead|alive}.)}}
			// Usage: {{nef.SPIN(I {love {PHP|Java|C|C++|JavaScript|Python}|hate Ruby}.)}}

		} elseif ( str_contains( $valueKey, 'RAND_NUM' ) ) {

			// Extract the numbers from the valueKey
			preg_match_all( '/\d+/', $valueKey, $matches );

			if ( count( $matches[0] ) == 2 ) {
				// Convert matches to integers
				$min = (int) $matches[0][0];
				$max = (int) $matches[0][1];

				// Ensure min is less than or equal to max
				if ( $min <= $max ) {
					// Return a random number within the range
					return rand( $min, $max );
				}
			}
			// Usage: {{nef.RAND(0,100)}}
			// Will return a random number between 0 and 100 (inclusive).
		}

		return $defaultValue; // Default value in case of invalid format
	};

	FluentCrmApi('extender')->addSmartCode( $key, $title, $shortCodes, $callback );
}
add_action( 'fluentcrm_loaded', 'nef_smart_codes' );
