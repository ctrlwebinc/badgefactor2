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

namespace BadgeFactor2;

/**
 * Badgr Entity Interface.
 */
interface Badgr_Entity {


	/**
	 * Retrieve all entities from Badgr provider.
	 *
	 * @param int   $elements_per_page Elements per page.
	 * @param int   $paged Page number.
	 * @param array $filter Filter.
	 * @return array|boolean Object instances array or false in case of error.
	 */
	public static function all( $elements_per_page = null, $paged = null, $filter = array() );


	/**
	 * Count entities.
	 *
	 * @return int
	 */
	public static function count();


	/**
	 * Retrieve single entity from Badgr provider.
	 *
	 * @param string $entity_id Slug / Entity ID.
	 * @return WP_Post Virtual WP_Post representation of the entity.
	 */
	public static function get( $entity_id );


	/**
	 * Create entity through Badgr provider.
	 *
	 * @param array $values Associated array of values of entity to create.
	 * @param array $files Files to upload.
	 * @param boolean $create performed action flag.
	 * @return string|boolean Id of created entity, or false on error.
	 */
	public static function create ( $values, $files = null, $create = true  );


	/**
	 * Update single entity through Badgr provider.
	 *
	 * @param string $entity_id Slug / Entity ID.
	 * @param array  $values Associative array of values to change.
	 * @param array  $files Files to upload.
	 * @param boolean $create performed action flag.
	 * @return boolean Whether or not update has succeeded.
	 */
	public static function update( $entity_id, $values, $files = null, $create = false );


	/**
	 * Delete a single entity through Badgr provider.
	 *
	 * @param string $entity_id Slug / Entity ID.
	 * @return boolean Whether or not deletion has succeeded.
	 */
	public static function delete( $entity_id );


	/**
	 * Get columns.
	 *
	 * @return void
	 */
	public static function get_columns();


	/**
	 * Get sortable columns.
	 *
	 * @return void
	 */
	public static function get_sortable_columns();
}
