<?php

namespace Gtg_Advanced_Blocks;

defined( 'ABSPATH' ) || exit();

class Api {

	/**
	 * api server to fetch templates
	 */
	public $api_info_url = 'https://gutengeek.com/wp-json/gutengeek-api/v1/info/';

	/**
	 * api server to fetch user
	 */
	public $api_user_info_url = 'https://gutengeek.com/wp-json/gutengeek-api/v1/user/';

	/**
	 * api server to fetch templates
	 */
	public $api_store_url = 'https://gutengeek.com/wp-json/gutengeek-api/v1/';


	/**
	 * api post feedback
	 */
	public $feedback_url = 'https://gutengeek.com/wp-json/gutengeek-api/v1/feedback';

	/**
	 * api controllers
	 *
	 * @var array
	 */
	public $controllers = [];

	/**
	 * Api_Server constructor.
	 */
	public function __construct() {
		// register rest routes
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		// register fields for 'post-*' blocks
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ] );
		// register api rest field orderby
		add_action( 'init', [ $this, 'register_rest_fields_orderby' ] );
		add_action( 'init', [ $this, 'register_post_meta' ] );
	}

	/**
	 * register routes
	 */
	public function register_rest_routes() {
		$controllers = $this->get_api_controllers();
		foreach ( $controllers as $controller ) {
			$api_controller = new $controller();
			$api_controller->register_routes();
			$this->controllers[ $api_controller->get_namespace() ] = $api_controller;
		}
	}

	/**
	 * get api controllers
	 *
	 * @return mixed|void
	 */
	private function get_api_controllers() {
		return apply_filters( 'gutengeek_api_controllers', [
			'\Gtg_Advanced_Blocks\API\Core',
			'\Gtg_Advanced_Blocks\API\Template_Library'
		] );
	}

	/**
	 * register rest fields
	 */
	public function register_rest_fields() {
		$post_type = gtg_get_post_types();

		foreach ( $post_type as $key => $value ) {
			// featured
			register_rest_field(
				$value['value'],
				'gutengeek_image_src',
				[
					'get_callback' => [ $this, 'blocks_get_image_src_callback' ],
					'update_callback' => null,
					'schema' => null,
				]
			);

			// author
			register_rest_field(
				$value['value'],
				'gutengeek_author',
				[
					'get_callback' => [ $this, 'blocks_get_author_callback' ],
					'update_callback' => null,
					'schema' => null,
				]
			);

			// comment
			register_rest_field(
				$value['value'],
				'gutengeek_total_comments',
				[
					'get_callback' => [ $this, 'get_total_comments' ],
					'update_callback' => null,
					'schema' => null,
				]
			);

			// excerpt
			register_rest_field(
				$value['value'],
				'gutengeek_excerpt',
				[
					'get_callback' => [ $this, 'blocks_get_excerpt_callback' ],
					'update_callback' => null,
					'schema' => null,
				]
			);

			// excerpt
			register_rest_field(
				$value['value'],
				'_gutengeek_post_settings',
				[
					'get_callback' => [ $this, 'blocks_get_post_settings_callback' ],
					'update_callback' => null,
					'schema' => $this->get_post_settings_schema(),
				]
			);
		}
	}

	/**
	 * register reset fields orderBy
	 */
	public function register_rest_fields_orderby() {
		$post_type = gtg_get_post_types();

		foreach ( $post_type as $key => $type ) {
			add_filter( "rest_{$type['value']}_collection_params", [ $this, 'blocks_add_orderby_callback' ], 10, 1 );
		}
	}

	public function get_post_settings_schema() {
		return [
			'properties' => [
				'color' => [
					'description' => __( 'Unique identifier for the resource.', 'gutengeek' ),
					'type' => [ 'string', null ],
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
				],
				'bodyTypography' => [
					'description' => __( 'Body typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties(),
				],
				'h1Typography' => [
					'description' => __( 'H1 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'h2Typography' => [
					'description' => __( 'H2 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'h3Typography' => [
					'description' => __( 'H3 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'h4Typography' => [
					'description' => __( 'H4 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'h5Typography' => [
					'description' => __( 'H5 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'h6Typography' => [
					'description' => __( 'H6 typography', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_typo_properties()
				],
				'rowContainerWidth' => [
					'description' => __( 'Row Container Width', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_range_properties()
				],
				'rowGutter' => [
					'description' => __( 'Row Gutter', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_range_properties()
				],
				'buttonDefault' => [
					'description' => __( 'Button Default', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonPrimary' => [
					'description' => __( 'Button Primary', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonSecondary' => [
					'description' => __( 'Button Secondary', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonWarning' => [
					'description' => __( 'Button Warning', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonInfo' => [
					'description' => __( 'Button INfo', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonSuccess' => [
					'description' => __( 'Button Primary', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				],
				'buttonDanger' => [
					'description' => __( 'Button Danger', 'gutengeek' ),
					'type' => 'object',
					'context' => [ 'view', 'edit' ],
					'readonly' => true,
					'properties' => $this->get_button_properties()
				]
			]
		];
	}

	/**
	 * register post meta
	 */
	public function register_post_meta() {

		// meta key storage google font each the post
		register_meta( 'post', '_gutengeek_post_google_font', [
			'show_in_rest' => false,
			'description' => __( 'GutenGeek google font meta field', 'gutengeek' ),
			'single' => true,
			'type' => 'string',
			'auth_callback' => [ $this, 'auth_callback' ],
		] );

		// meta key storage css each the post
		register_meta( 'post', '_gutengeek_css', [
			'show_in_rest' => false,
			'description' => __( 'GutenGeek css of the post', 'gutengeek' ),
			'single' => true,
			'type' => 'string',
			'auth_callback' => [ $this, 'auth_callback' ],
		] );
		// prepare_callbacks
		register_meta( 'post', '_gutengeek_post_settings', [
			'show_in_rest' => [
				'schema' => $this->get_post_settings_schema(),
			],
			'description' => __( 'GutenGeek global post settings', 'gutengeek' ),
			'single' => true,
			'type' => [ 'object', 'null' ],
			'auth_callback' => [ $this, 'auth_callback' ],
			'arg_options' => [
				'sanitize_callback' => [ $this, 'sanitize_callback' ]
			],
		] );
	}

	private function get_typo_properties() {
		return [
			'openTypography' => [
				'type' => 'boolean',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'fontStyle' => [
				'type' => [ 'string', 'null' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'fontSize' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'tablet' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'mobile' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'unit' => [
						'type' => 'string',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'openRange' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'responsive' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'value' => [
						'type' => [ 'null', 'boolean', 'number', 'string' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					]
				],
			],
			'lineHeight' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'tablet' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'mobile' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'unit' => [
						'type' => 'string',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'openRange' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'responsive' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					]
				]
			],
			'letterSpacing' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'tablet' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'mobile' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'unit' => [
						'type' => 'string',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'openRange' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'responsive' => [
						'type' => [ 'null', 'boolean' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					]
				]
			],
			'textTransform' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'tablet' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'mobile' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'openTransform' => [
						'type' => 'boolean',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					]
				]
			],
			'typography' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'fontFamily' => [
						'type' => 'string',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'fontWeight' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					]
				]
			]
		];
	}


	/**
	 * range slider properties
	 */
	public function get_range_properties() {
		return [
			'desktop' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'tablet' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'mobile' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'unit' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'openRange' => [
				'type' => [ 'boolean', 'number', 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'responsive' => [
				'type' => [ 'boolean', 'number', 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			]
		];
	}

	public function get_button_properties() {
		return [
			'openGroupControl' => [
				'type' => [ 'boolean', 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'typography' => [
				'description' => __( 'Button typography', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_typo_properties(),
			],
			'borderRadius' => [
				'description' => __( 'Button border radius', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_border_radius_properties(),
			],
			'borderRadiusHover' => [
				'description' => __( 'Button border radius hover', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_border_radius_properties(),
			],
			'border' => [
				'description' => __( 'Button border', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_border_properties(),
			],
			'borderHover' => [
				'description' => __( 'Button border', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_border_properties(),
			],
			'boxShadow' => [
				'description' => __( 'Box Shadow', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_boxshadow_properties(),
			],
			'boxShadowHover' => [
				'description' => __( 'Box Shadow Hover', 'gutengeek' ),
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_boxshadow_properties(),
			],
			'color' => [
				'description' => __( 'Button color.', 'gutengeek' ),
				'type' => [ 'string', null ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'hoverColor' => [
				'description' => __( 'Button color hover.', 'gutengeek' ),
				'type' => [ 'string', null ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'bg' => [
				'description' => __( 'Button background.', 'gutengeek' ),
				'type' => [ 'string', null ],
				'context' => [ 'view', 'edit' ],
				'properties' => $this->get_background_properties(),
			],
			'bgHover' => [
				'description' => __( 'Button background.', 'gutengeek' ),
				'type' => [ 'string', null ],
				'context' => [ 'view', 'edit' ],
				'properties' => $this->get_background_properties(),
			]
		];
	}

	public function get_border_properties() {
		return [
			'type' => [
				'type' => [ 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'responsive' => [
				'type' => [ 'null', 'boolean' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'global' => [
				'type' => [ 'global' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'tablet' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
					'mobile' => [
						'type' => [ 'string', 'number' ],
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
					],
				]
			],
			'openBorder' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'color' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'widthType' => [
				'type' => [ 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'unit' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'custom' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'top' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'right' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottom' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'left' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'mobile' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'top' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'right' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottom' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'left' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'tablet' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'top' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'right' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottom' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'left' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					]
				]
			]
		];
	}

	public function get_border_radius_properties() {
		return [
			'radiusType' => [
				'type' => [ 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'responsive' => [
				'type' => [ 'null', 'boolean' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'openBorderRadius' => [
				'type' => [ 'string', 'number' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'global' => [
				'type' => [ 'object' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => $this->get_range_properties()
			],
			'unit' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'custom' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'topLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'topRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'mobile' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'topLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'topRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'tablet' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'topLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'topRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomLeft' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'bottomRight' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					]
				]
			]
		];
	}

	public function get_boxshadow_properties() {
		return [
			'boxShadowType' => [
				'type' => [ 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'openBoxShadow' => [
				'type' => 'boolean',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'unit' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'boxShadow' => [
				'type' => 'object',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'color' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'horizontal' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'vertical' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'blur' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'spread' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'mobile' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'color' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'horizontal' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'vertical' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'blur' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'spread' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					],
					'tablet' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'color' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'horizontal' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'vertical' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'blur' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'spread' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
						]
					]
				]
			]
		];
	}

	public function get_background_properties() {
		return [
			'source' => [
				'type' => [ 'string' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'openBackground' => [
				'type' => [ 'string', 'number', 'boolean' ],
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'bgColor' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
			],
			'bgImage' => [
				'type' => 'string',
				'context' => [ 'view', 'edit' ],
				'readonly' => true,
				'properties' => [
					'desktop' => [
						'type' => 'object',
						'context' => [ 'view', 'edit' ],
						'readonly' => true,
						'properties' => [
							'media' => [
								'type' => 'object',
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
								'properties' => [
									'id' => [
										'type' => [ 'string', 'number' ],
										'context' => [ 'view', 'edit' ],
										'readonly' => true,
									],
									'url' => [
										'type' => [ 'string' ],
										'context' => [ 'view', 'edit' ],
										'readonly' => true,
									]
								]
							],
							'position' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'repeat' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'size' => [
								'type' => [ 'string', 'number' ],
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
							],
							'positionX' => [
								'type' => 'object',
								'context' => [ 'view', 'edit' ],
								'readonly' => true,
								'properties' => [
									'value' => [
										'type' => [ 'string', 'number'],
										'context' => [ 'view', 'edit' ],
										'readonly' => true,
									],
									'unit' => [
										'type' => [ 'string', 'number'],
										'context' => [ 'view', 'edit' ],
										'readonly' => true,
									]
								]
							]
						]
					]
				]
			]
		];
	}

	/**
	 * @return bool
	 */
	public function auth_callback() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Adds Order By values to Rest API
	 *
	 * @param $params
	 * @return mixed
	 */
	public function blocks_add_orderby_callback( $params ) {
		$params['orderby']['enum'][] = 'rand';
		$params['orderby']['enum'][] = 'menu_order';
		return $params;
	}

	public function blocks_get_author_callback( $object, $field_name, $request ) {
		return [
			'display_name' => get_the_author_meta( 'display_name', $object['author'] ),
			'author_link' => get_author_posts_url( $object['author'] )
		];
	}

	/**
	 * Get comment for the rest field
	 *
	 * @param $object
	 * @param $field_name
	 * @param $request
	 * @return mixed
	 */
	function get_total_comments( $object, $field_name, $request ) {
		// Get the comments link.
		$comments_count = wp_count_comments( $object['id'] );
		return $comments_count->total_comments;
	}

	/**
	 * Get featured image for the rest field
	 *
	 * @param $object
	 * @param $field_name
	 * @param $request
	 * @return array
	 */
	function blocks_get_image_src_callback( $object, $field_name, $request ) {
		$image_sizes = gtg_get_image_sizes();
		$featured_images = [];
		foreach ( $image_sizes as $key => $value ) {
			$size = $value['value'];
			if ($object && isset($object['featured_media'])) {
				$featured_images[ $size ] = wp_get_attachment_image_src(
					$object['featured_media'],
					$size,
					false
				);
			}
		}
		return $featured_images;
	}

	/**
	 * get except callback for rest field
	 * @param $object
	 * @param $field_name
	 * @param $request
	 * @return string|null
	 */
	public function blocks_get_excerpt_callback( $object, $field_name, $request ) {
		$excerpt = wp_trim_words( get_the_excerpt( $object['id'] ) );
		if ( !$excerpt ) {
			$excerpt = null;
		}
		return $excerpt;
	}

	public function blocks_get_post_settings_callback( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], '_gutengeek_post_settings', true );
	}

}
