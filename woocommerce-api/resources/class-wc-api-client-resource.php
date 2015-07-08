<?php
/**
 * WC API Client Resource class
 *
 * @since 2.0
 */
abstract class WC_API_Client_Resource {


	/** @var string resource endpoint */
	protected $endpoint;

	/** @var WC_API_Client class instance */
	protected $client;

	/** @var string request method, e.g. GET */
	protected $request_method;

	/** @var string request path, e.g. orders/123 */
	protected $request_path;

	/** @var array request params, e.g. { 'status' => 'processing' } */
	protected $request_params;

	/** @var array request body data, only used for PUT/POST requests */
	protected $request_body;


	/**
	 * Set the endpoint and client
	 *
	 * @since 2.0
	 * @param string $endpoint
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $endpoint, $client ) {

		$this->endpoint = $endpoint;
		$this->client = $client;
	}


	/**
	 * Set the arguments for the request, required:
	 *
	 * `method`
	 * `path`
	 *
	 * optional:
	 *
	 * `params`
	 * `body`
	 *
	 * @since 2.0
	 * @param array $args
	 */
	protected function set_request_args( $args ) {

		$this->request_method = $args['method'];
		$this->request_path   = isset( $args['path'] ) ? $args['path'] : null;
		$this->request_params = isset( $args['params'] ) ? $args['params'] :[];
		$this->request_body   = isset( $args['body'] ) ? $args['body'] : [];

		// convert bool true to string 'true', required for DELETE endpoints
		if ( isset( $this->request_params['force'] ) && $this->request_params['force'] ) {
			$this->request_params['force'] = 'true';
		}
	}


	/**
	 * Return the full endpoint path, e.g. given an endpoint of `orders` and a
	 * path of `123`, return `orders/123`. For nested resources, this will return
	 * the full path as well
	 *
	 * @since 2.0
	 * @return string
	 */
	protected function get_endpoint_path() {

		if ( $this->request_path ) {
			return $this->endpoint  . '/' . implode( '/', (array) $this->request_path );

		} else {
			return $this->endpoint;

		}
	}


	/**
	 * Return the request data, either query parameters (for GET/DELETE requests)
	 * or the request body (for PUT/POST requests)
	 *
	 * @since 2.0
	 * @return array
	 */
	protected function get_request_data() {

		return 'GET' == $this->request_method ? $this->request_params : $this->request_body;
	}


	/**
	 * Perform the request and return the response
	 *
	 * @since 2.0
	 * @return array|object
	 */
	protected function do_request() {

		return $this->client->make_api_call( $this->request_method, $this->get_endpoint_path(), $this->get_request_data() );
	}


}


