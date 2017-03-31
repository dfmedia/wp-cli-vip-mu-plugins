<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

class DFM_VIP_MU_Update extends WP_CLI_Command {

	/**
	 * Updates the VIP MU plugins
	 *
	 * ## OPTIONS
	 * [--remove-cron-control]
	 * : Whether or not to remove the cron control plugin
	 * default: true
	 * options:
	 * - true
	 * - false
	 *
	 * ## EXAMPLES
	 *
	 * 		$wp plugin vip-mu-update
	 * 		# Clones the VIP MU plugin repo and removes cron control
	 *
	 * 		$wp plugin vip-mu-update --remove-cron-control=false
	 * 		# Clones the VIP MU plugin repo but does not remove cron control
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update_mu_plugins( $args, $assoc_args ) {

		$remove_cron_control = ( isset( $assoc_args['remove-cron-control'] ) ) ? $assoc_args['remove-cron-control'] : true;

		$fs = $this->init_wp_filesystem();

		if ( is_dir( WPMU_PLUGIN_DIR ) ) {
			WP_CLI::confirm( 'MU Directory already exists. Continuing will delete this directory completely, would you like to continue?' );
			$result = $fs->rmdir( WPMU_PLUGIN_DIR, true );
			if ( $result !== true ) {
				WP_CLI::error( 'Unfortunately we couldn\'t remove the old mu-plugins directory, so we can\'t continut. This is probably a filesystem issue' );
			}
		}

		WP_CLI::success( 'Cloning the VIP MU Plugins git repo...' );

		exec( 'git clone --recursive git@github.com:Automattic/vip-go-mu-plugins.git ' . WPMU_PLUGIN_DIR );

		if ( true == $remove_cron_control ) {
			WP_CLI::success( 'Removing cron control...' );
			$fs->rmdir( WPMU_PLUGIN_DIR . '/wp-cron-control', true );
			$fs->delete( WPMU_PLUGIN_DIR . '/001-cron.php' );
		}

		WP_CLI::success( 'All done here!' );

	}

	/**
	 * Initialize WP Filesystem
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}

}

WP_CLI::add_command( 'plugin vip-mu-update', [ 'DFM_VIP_MU_Update', 'update_mu_plugins' ] );
