<?php

namespace Gtg_Advanced_Blocks\Blocks;

use Gtg_Advanced_Blocks\Abstracts\Post_Block;
use Gtg_Advanced_Blocks\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Post_Grid extends Post_Block implements InterfaceBlock {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'wp_ajax_nopriv_gutengeek_post_ajax_loadmore', [ $this, 'ajax_loadmore' ] );
		add_action( 'wp_ajax_gutengeek_post_ajax_loadmore', [ $this, 'ajax_loadmore' ] );
	}

	/**
	 * Block id
	 *
	 * @return mixed|string
	 */
	public function set_slug() {
		return 'post-grid';
	}

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes() {
		return [
			'render_callback' => [ $this, 'render_callback' ],
		];
	}

	/**
	 * server render block
	 */
	public function render_callback( $attributes ) {
		global $gutengeek_post_settings;

		$attributes = array_merge([
			'imgSize' => 'large',
			'pagiLoadmoreLabel' => __('Load More', 'gutengeek'),
			'pagiNextEnableIcon' => true,
			'align' => 'center',
			'enablePostExcerpt' => true,
			'enableFilter' => false,
			'excerptLength' => 25,
			'pagiPageLimit' => 5,
			'enablePostTitle' => true,
			'pagiLoadmoreEnableIconAfter' => false,
			'enablePostImage' => true,
			'titleTag' => 'h3',
			'pagiItemSize' => 'medium',
			'orderBy' => 'date',
			'pagiType' => 'none',
			'enableButton' => false,
			'pagiNextEnableIcon' => false,
			'pagiLoadmoreEnableIconBefore' => false,
			'enablePostAuthor' => true,
			'order' => 'desc',
			'termType' => [],
			'enablePostDate' => true,
			'pagiShorten' => false,
			'enablePostComment' => true,
			'filterRootText' => __('All', 'gutengeek'),
			'numberOfPosts' => 6,
			'pagiNextLabel' => __('Next', 'gutengeek'),
			'pagiLoadmoreWidth' => 'auto',
			'filterRoot' => true,
			'newTab' => false,
			'pagiPrevEnableIcon' => false,
			'pagiLoadmoreSize' => 'medium',
			'buttonSize' => 'small',
			'buttonStyle' => 'primary',
			'filterAjax' => false
		], $attributes);
		if ( empty( $attributes['blockId'] ) ) {
			return null;
		}
		$query = [];
		if ( ! empty($attributes['enableFilter']) && $attributes['enableFilter'] && ! $attributes['filterAjax'] && $attributes['termType'] ) {
			foreach ( $attributes['termType'] as $term ) {
				$attributes['categories'] = $term;
				$new_query['query']       = gtg_build_query( $attributes, 'grid' );
				$new_query['term']        = $term;
				$query[]                  = $new_query;
			}

			if ( isset( $_GET['term'] ) && $_GET['term'] !== '' ) {
				$attributes['categories'] = sanitize_text_field( $_GET['term'] );
			}
		} else {
			if ( isset( $_GET['term'] ) && $_GET['term'] !== '' ) {
				$attributes['categories'] = sanitize_text_field( $_GET['term'] );
			}

			$new_query['query'] = gtg_build_query( $attributes, 'grid' );
			$new_query['term']  = '';
			$query[]            = $new_query;
		}

		if ( isset( $_GET['term'] ) && $_GET['term'] !== '' ) {
			$attributes['categories'] = sanitize_text_field( $_GET['term'] );
		}

		$gutengeek_post_settings['grid'][ $attributes['blockId'] ] = $attributes;

		ob_start();

		$this->gtg_get_post_html( $attributes, $query, 'grid' );

		return ob_get_clean();
	}

	/**
	 * Loadmore posts via AJAX.
	 */
	public function ajax_loadmore() {
		$attributes               = isset($_POST['attributes']) ? array_map( 'gtg_sanitize_value', $_POST['attributes'] ) : [];
		$attributes['paged']      = absint( $_POST['paged'] );
		$attributes['categories'] = isset( $_POST['term'] ) && $_POST['term'] ? array_map( 'sanitize_text_field', $_POST['term'] ) : '';

		$query = gtg_build_query( $attributes, 'grid' );

		if ( $query->have_posts() ) {
			$return['status'] = true;
			ob_start();
			while ( $query->have_posts() ) : ( $query->the_post() );
				gtg_get_template( 'blocks/loop-post-grid.php', [ 'attributes' => $attributes ] );
			endwhile;
			$return['html'] = ob_get_clean();
		} else {
			$return['status'] = false;
			$return['html']   = '';
		}

		wp_send_json( $return ); die();
	}
}
