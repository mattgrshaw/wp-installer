<?php
/**
 * Script Name: WP CLI Installer
 * Description: Simple commands to install and uninstall WordPress.
 * License: GPLv3
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'installer', 'WP_CLI_Installer', array( 'when' => 'before_wp_load' ) );
}

/**
 * Install, reset, and delete WordPress installations.
 *
 * Usages:
 *
 * wp installer install
 * wp installer uninstall
 * wp installer reset
 *
 */
class WP_CLI_Installer {

	/**
	 * Install WordPress Core
	 *
	 * ## OPTIONS
	 *
	 * <dest>
	 * : The destination for the new WordPress install.
	 *
	 * [--base_path=<path>]
	 * : Optional path to the installation.
	 *
	 * [--base_url=<url>]
	 * : Base URL that sites will be subdirectories of
	 *
	 * [--multisite]
	 * : Convert the install to a Multisite installation.
	 *
	 * [--dbuser=<user>]
	 * : Database username
	 *
	 * [--dbpass=<pass>]
	 * : Database password
	 *
	 * [--dbhost=<host>]
	 * : Database host
	 *
	 * [--admin_user]
	 * : Admin username
	 *
	 * [--admin_password]
	 * : Admin password
	 *
	 * [--admin_email]
	 * : Admin email
	 *
	 * [--after_script]
	 * : Custom script to run after install
	 */
	public function install( $args, $assoc_args ) {
		$base_path = isset( $assoc_args['base_path'] ) ? $assoc_args['base_path'] : getcwd();
		$site_path = $base_path . '/' . $args[0];
		$dbuser    = $assoc_args['dbuser'];
		$dbpass    = $assoc_args['dbpass'];
		$dbhost    = $assoc_args['dbhost'];
		$dbname    = str_replace( '.', '_', $args[0] );

		// Download WordPress
		$download = "wp core download --path=%s";
		WP_CLI::log( 'Downloading WordPress...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $download, $site_path ) );

 		// Create the wp-config file
 		$config = "wp --path=%s core config --dbname=%s --dbuser=%s --dbpass=%s --dbhost=%s";
 		WP_CLI::log( 'Creating wp-config.php...' );
    	WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $config, $site_path, $dbname, $dbuser, $dbpass, $dbhost ) );

		// Create the database
		$db_create = "wp --path=%s db create";
		WP_CLI::log( 'Creating the database...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $db_create, $site_path ) );

		// Install WordPress core.
		$admin_user  = $assoc_args['admin_user'];
		$admin_pass  = $assoc_args['admin_password'];
		$admin_email = $assoc_args['admin_email'];
		$subcommand  = 'install';
		$base_url    = $assoc_args['base_url'];

		if ( isset( $assoc_args['multisite'] ) ) {
			$subcommand = 'multisite-install';
		}

		$core_install = "wp --path=%s core %s --url=%s --title=%s --admin_user=%s --admin_password=%s --admin_email=%s";
		WP_CLI::log( 'Installing WordPress...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $core_install, $site_path, $subcommand, 'http://' . $args[0], $args[0], $admin_user, $admin_pass, $admin_email ) );

		if ( isset( $assoc_args['after_script'] ) ) {
			WP_CLI::launch( $assoc_args['after_script'] . ' ' . $args[0] . '&>/dev/null' );
		}

		WP_CLI::success( "WordPress installed at $site_path" );
	}

	/**
	 * Uninstall the given WordPress install.
	 *
	 * ## OPTIONS
	 *
	 * <dest>
	 * : The site that should be uninstalled.
	 *
	 * [--base_path=<path>]
	 * : Base path that all sites are installed in
	 *
	 * [--after-script]
	 * : A custom script to run after the uninstall.
	 */
	public function uninstall( $args, $assoc_args ) {
		$base_path = isset( $assoc_args['base_path'] ) ? $assoc_args['base_path'] : getcwd();
		$site_path = $base_path . '/' . $args[0];

		// Let's make sure we really want to do this
		if ( ! isset( $assoc_args['yes'] ) ) {
			WP_CLI::confirm( 'Are you sure you want to proceed? Data WILL be lost!', $assoc_args );
		}

		// Drop the database
		$db_drop = "wp --path=%s db drop --yes";
		WP_CLI::log( 'Dropping database...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $db_drop, $site_path ) );

		// Remove the files
		$remove_files = "rm -rf %s";
		WP_CLI::log( 'Removing files...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $remove_files, $site_path ) );

		if ( isset( $assoc_args['after-script'] ) ) {
			WP_CLI::launch( $assoc_args['after-script'] . ' ' . $args[0] . '&>/dev/null' );
		}

		WP_CLI::success( "Uninstalled WordPress from $site_path" );
	}

	/**
	 * Cleanup any unnecessary data after install.
	 *
	 * ## OPTIONS
	 *
	 * <dest>
	 * : The site to clean up
	 *
	 * [--after_script]
	 * : A custom script to run after the cleanup.
	 */
	public function cleanup( $args, $assoc_args ) {
		$base_path = isset( $assoc_args['base_path'] ) ? $assoc_args['base_path'] : getcwd();
		$site_path = $base_path . '/' . $args[0];

		WP_CLI::log( 'Removing extra themes...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s theme delete twentyfifteen', $site_path ) );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s theme delete twentysixteen', $site_path ) );

		WP_CLI::log( 'Removing default plugins...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s plugin delete hello', $site_path ) );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s plugin delete akismet', $site_path ) );

		WP_CLI::log( 'Removing sample data...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s db query "TRUNCATE TABLE wp_posts; TRUNCATE TABLE wp_postmeta; TRUNCATE TABLE wp_comments; TRUNCATE TABLE wp_commentmeta;"', $site_path ) );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( 'wp --path=%s user meta update 1 show_welcome_panel "0"', $site_path ) );

		if ( isset( $assoc_args['after_script'] ) ) {
			WP_CLI::launch( $assoc_args['after_script'] . ' ' . $args[0] . '&>/dev/null' );
		}
	}

	/**
	 * Add plugins from the provided file.
	 *
	 * ## OPTIONS
	 *
	 * <dest>
	 * : The site to add plugins to.
	 *
	 * [--plugin_list]
	 * : The path to the file containing the list of plugins to install.
	 */
	public function add_plugins( $args, $assoc_args ) {
		$base_path = isset( $assoc_args['base_path'] ) ? $assoc_args['base_path'] : getcwd();

		if ( isset( $assoc_args['plugin_list'] ) && file_exists( $assoc_args['plugin_list'] ) ) {
			$plugins = file_get_contents( $assoc_args['plugin_list'] );
			$plugins = array_filter( explode( PHP_EOL, $plugins ) );

			foreach ( $plugins as $plugin ) {
				$cmd = 'wp --path=%s plugin install %s';
				$cmd = \WP_CLI\Utils\esc_cmd( $cmd, $base_path . '/' . $args[0], $plugin );
				$result = WP_CLI::launch( $cmd, false, true );
				WP_CLI::log( $result );

			}
		} else {
			WP_CLI::log( 'Plugin list not found' );
		}
	}

}
