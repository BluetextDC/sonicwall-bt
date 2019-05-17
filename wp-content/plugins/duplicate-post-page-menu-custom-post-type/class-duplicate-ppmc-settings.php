<?php

/* Check for wordpress installation */
if ( ! function_exists( 'add_action' ) ) {
	die( 'Wordpress installation not found!' );
}	/* End of wordpress installation check */


if ( ! class_exists( 'Duplicate_PPMC_Settings' ) ) {

	class Duplicate_PPMC_Settings{

		private static $initiated = false;

		public static function dppmc_Init(){
			if ( ! self::$initiated ) {
				self::dppmc_hooks();
			}
		}

		private static function dppmc_hooks(){
			add_action( 'admin_menu', array( 'Duplicate_PPMC_Settings', 'dppmc_plugin_menu' ) );
			add_action( 'admin_init', array( 'Duplicate_PPMC_Settings', 'dppmc_reg_settings' ) );
		}

		public static function dppmc_reg_settings(){
			register_setting(
				'dppmc-settings-options',
				'dppmc_post'
			);
			register_setting(
				'dppmc-settings-options',
				'dppmc_page'
			);
			register_setting(
				'dppmc-settings-options',
				'dppmc_menu'
			);

			/* Call a function to get all custom post */
			$post_types = self::dppmc_all_post();

			/* Loop through each available custom post */
			foreach ( $post_types as $post_type ) {
				$post_name = 'dppmc_'. $post_type->name;
				register_setting(
				'dppmc-settings-options',
				$post_name
				);

			} /* End of foreach */

		}

		public static function dppmc_plugin_menu(){
			add_options_page( 'Duplicate', __( 'Duplicate', 'duplicate-ppmc'), 'manage_options', 'dppmc-settings', array( 'Duplicate_PPMC_Settings', 'dppmc_settings_display' ) );
			add_settings_section( 'dppmc_settings_general', '', array( 'Duplicate_PPMC_Settings', 'dppmc_settings_general'), 'dppmc-settings' );
		}

		public static function dppmc_all_post(){
			$args = array(
					'_builtin' => false
					);

			$output = 'name'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'
			$post_types = get_post_types( $args, $output, $operator );
					
			return $post_types;
		}

		public static function dppmc_settings_general(){
		?>
			<tr><th scope="row"><?php _e( 'Elements to duplicate:', 'duplicate-ppmc' ); ?> </th>
				<td>
					<fieldset>
					<label for="dppmc_post"><input type="checkbox" name="dppmc_post" id="dppmc_post" value="0" <?php checked( '0', get_option( 'dppmc_post', '0' ) ); ?>" /><?php _e( 'Post', 'duplicate-ppmc' ); ?></label><br/>
					<label for="dppmc_page"><input type="checkbox" name="dppmc_page" id="dppmc_page" value="0" <?php checked( '0', get_option( 'dppmc_page', '0' ) ); ?>" /><?php _e( 'Page', 'duplicate-ppmc' ); ?></label><br/>
					<?php
					$post_types = self::dppmc_all_post();
					
						foreach ( $post_types as $post_type ) {
						$post_name = 'dppmc_'. $post_type->name;

					?>
					<label for="<?php echo $post_name; ?>"><input type="checkbox" name="<?php echo $post_name; ?>" id="<?php echo $post_name; ?>" value="0" <?php checked( '0', get_option($post_name, '0' ) ); ?>" /><?php _e( $post_type->label, 'duplicate-ppmc' ); ?></label><br/>
					
					<?php
						}
					?>
						<label for="dppmc_menu"><input type="checkbox" name="dppmc_menu" id="dppmc_menu" value="0" <?php checked( '0', get_option( 'dppmc_menu', '0' ) ); ?>" /><?php _e( 'Menu', 'duplicate-ppmc' ); ?></label><br/>
					</fieldset>
				</td>
			</tr>
		<?php
		}

		public static function dppmc_settings_display(){
		?>
			<div class='wrap'>
				<h1><?php _e( 'Duplicate Post, Page, Menu & Custom Post Type Options'); ?> </h1>
				<form method="post" action="options.php">
					<table class="form-table"><tbody>
						<?php
							settings_fields( 'dppmc-settings-options' );
							do_settings_sections( 'dppmc-settings' );
						?>

						<tr><td>
							<?php
							submit_button();
							?>
						</td></tr>
					</tbody></table>
				</form>
			</div>

			<?php
		}

	} /* End of Duplicate_PPMC_Settings{} */
	
} /* End of if-class_exists */
return Duplicate_PPMC_Settings::dppmc_Init();