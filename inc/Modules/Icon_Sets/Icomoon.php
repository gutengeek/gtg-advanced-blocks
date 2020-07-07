<?php
namespace Gtg_Advanced_Blocks\Modules\Icon_Sets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Icomoon extends Icon_Set_Base {

	protected $data_file = 'symbol-defs.svg';
	protected $stylesheet_file = 'style.css';
	protected $allowed_zipped_files = [ 'selection.json', 'demo.html', 'Read Mw.txt', 'demo-files/', 'fonts/', 'symbol-defs.svg' ];
	protected $allowed_webfont_extensions = [ 'woff', 'ttf', 'svg', 'eot' ];

	public function get_type() {
		return __( 'Icomoon', 'gutengeek' );
	}

	public function is_valid() {
		if ( ! file_exists( $this->directory . $this->data_file ) ) {
			return false; // missing data file
		}

		return true;
	}

	private function get_json() {
		return json_decode( file_get_contents( $this->directory . $this->data_file ) );
	}

	protected function extract_svg_list() {
		$file_content = file_get_contents( $this->get_directory() . '/symbol-defs.svg' );
		$xml = new \SimpleXMLElement( $file_content );
		$symbols = $xml->children()->children();

		if ( ! count( $symbols ) ) {
			return [];
		}

		$svgs    = [];
		foreach ( $symbols as $symbol ) {
			$symbol_tag = $symbol->asXML();
			$attributes = $symbol->attributes();

			$id = (string) $attributes['id'];

			if ( ! $id ) {
				continue;
			}

			// Regex name.
			preg_match( '/<title.*?>((.|\n)*?)<\/title>/', $symbol_tag, $title );
			$name = str_replace( 'icon-', '', $id );
			if ( isset( $title[1] ) ) {
				$name = esc_html( $title[1] );
			}

			$title_tag = '';
			if ( isset( $title[0] ) ) {
				$title_tag = $title[0];
			}

			$svg = str_replace( [ 'symbol', $title_tag ], [ 'svg', '' ], $symbol_tag );

			$svgs[ $id ] = [
				'id'   => $id,
				'name' => $name,
				'svg'  => $svg,
			];
		}

		return $svgs;
	}

	protected function get_url( $filename = '' ) {
		return $this->get_file_url( $this->get_name() . $filename );
	}

	public function get_name() {
		$config = $this->get_json();
		if ( isset( $config->metadata->name ) ) {
			return $config->metadata->name;
		}

		if ( isset( $config->preferences->fontPref->metadata->fontFamily ) ) {
			return $config->preferences->fontPref->metadata->fontFamily;
		}

		return __( 'icomoon', 'gutengeek' );
	}
}
