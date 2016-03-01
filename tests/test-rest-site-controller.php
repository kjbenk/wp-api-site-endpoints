<?php
/**
 * Unit tests covering WP_Test_REST_Site_Controller functionality.
 *
 * @package WordPress
 * @subpackage JSON API
 */

class WP_Test_REST_Site_Controller extends WP_Test_REST_Controller_Testcase {

	public function setUp() {
		parent::setUp();

		$this->user = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $this->user );

		$this->endpoint = new WP_REST_Site_Controller();
	}

	public function test_register_routes() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/wp/v2/site', $routes );
	}

	public function test_context_param() {
		$request  = new WP_REST_Request( 'OPTIONS', '/wp/v2/site' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 'view', $data['endpoints'][0]['args']['context']['default'] );
	}

	public function test_get_items() {
		$options = $this->endpoint->get_item_mappings();

		$request = new WP_REST_Request( 'GET', '/wp/v2/site' );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertCount( count( $options ), $data );
	}

	public function test_get_items_unauthenticated() {
		wp_set_current_user( 0 );
		$request = new WP_REST_Request( 'GET', '/wp/v2/site' );
		$response = $this->server->dispatch( $request );

		$this->assertErrorResponse( 'rest_forbidden', $response, 401 );
	}

	public function test_get_item() {
		$request = new WP_REST_Request( 'GET', '/wp/v2/site/title' );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertNotEmpty( $data );
	}

	public function test_get_item_unauthenticated() {
		wp_set_current_user( 0 );
		$request = new WP_REST_Request( 'GET', '/wp/v2/site/title' );
		$response = $this->server->dispatch( $request );

		$this->assertErrorResponse( 'rest_forbidden', $response, 401 );
	}

	public function test_get_item_invalid_site_option() {
		$request = new WP_REST_Request( 'GET', sprintf( '/wp/v2/site/%d', 0 ) );
		$response = $this->server->dispatch( $request );

		$this->assertErrorResponse( 'rest_site_invalid_option', $response, 404 );
	}

	public function test_create_item() {
		// No op
	}

	public function test_update_item() {
		update_option( 'blogname', 'Old Title' );
		$_POST['title'] = 'New Title';

		$request = new WP_REST_Request( 'PUT', '/wp/v2/site/title' );
		$request->set_body_params( $_POST );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 'New Title', $data['title'] );
	}

	public function test_update_item_unauthenticated() {
		wp_set_current_user( 0 );
		$_POST['title'] = 'New Title';

		$request = new WP_REST_Request( 'PUT', '/wp/v2/site/title' );
		$request->set_body_params( $_POST );
		$response = $this->server->dispatch( $request );

		$this->assertErrorResponse( 'rest_forbidden', $response, 401 );
	}

	public function test_update_item_invalid_site_option() {
		$request = new WP_REST_Request( 'PUT', sprintf( '/wp/v2/site/%d', 0 ) );
		$response = $this->server->dispatch( $request );

		$this->assertErrorResponse( 'rest_site_invalid_option', $response, 404 );
	}

	public function test_delete_item() {
		// No op
	}

	public function test_prepare_item() {
		$title = get_option( 'blogname' );
		$request = new WP_REST_Request( 'GET', '/wp/v2/site/title' );
		$response = $this->server->dispatch( $request );
		$data = $this->endpoint->prepare_item_for_response( 'title', $request );
		$data = $response->get_data();

		$this->assertEquals( $title, $data['title'] );
	}

	public function test_get_item_schema() {
		$request = new WP_REST_Request( 'OPTIONS', '/wp/v2/site' );
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();
		$properties = $data['schema']['properties'];
		$options = $this->endpoint->get_item_mappings();

		$this->assertEquals( count( $options ), count( $properties ) );

		foreach ( $options as $key => $option ) {
			$this->assertArrayHasKey( $key, $properties );
		}
	}

}
