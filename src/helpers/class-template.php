<?php
/**
 * Badge Factor 2
 * Copyright (C) 2019 ctrlweb
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package Badge_Factor_2
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

namespace BadgeFactor2\Helpers;

/**
 * Template helper class.
 */
class Template {

	/**
	 * Locates a template.
	 *
	 * @param string $template_filename Template filename.
	 * @param string $default Default template, or null if none.
	 * @param string $extension Filename extension.
	 * @return string
	 */
	public static function locate( $template_filename, $default = null, $extension = '.tpl.php' ) {

		// Add extension if absent.
		if ( substr( $template_filename, -strlen( $extension ) ) !== $extension ) {
			$template_filename .= $extension;
		}

		$plugins = array(
			'badgefactor2',
			'bf2-certificates',
			'bf2-courses',
			'bf2-gravityforms',
			'bf2-woocommerce',
		);

		$template_fullpath = null;

		foreach ( $plugins as $plugin ) {
			if ( ! $template_fullpath ) {
				// If in theme, use it.
				$template_fullpath = locate_template( "templates/{$plugin}/{$template_filename}" );

				if ( ! $template_fullpath ) {
					$template_fullpath = locate_template( "templates/{$plugin}/content/{$template_filename}" );
				}
				// Else, if in plugin, use it.
				if ( ! $template_fullpath ) {
					$template_fullpath = WP_PLUGIN_DIR . "/{$plugin}/templates/{$template_filename}";
					if ( ! file_exists( $template_fullpath ) ) {
						$template_fullpath = WP_PLUGIN_DIR . "/{$plugin}/templates/content/{$template_filename}";
					}
					if ( ! file_exists( $template_fullpath ) ) {
						$template_fullpath = null;
					}
				}
			}
		}
		if ( ! file_exists( $template_fullpath ) ) {
			$template_fullpath = $default;
		}
		return $template_fullpath;
	}


	/**
	 * Allows you to include a template in a variable instead of outputing it right away.
	 *
	 * @param string $file Full file path to include.
	 * @return string Output returned by the include.
	 */
	public static function include_to_var( $file ) {
		ob_start();
		require $file;
		return ob_get_clean();
	}
}
