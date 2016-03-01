<?php

/**
 * Manage a WordPress site
 */

class WP_REST_Site_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'site';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_items' ),
				'args'     => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	public function get_items_permissions_check( $request ) {

	}

	/**
	 * Get a collection of site settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$options  = $this->get_endpoint_args_for_item_schema( WP_REST_Server::READABLE );
		$response = array();

		foreach ( $options as $name => $args ) {
			if ( ! $this->get_item_mapping( $name ) ) {
				continue;
			}

			$response[ $name ] = $this->prepare_item_for_response( $name, $request );
		}

		return rest_ensure_response( $response );
	}

	public function delete_item_permission_check( $request ) {

	}

	public function delete_item( $request ) {

	}

	/**
	 * Prepare a site setting for response
	 *
	 * @param  string           $option_name The option name
	 * @param  WP_REST_Request  $request
	 * @return string           $value       The option value
	 */
	public function prepare_item_for_response( $option_name, $request ) {
		$schema = $this->get_item_schema();
		$value  = get_option( $this->get_item_mapping( $option_name ) );
		$value  = ( ! $value && isset( $schema['properties'][ $option_name ]['default'] ) ) ? $schema['properties'][ $option_name ]['default'] : $value;

		if ( isset( $schema['properties'][ $option_name ]['type'] ) ) {
			settype( $value, $schema['properties'][ $option_name ]['type'] );
		}

		return $value;
	}

	/**
	 * Get the site setting schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'site',
			'type'       => 'object',
			'properties' => array(

				// Reference: https://docs.google.com/spreadsheets/d/1vI-s8MjbEllCR0_0BmBgaGvKNm1Fa42MCoqaKCwbRyQ/edit#gid=0

				// Discussion

				'avatar_default' => array(
					'description' => __( 'Default Avatar' ),
					'type'        => 'string',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'avatar_rating' => array(
					'description' => __( 'Maximum Rating' ),
					'type'        => 'string',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'comment_blacklist' => array(
					'description' => __( 'When a comment contains any of these words in its content, name, URL, email, or IP, it will be put in the trash. One word or IP per line. It will match inside words, so “press” will match “WordPress”.' ),
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'close_comments_days_old' => array(
					'description' => __( '[integer, default: 14]' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'close_comments_for_old_posts' => array(
					'description' => __( 'Automatically close comments on articles older than [integer, default: 14] days.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'comment_max_links' => array(
					'description' => __( 'Hold a comment in the queue if it contains [integer, default: 2] or more links. (A common characteristic of comment spam is a large number of hyperlinks.)' ),
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'comment_moderation' => array(
					'description' => __( 'Comment must be manually approved.' ),
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'comment_order' => array(
					'description' => __( 'Comments should be displayed with the ["older" or "newer", default: "older"] comments at the top of each page.' ),
					'type'        => 'string',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'comment_registration' => array(
					'description' => __( 'Users must be registered and logged in to comment.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'comment_whitelist' => array(
					'description' => __( 'Comment author must have a previously approved comment.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'comments_notify' => array(
					'description' => __( 'Anyone posts a comment.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'comments_per_page' => array(
					'description' => __( '[integer, default: 50]' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'default_comment_status' => array(
					'description' => __( 'Allow people to post comments on new articles.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'default_comments_page' => array(
					'description' => __( '["last" or "first", default: "last"]' ),
					'type'        => 'string',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'default_ping_status' => array(
					'description' => __( 'Allow link notifications from other blogs (pingbacks and trackbacks) on new articles.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'default_pingback_flag' => array(
					'description' => __( 'Attempt to notify any blogs linked to from the article.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'moderation_keys' => array(
					'description' => __( 'When a comment contains any of these words in its content, name, URL, email, or IP, it will be held in the moderation queue. One word or IP per line. It will match inside words, so “press” will match “WordPress”.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'moderation_notify' => array(
					'description' => __( 'A comment is held for moderation.' ),
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'comment_pagination' => array(
					'description' => __( 'Break comments into pages with [integer, default: 50] top level comments per page and the ["last" or "first", default: "last"] page displayed by default.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'comment_require_name_email' => array(
					'description' => __( 'Comment author must fill out name and email.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'show_avatars' => array(
					'description' => __( 'Show Avatars.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'thread_comments' => array(
					'description' => __( 'Enable threaded (nested) comments [integer, default: 5] levels deep.' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'thread_comments_depth' => array(
					'description' => __( '[integer, defaut: 5]' ),
					'type'        => 'integer',
					'screen'      => 'discussion',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),

				// General

				'admin_email' => array(
					'description' => __( 'Email Address' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'tagline' => array(
					'description' => __( 'Tagline' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'title' => array(
					'description' => __( 'Site Title' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'date_format' => array(
					'description' => __( 'Date Format' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'default_role' => array(
					'description' => __( 'New User Role Default' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'url' => array(
					'description' => __( 'Site Address (URL)' ),
					'type'        => 'string',
					'screen'      => 'general',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'wordpress_url' => array(
					'description' => __( 'WordPress Address (URL)' ),
					'type'        => 'string',
					'screen'      => 'general',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'start_of_week' => array(
					'description' => __( 'Week Starts On' ),
					'type'        => 'integer',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'time_format' => array(
					'description' => __( 'Time Format' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'timezone_string' => array(
					'description' => __( 'Timezone' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
					'default'     => 'UTC',
				),
				'users_can_register' => array(
					'description' => __( 'Membership' ),
					'type'        => 'boolean',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'locale' => array(
					'description' => __( 'Site Language' ),
					'type'        => 'string',
					'screen'      => 'general',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
					'default'     => 'en_US',
				),

				// Media

				'large_size_h' => array(
					'description' => __( 'Large size height' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'large_size_w' => array(
					'description' => __( 'Large size width' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'medium_size_h' => array(
					'description' => __( 'Meduim size height' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'medium_size_w' => array(
					'description' => __( 'Meduim size width' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'thumbnail_crop' => array(
					'description' => __( 'Crop thumbnail to exact dimensions (normally thumbnails are proportional)' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'thumbnail_size_h' => array(
					'description' => __( 'Thumbnail size height' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'thumbnail_size_w' => array(
					'description' => __( 'Thumbnail size width' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'uploads_use_yearmonth_folders' => array(
					'description' => __( 'Organize my uploads into month- and year-based folders' ),
					'type'        => 'integer',
					'screen'      => 'media',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),

				// Permalinks

				'permalink_structure' => array(
					'description' => __( 'Permalink Settings' ),
					'type'        => 'string',
					'screen'      => 'permalinks',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'permalink_category_base' => array(
					'description' => __( 'Category base' ),
					'type'        => 'string',
					'screen'      => 'permalinks',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'permalink_tag_base' => array(
					'description' => __( 'Tag base' ),
					'type'        => 'string',
					'screen'      => 'permalinks',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),

				// Reading

				'robots_can_index' => array(
					'description' => __( 'It is up to search engines to honor this request.' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'page_for_posts' => array(
					'description' => __( 'Posts page' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'page_on_front' => array(
					'description' => __( 'Front page' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'posts_per_page' => array(
					'description' => __( 'Blog pages show at most.' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'posts_per_rss' => array(
					'description' => __( 'Syndication feeds show the most recent.' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'rss_use_excerpt' => array(
					'description' => __( 'For each article in a feed, show.' ),
					'type'        => 'integer',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'show_on_front' => array(
					'description' => __( '(radio) Front page displays.' ),
					'type'        => 'string',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),
				'show_on_front' => array(
					'description' => __( '(radio) Front page displays.' ),
					'type'        => 'string',
					'screen'      => 'reading',
					'context'     => array( 'view', 'edit' ),
					'public'      => true,
				),

				// Writing

				'default_category' => array(
					'description' => __( 'Default Post Category.' ),
					'type'        => 'integer',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'default_email_category' => array(
					'description' => __( 'Default Mail Category.' ),
					'type'        => 'integer',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'default_post_format' => array(
					'description' => __( 'Default Post Format.' ),
					'type'        => 'string',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'mailserver_login' => array(
					'description' => __( 'Login Name.' ),
					'type'        => 'string',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'mailserver_pass' => array(
					'description' => __( 'Password.' ),
					'type'        => 'string',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'mailserver_port' => array(
					'description' => __( 'Port.' ),
					'type'        => 'integer',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'mailserver_url' => array(
					'description' => __( 'Mail Server.' ),
					'type'        => 'string',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
				'ping_sites' => array(
					'description' => __( 'When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see Update Services on the Codex. Separate multiple service URLs with line breaks.' ),
					'type'        => 'string',
					'screen'      => 'writing',
					'context'     => array( 'view', 'edit' ),
					'public'      => false,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

	/**
	 * Return an array of option name mappings
	 *
	 * @return array
	 */
	public function get_item_mappings() {
		return array(

			// Discussion

			'comment_blacklist'          => 'blacklist_keys',
			'comment_pagination'         => 'page_comments',
			'comment_require_name_email' => 'require_name_email',

			// General

			'title'                   => 'blogname',
			'tagline'                 => 'blogdescription',
			'wordpress_url'           => 'siteurl',
			'url'                     => 'home',
			'users_can_register'      => 'users_can_register',
			'timezone_string'         => 'timezone_string',
			'date_format'             => 'date_format',
			'time_format'             => 'time_format',
			'start_of_week'           => 'start_of_week',
			'locale'                  => 'WPLANG',

			// Permalinks

			'permalink_structure'     => 'permalink_structure',
			'permalink_category_base' => 'category_base',
			'permalink_tag_base'      => 'tag_base',

			// Reading

			'robots_can_index'        => 'blog_public',
		);
	}

	/**
	 * Return the mapped option name
	 *
	 * @param  string $option_name The API option name
	 * @return string|bool         The mapped option name, or false on failure
	 */
	public function get_item_mapping( $option_name ) {
		$mappings = $this->get_item_mappings();

		return isset( $mappings[ $option_name ] ) ? $mappings[ $option_name ] : false;
	}

}
