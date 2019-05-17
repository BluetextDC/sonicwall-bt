<?php
/**
 * GFEloqua Extensions
 *
 * @package gfeloqua
 */

/**
 * Add Extensions functionality to GF Eloqua
 */
class GFEloqua_Extensions {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * Array of current extensions
	 *
	 * @var array
	 */
	private $extensions = array();

	/**
	 * License manager storage for each extension
	 *
	 * @var array
	 */
	private $license_managers = array();

	/**
	 * Setup default hooks for all extensions.
	 */
	public function __construct() {
		add_action( 'gfeloqua_extension_settings', array( $this, 'license_field' ), 5, 2 );
		add_action( 'wp_ajax_gfeloqua_save_extension_settings', array( $this, 'ajax_save_settings' ) );
	}

	/**
	 * Get an instance of this class.
	 *
	 * @return object  GFEloqua_Extensions object.
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new GFEloqua_Extensions();
		}

		return self::$_instance;
	}

	/**
	 * Grabs an array of current extensions
	 * TODO: Pull this from a remote source.
	 *
	 * @return array  Array of current extensions.
	 */
	public function get_extensions() {
		if ( ! $this->extensions ) {
			// initialize extensions.
			$this->extensions = array(
				'gravityforms-eloqua-bulk-retry' => array(
					'name' => esc_html__( 'Bulk Retry', 'gfeloqua' ),
					'sku' => esc_html__( 'GFELQEX-BULKR' ),
					'description' => esc_html__( 'Adds ability to re-submit failed entries in bulk.', 'gfeloqua' ),
					'purchase_url' => 'http://briandichiara.com/product/gravityforms-eloqua-bulk-retry/',
					'slug' => 'gravityforms-eloqua-bulk-retry',
					'plugin' => 'gravityforms-eloqua-bulk-retry/gravityforms-eloqua-bulk-retry.plugin.php',
				),
			);

			foreach ( $this->extensions as &$extension ) {
				$extension['is_active'] = $this->is_active( $extension['plugin'] );

				// Add activation/deactivation and settings links
				$action_url = 'plugins.php?action=%s&amp;plugin=%s&amp;plugin_status=all&amp;paged=1&amp;s';
				$extension['activate_url'] = current_user_can( 'activate_plugin', $extension['plugin'] ) ? wp_nonce_url( sprintf( $action_url, 'activate', urlencode( $extension['plugin'] ) ), 'activate-plugin_' . $extension['plugin'] ) : '';
				$extension['deactivate_url'] = current_user_can( 'deactivate_plugin', $extension['plugin'] ) ? wp_nonce_url( sprintf( $action_url, 'deactivate', urlencode( $extension['plugin'] ) ), 'deactivate-plugin_' . $extension['plugin'] ) : '';
				$extension['settings_url'] = '#TB_inline?width=300&height=550&inlineId=' . $extension['slug'] . '-settings';
			}
		}

		$this->extensions = apply_filters( 'gfeloqua_extensions', $this->extensions );

		return $this->extensions;
	}

	/**
	 * Check if extension is active
	 *
	 * @param string $slug  Slug of extension
	 *
	 * @return bool  If active.
	 */
	public function is_active( $slug ) {
		return is_plugin_active( $slug );
	}

	/**
	 * Ajax Save Action for Settings dialog
	 */
	public function ajax_save_settings() {
		$success = true;

		ob_start();

		$posted = array();

		foreach ( $_POST as $key => $value ) {
			if ( GFELOQUA_OPT_PREFIX !== substr( $key, 0, strlen( GFELOQUA_OPT_PREFIX ) ) ) {
				continue;
			}
			$posted[ $key ] = sanitize_text_field( esc_html( $value ) );
		}

		$extensions = $this->get_extensions();
		$extension_slug = $posted[ GFELOQUA_OPT_PREFIX . 'extension_slug' ] ? $posted[ GFELOQUA_OPT_PREFIX . 'extension_slug' ] : '';

		if ( ! $extension_slug ) {
			$success = false;
		}

		if ( isset( $extensions[ $extension_slug ] ) ) {
			$extension = $extensions[ $extension_slug ];
			do_action( 'gfeloqua_extension_settings', $extension, $posted );
		} else {
			$success = false;
		}

		$html = ob_get_clean();

		$response = apply_filters( 'gfeloqua_ajax_save_settings_response', array(
			'success' => $success,
			'html' => $html,
		));

		wp_send_json( $response );
		exit();
	}

	/**
	 * Save Extension License Key
	 *
	 * @param string $license_key  License key.
	 * @param array  $extension    Extension array.
	 */
	public function save_license( $license_key, $extension ) {
		if ( ! $license_key ) {
			return;
		}
		if ( ! isset( $extension['slug'] ) || empty( $extension['slug'] ) ) {
			return false;
		}
		update_option( GFELOQUA_OPT_PREFIX . 'extension_license_key_' . $extension['slug'], $license_key );
	}

