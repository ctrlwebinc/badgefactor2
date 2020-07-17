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
 */

namespace BadgeFactor2\Post_Types;

/**
 * Approver user helper functions.
 */
class Approver {

	/**
	 * Get select-formatted options.
	 *
	 * @return array
	 */
	public static function select_options() {
		$args      = array(
			'role'    => 'approver',
			'orderby' => 'user_nicename',
			'order'   => 'ASC',
		);
		$approvers = get_users( $args );

		$post_options = array();
		if ( $approvers ) {
			foreach ( $approvers as $approver ) {
				$post_options[ $approver->ID ] = $approver->user_nicename;
			}
		}

		return $post_options;
	}
}
