<?php
/**
 * Plugin Name: Theme Toolbox
 * Plugin URI: http://johnregan3.github.io/theme-toolbox
 * Description: A set of tools for developers covering common, but not simple functionality.
 * Author: John Regan
 * Author URI: http://johnregan3.me
 * Version: 0.1.0
 * Text Domain: theme-toolbox
 *
 * Copyright 2018  John Regan  (email: john@johnregan3.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Theme_Toolbox
 * @author  John Regan <john@johnregan3.com>
 * @version 0.1.0
 */

namespace Theme_Toolbox;
// Namespace this plugin so there are no naming conflicts with other classes.

// Load the Singleton so we can kick this off.
require_once dirname( __FILE__ ) . '/php/class-singleton.php';

/**
 * Class Theme_Toolbox
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 */
class Theme_Toolbox extends Singleton {

	/**
	 * Initialize the plugin.
	 *
	 * @since 0.1.0
	 */
	public function _init() {
		// Array of module names in slug/label format.
		$modules = array(
			'video-player' => __( 'Video Player', 'theme-toolbox' ),
		);

		self::load_modules( $modules );

	}

	/**
	 * Load Toolbox modules.
	 *
	 * Modules are located in the module directory,
	 * with the slug of the module as the subdirectory name,
	 * and the primary file as {slug}.php.
	 *
	 * For example, the 'video-player' module is located in
	 * modules/video-player/video-player.php
	 * and the class name in that file is Video_Player.
	 *
	 * @since 0.1.0
	 *
	 * @param array $modules An array of Module slug/label key/value pairs.
	 *
	 * @return void
	 */
	public static function load_modules( $modules ) {
		if ( empty( $modules ) || ! is_array( $modules ) ) {
			return;
		}

		foreach ( $modules as $slug => $label ) {
			$path = dirname( __FILE__ ) . '/modules/' . $slug . '/class-' . $slug . '.php';
			if ( ! file_exists( $path ) ) {
				continue;
			}

			include_once $path;
			$classname = self::convert_slug_to_classname( $slug );
			if ( empty( $classname ) || ! class_exists( $classname ) ) {
				continue;
			}

			$classname::get_instance();
		}
	}

	/**
	 * Convert a module slug into a class name.
	 *
	 * Expects input as a hyphenated string, then converts it to
	 * a proper class name.
	 *
	 * Example: 'video-player' becomes 'Video_Player'
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug A module slug.
	 *
	 * @return string
	 */
	public static function convert_slug_to_classname( $slug ) {
		if ( ! is_string( $slug ) ) {
			return '';
		}

		// Written in an expanded form for clarity.
		$text = str_replace( '-', ' ', $slug );
		$text = ucwords( $text );

		return str_replace( ' ', '_', $text );
	}

}

Theme_Toolbox::get_instance();