	/**
	 * Get Extension License
	 *
	 * @param array $extension  Extension array.
	 *
	 * @return string  License key for extension.
	 */
	public function get_license( $extension ) {
		if ( ! isset( $extension['slug'] ) || empty( $extension['slug'] ) ) {
			return false;
		}
		return get_option( GFELOQUA_OPT_PREFIX . 'extension_license_key_' . $extension['slug'] );
	}

	/**
	 * Insert a license field to all settings pages.
	 *
	 * @param array $extension  Extension array.
	 */
	public function license_field( $extension, $posted = array() ) {
		if ( ! $extension['is_active'] ) {
			return;
		}

		if ( ! isset( $this->license_managers[ $extension['slug'] ] ) ) {
			$this->init_extension( $extension );
		}

		$license_stored = false;
		$license = $this->get_license( $extension );
		$error = '';

		if ( isset( $posted[ GFELOQUA_OPT_PREFIX . 'extension_license_key' ] ) ) {
			$extension_license_key = $posted[ GFELOQUA_OPT_PREFIX . 'extension_license_key' ];

			if ( $extension_license_key ) {
				if ( ! isset( $this->license_managers[ $extension['slug'] ] ) ) {
					$error = __( 'Unsupported extension: ', 'gfeloqua' ) . $extension['slug'];
				} else {
					if ( $this->license_managers[ $extension['slug'] ]->activate( $extension_license_key ) ) {
						$this->save_license( $extension_license_key, $extension );
						$license_stored = true;
					} else {
						$error = __( 'Invalid license key for this extension.', 'gfeloqua' );
					}
				}
			} else {
				$error = __( 'License Key is required for this extension.', 'gfeloqua' );
			}
		} elseif ( $license ) {
			if ( $this->license_managers[ $extension['slug'] ]->is_active_license( $license ) ) {
				$license_stored = true;
			} else {
				$error = __( 'Invalid license key for this extension.', 'gfeloqua' );
			}
		}

		?>
		<?php if ( $error ) : ?>
			<div class="error"><?php echo esc_html( $error ); ?></div>
		<?php endif; ?>

		<div class="setting-row">
			<label>
				<?php esc_html_e( 'Extension License Key', 'gfeloqua' ); ?>
				<span class="gfield_required">*</span>
			</label>
			<?php if ( $license_stored ) : ?>
				<span class="gfeloqua-stored-license"><?php _e( 'License saved!', 'gfeloqua' ); ?></span>
			<?php else : ?>
				<input type="text" name="<?php echo esc_attr( GFELOQUA_OPT_PREFIX ); ?>extension_license_key" class="regular-text" value="<?php echo esc_attr( $license ); ?>" tabindex="8000"/>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Checks if extension license is stored and valid
	 *
	 * $extension array must have:
	 *   'plugin' => Plugin full slug (folder/file.php)
	 *   'slug' => Slug of plugin (folder)
	 *   'sku' => Corresponding SKU associated with plugin/license
	 *
	 * @param array $extension  Extension Array.
	 *
	 * @return bool  If valid license.
	 */
	public function has_valid_license( $extension ) {
		$license = $this->get_license( $extension );
		if ( ! $license ) {
			return false;
		}

		if ( ! isset( $extension['plugin'] ) || ! isset( $extension['slug'] ) || ! isset( $extension['sku'] ) ) {
			return false;
		}

		if ( ! isset( $this->license_managers[ $extension['slug'] ] ) ) {
			$this->init_extension( $extension );
		}

		if ( $this->license_managers[ $extension['slug'] ]->is_active_license() ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize Extension
	 *
	 * @param array $extension  Extension array.
	 */
	public function init_extension( $extension ) {
		if ( ! isset( $extension['plugin'] ) || ! isset( $extension['slug'] ) || ! isset( $extension['sku'] ) ) {
			return false;
		}

		$license = $this->get_license( $extension );

		if ( ! isset( $this->license_managers[ $extension['slug'] ] ) ) {
			$this->license_managers[ $extension['slug'] ] = new BD_License_Manager( $license, array(
				'path' => trailingslashit( WP_PLUGIN_DIR ) . trailingslashit( $extension['slug'] ),
				'slug' => $extension['slug'],
				'filename' => basename( $extension['plugin'] ),
				'sku' => $extension['sku'],
			) );
		}

		if ( ! $license ) {
			return;
		}

		add_action( 'admin_init', array( $this->license_managers[ $extension['slug'] ], 'update' ) );
	}

} // end GFEloqua_Extensions.
