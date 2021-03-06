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

use BadgeFactor2\BadgrClient;

/**
 * Badgr Client Test.
 */
class BadgrClientTest extends WP_UnitTestCase {

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_can_create_client() {

		// Needs userName, isAdmin, Badgr server public url and badgrServerFlavor.
		$basic_parameters = array(
			'username'                => 'dave@example.net',
			'as_admin'                => true,
			'badgr_server_public_url' => 'http://127.0.0.1:8000',
			'badgr_server_flavor'     => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
		);

		$client = null;

		try {
			$client = BadgrClient::make_instance( $basic_parameters );
		} catch ( BadMethodCallException $e ) {
			// Not catched.
		}

		$this->assertNotNull( $client );
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_creation_missing_key_params_generates_unconfigured_client() {

		// Needs userName, isAdmin, Badgr server public url and badgrServerFlavor.
		$basic_parameters = array(
			'username'                => 'dave@example.net',
			'as_admin'                => true,
			'badgr_server_public_url' => 'http://127.0.0.1:8000',
			'badgr_server_flavor'     => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
		);

		foreach ( $basic_parameters as $key => $value ) {
			$client = null;

			$incomplete_parameters = $basic_parameters;
			unset( $incomplete_parameters[ $key ] );

			$client = BadgrClient::make_instance( $incomplete_parameters );
			$this->assertEquals(BadgrClient::STATE_NEW_AND_UNCONFIGURED, $client->get_state());
		}
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_client_creation_accepts_additional_parameters() {

		// Basic parameters userName, isAdmin, Badgr server public url and badgrServerFlavor.
		$parameters = array(
			'username'                => 'dave@example.net',
			'as_admin'                => true,
			'badgr_server_public_url' => 'http://127.0.0.1:8000',
			'badgr_server_flavor'     => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
		);

		// client_id is an additional parameter.
		$parameters['client_id'] = 'AClientId';

		$client = null;

		try {
			$client = BadgrClient::make_instance( $parameters );
		} catch ( BadMethodCallException $e ) {
			// Not catched.
		}

		$this->assertNotNull( $client );
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_badgr_client_auth_code_connectivity() {

		// Setup a completely configured client and check that we can get the profile info.

		$client_parameters = array(
			'username'                  => getenv( 'BADGR_ADMIN_USERNAME' ),
			'as_admin'                  => true,
			'badgr_server_public_url'   => getenv( 'BADGR_SERVER_PUBLIC_URL' ),
			'badgr_server_flavor'       => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
			'badgr_server_internal_url' => getenv( 'BADGR_SERVER_INTERNAL_URL' ),
			'client_id'                 => getenv( 'BADGR_SERVER_CLIENT_ID' ),
			'client_secret'             => getenv( 'BADGR_SERVER_CLIENT_SECRET' ),
			'access_token'              => getenv( 'BADGR_SERVER_ACCESS_TOKEN' ),
			'refresh_token'             => getenv( 'BADGR_SERVER_REFRESH_TOKEN' ),
			'token_expiration'          => getenv( 'BADGR_SERVER_TOKEN_EXPIRATION' ),
		);

		$client = null;

		try {
			$client = BadgrClient::make_instance( $client_parameters );
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}

		$this->assertNotNull( $client );

		// Check that we can retreive information on the authorized user.
		// Make GET request to /v2/users/self.
		$response = $client->get( '/v2/users/self' );

		// Check response isn't null.
		$this->assertNotNull( $response );

		// Check response has status code 200.
		$this->assertEquals( 200, $response->getStatusCode() );

		$response_info = json_decode( $response->getBody() );

		// Check that entity id exists.
		$this->assertTrue( isset( $response_info->result[0]->entityId ) );

		// Check that entityId isn't empty.
		$this->assertNotEmpty( $response_info->result[0]->entityId );

	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_badgr_client_password_grant_connectivity() {

		// Setup a completely configured client and check that we can get the profile info.

		$client_parameters = array(
			'username'                  => getenv( 'BADGR_SERVER_PASSWORD_GRANT_USERNAME' ),
			'as_admin'                  => true,
			'badgr_server_public_url'   => getenv( 'BADGR_SERVER_PUBLIC_URL' ),
			'badgr_server_flavor'       => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
			'badgr_server_internal_url' => getenv( 'BADGR_SERVER_INTERNAL_URL' ),
			'client_id'                 => getenv( 'BADGR_SERVER_PASSWORD_GRANT_CLIENT_ID' ),
			'badgr_password'            => getenv( 'BADGR_SERVER_PASSWORD_GRANT_PASSWORD' ),
		);

		$client = null;

		try {
			$client = BadgrClient::make_instance( $client_parameters );
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}

		$this->assertNotNull( $client );

		// Attempt to get token.
		$client->get_access_token_from_password_grant();

		// Check that we can retreive information on the authorized user.
		// Make GET request to /v2/users/self.
		$response = $client->get( '/v2/users/self' );

		// Check response isn't null.
		$this->assertNotNull( $response );

		// Check response has status code 200.
		$this->assertEquals( 200, $response->getStatusCode() );

		$response_info = json_decode( $response->getBody() );

		// Check that entity id exists.
		$this->assertTrue( isset( $response_info->result[0]->entityId ) );

		// Check that entityId isn't empty.
		$this->assertNotEmpty( $response_info->result[0]->entityId );

	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_badgr_client_password_grant_bad_credentials_raise_exception() {

		// Setup a completely configured client and check that we can get the profile info.

		$client_parameters = array(
			'username'                  => 'dev@ctrlweb.ca',
			'as_admin'                  => false,
			'badgr_server_public_url'   => getenv( 'BADGR_SERVER_PUBLIC_URL' ),
			'badgr_server_flavor'       => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
			'badgr_server_internal_url' => getenv( 'BADGR_SERVER_INTERNAL_URL' ),
			'client_id'                 => getenv( 'BADGR_SERVER_PASSWORD_GRANT_CLIENT_ID' ),
			'badgr_password'            => 'WRONG_PASSWORD',
		);

		$client = null;

		try {
			$client = BadgrClient::make_instance( $client_parameters );
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}

		$this->assertNotNull( $client );

		try {
			// Attempt to get token.
			$client->get_access_token_from_password_grant();

			// If exception is thrown, we shouldn't get this far.
			$this->fail( 'Bad credentials didn\'t raise exception' );
		} catch ( Exception $e ) {
			$this->assertTrue( true );
		}
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_badgr_client_password_grant_connectivity_badgrio() {

		// Setup a completely configured client and check that we can get the profile info.

		$client_parameters = array(
			'username'                => getenv( 'BADGRIO_USERNAME' ),
			'as_admin'                => false,
			'badgr_server_public_url' => getenv( 'BADGRIO_URL' ),
			'badgr_server_flavor'     => BadgrClient::FLAVOR_BADGRIO_01,
			'badgr_password'          => getenv( 'BADGRIO_PASSWORD' ),
		);

		$client = null;

		try {
			$client = BadgrClient::make_instance( $client_parameters );
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}

		$this->assertNotNull( $client );

		// Attempt to get token.
		$client->get_access_token_from_password_grant();

		// Check that we can retreive information on the authorized user.
		// Make GET request to /v2/users/self.
		$response = $client->get( '/v2/users/self' );

		// Check response isn't null.
		$this->assertNotNull( $response );

		// Check response has status code 200.
		$this->assertEquals( 200, $response->getStatusCode() );

		$response_info = json_decode( $response->getBody() );

		// Check that entity id exists.
		$this->assertTrue( isset( $response_info->result[0]->entityId ) );

		// Check that entityId isn't empty.
		$this->assertNotEmpty( $response_info->result[0]->entityId );

		// Check that the profile conatains the expected information.
		$this->assertEquals( getenv( 'BADGRIO_EXPECTED_LASTNAME' ), $response_info->result[0]->lastName );

	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_admin_reads_own_backpack() {
		// Password grant admin client.
		$admin_client_parameters = array(
			'username'                  => getenv( 'BADGR_ADMIN_USERNAME' ),
			'as_admin'                  => true,
			'badgr_server_public_url'   => getenv( 'BADGR_SERVER_PUBLIC_URL' ),
			'badgr_server_internal_url' => getenv( 'BADGR_SERVER_INTERNAL_URL' ),
			'badgr_server_flavor'       => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
			'badgr_password'            => getenv( 'BADGR_ADMIN_PASSWORD' ),
			'client_id'                 => getenv( 'BADGR_SERVER_PASSWORD_GRANT_CLIENT_ID' ),
		);

		$admin_client = null;

		try {
			$admin_client = BadgrClient::make_instance( $admin_client_parameters );
			$admin_client->get_access_token_from_password_grant();
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}

		// Check backpack.
		$response = $admin_client->get( '/v2/backpack/assertions' );

		$success = false;

		// Check for 200 response.
		if ( null !== $response && 200 === $response->getStatusCode() ) {
			$response_info = json_decode( $response->getBody() );
			if ( isset( $response_info->status->success ) &&
				true === $response_info->status->success &&
				isset( $response_info->result ) && is_array( $response_info->result ) ) {
				$success = true;
			}
		}

		$this->assertTrue( $success );

	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_password_client_has_proper_scopes() {

		// Password grant admin client.
		$admin_client_parameters = array(
			'username'                  => getenv( 'BADGR_ADMIN_USERNAME' ),
			'as_admin'                  => true,
			'badgr_server_public_url'   => getenv( 'BADGR_SERVER_PUBLIC_URL' ),
			'badgr_server_internal_url' => getenv( 'BADGR_SERVER_INTERNAL_URL' ),
			'badgr_server_flavor'       => BadgrClient::FLAVOR_LOCAL_R_JAMIROQUAI,
			'badgr_password'            => getenv( 'BADGR_ADMIN_PASSWORD' ),
			'client_id'                 => getenv( 'BADGR_SERVER_PASSWORD_GRANT_CLIENT_ID' ),
		);

		$admin_client = null;

		try {
			$admin_client = BadgrClient::make_instance( $admin_client_parameters );
			$admin_client->get_access_token_from_password_grant();

			// Assert success.
			$this->assertTrue( true );
		} catch ( BadMethodCallException $e ) {
			$this->fail( 'Exception thrown on client creation: ' . $e->getMessage() );
		}
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function test_unconfigured_client_returns_null_response() {
		$client = new BadgrClient();

		$response = $client->get( 'anyurl' );

		$this->assertNull( $response );
	}
}
