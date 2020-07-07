<?php
namespace Gtg_Advanced_Blocks\Modules\Icon_Sets;

use Gtg_Advanced_Blocks\Modules\CustomIcons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Icon_Set_Base {

	protected $dir_name = '';
	protected $directory = '';
	protected $data_file = '';
	protected $stylesheet_file = '';
	protected $allowed_zipped_files = [];
	protected $files_to_save = [];
	/**
	 * Webfont extensions.
	 *
	 * @var array
	 */
	protected $allowed_webfont_extensions = [ 'woff', 'woff2', 'ttf', 'svg', 'otf', 'eot' ];

	abstract protected function extract_svg_list();

	abstract protected function get_type();

	abstract public function get_name();

	abstract protected function get_url( $filename = '' );

	/**
	 * Icon Set Base constructor.
	 *
	 * @param $directory
	 */
	public function __construct( $directory ) {
		$this->directory = $directory;

		return $this->is_icon_set() ? $this : false;
	}

	private function is_path_dir( $path ) {
		return '/' === substr( $path, -1 );
	}

	private function is_file_allowed( $path_name ) {
		$check = $this->directory . $path_name;
		if ( ! file_exists( $check ) ) {
			return false;
		}
		if ( $this->is_path_dir( $path_name ) ) {
			return is_dir( $check );
		}

		return true;
	}

	/**
	 * is icon set
	 *
	 * validate that the current uploaded zip is in this icon set format
	 *
	 * @return bool
	 */
	public function is_icon_set() {
		foreach ( $this->allowed_zipped_files as $file ) {
			if ( ! $this->is_file_allowed( $file ) ) {
				return false;
			}
		}

		return true;
	}

	public function is_valid() {
		return false;
	}

	/**
	 * cleanup_temp_files
	 *
	 * @param \WP_Filesystem_Base $wp_filesystem
	 */
	protected function cleanup_temp_files( $wp_filesystem ) {
		$wp_filesystem->rmdir( $this->directory, true );
	}

	public function get_upload_folder_url() {
		$wp_upload_dir = wp_upload_dir();
		$url           = $wp_upload_dir['baseurl'] . '/gutengeek/custom-icons';

		/**
		 * Upload file URL.
		 *
		 * Filters the URL to a file uploaded using Gtg_Advanced_Blocks\ forms.
		 *
		 * @param string $url       File URL.
		 * @param string $file_name File name.
		 *
		 */
		$url = apply_filters( 'gutengeek_custom_icons_folder_url', $url );

		return $url;
	}

	/**
	 * Gets the URL to uploaded file.
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	public function get_file_url( $file_name ) {
		$url = $this->get_upload_folder_url() . '/' . $file_name;

		/**
		 * Upload file URL.
		 *
		 * Filters the URL to a file uploaded using Gtg_Advanced_Blocks\ forms.
		 *
		 * @param string $url       File URL.
		 * @param string $file_name File name.
		 *
		 */
		$url = apply_filters( 'gutengeek_custom_icons_url', $url, $file_name );

		return $url;
	}

	public function get_icon_sets_dir() {
		$path = CustomIcons::get_upload_folder_path();

		/**
		 * Upload file path.
		 *
		 * Filters the path for custom icons file uploads using custom icons.
		 *
		 * @param string $path .
		 */
		$path = apply_filters( 'gutengeek_custom_icons_dir', $path );
		self::gtg_get_ensure_upload_dir( $path );

		return $path;
	}

	public function get_ensure_upload_dir( $dir = '' ) {
		$path = $this->get_icon_sets_dir();
		if ( ! empty( $dir ) ) {
			$path .= '/' . $dir;
		}

		return self::gtg_get_ensure_upload_dir( $path );
	}

	public function move_files() {
		$wp_filesystem = CustomIcons::get_wp_filesystem();
		$unique_name   = $this->get_unique_name();
		$to            = $this->get_ensure_upload_dir( $unique_name ) . '/';

		foreach ( $wp_filesystem->dirlist( $this->directory, false, true ) as $file ) {
			$full_path = $this->directory . $file['name'];
			if ( $wp_filesystem->is_dir( $full_path ) ) {
				$wp_filesystem->mkdir( $to . $file['name'] );

				foreach ( $file['files'] as $filename => $sub_file ) {
					$new_path = $to . $file['name'] . DIRECTORY_SEPARATOR . $filename;
					$wp_filesystem->move( $full_path . DIRECTORY_SEPARATOR . $filename, $new_path );
				}
			} else {
				$new_path = $to . $file['name'];
				$wp_filesystem->move( $full_path, $new_path );
			}
		}

		$this->cleanup_temp_files( $wp_filesystem );
		$this->dir_name  = $unique_name;
		$this->directory = $to;
	}

	public function build_config() {
		$name     = $this->get_name();
		$key_name = sanitize_title( $name );
		$counter  = 1;
		while ( isset( $config[ $key_name ] ) ) {
			$key_name = $key_name . '-' . $counter;
			$counter++;
		}

		$icon_set_config = [
			'name'             => $name,
			'key_name'         => $key_name,
			'id'               => 0,
			'icon_set_name'    => '',
			'label'            => ucwords( str_replace( [ '-', '_' ], ' ', $name ) ),
			'custom_icon_type' => $this->get_type(),
		];

		$icons                    = $this->extract_svg_list();
		$icon_set_config['count'] = count( $icons );
		$icon_set_config['icons'] = $icons;

		return $icon_set_config;
	}

	public function get_unique_name() {
		$name     = $this->get_name();
		$basename = $name;
		$counter  = 1;
		while ( ! $this->is_name_unique( $name ) ) {
			$name = $basename . '-' . $counter;
			$counter++;
		}

		return $name;
	}

	private function is_name_unique( $name ) {
		return ! is_dir( $this->get_icon_sets_dir() . '/' . $name );
	}

	public static function gtg_get_ensure_upload_dir( $path ) {
		if ( file_exists( $path . '/index.php' ) ) {
			return $path;
		}
		if ( !function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		wp_mkdir_p( $path );

		$files = [
			[
				'file'    => 'index.php',
				'content' => [
					'<?php',
					'// Silence is golden.',
				],
			],
			[
				'file'    => '.htaccess',
				'content' => [
					'Options -Indexes',
					'<ifModule mod_headers.c>',
					'	<Files *.*>',
					'       Header set Content-Disposition attachment',
					'	</Files>',
					'</IfModule>',
				],
			],
		];

		WP_Filesystem();
		global $wp_filesystem;

		foreach ( $files as $file ) {
			if ( ! file_exists( trailingslashit( $path ) . $file['file'] ) ) {
				$content = implode( PHP_EOL, $file['content'] );
				$wp_filesystem->put_contents( trailingslashit( $path ) . $file['file'], $content );
			}
		}

		return $path;
	}

	public function get_dir_name() {
		return $this->dir_name;
	}

	public function get_directory() {
		return $this->directory;
	}

	public function build_svg( $info ) {
		$svg = '<svg id="' . $info['id'] . '" viewBox="' . $info['viewbox'] . '">';
		$svg .= '<path d="' . $info['d'] . '"></path>';
		$svg .= '</svg>';

		return $svg;
	}
}
