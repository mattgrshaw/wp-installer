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
	 * [--site_base_path=<path>]
	 * : Optional path to the installation.
	 *
	 * [--site_base_url=<url>]
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
	 */
	public function install( $args, $assoc_args ) {
		$path   = $assoc_args['site_base_path'] . $args[0];
		$dbuser = $assoc_args['dbuser'];
		$dbpass = $assoc_args['dbpass'];
		$dbhost = $assoc_args['dbhost'];

		// Download WordPress
		$download = "wp core download --path=%s";
		WP_CLI::log( 'Downloading WordPress...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $download, $path ) );

 		// Create the wp-config file
 		$config = "wp --path=%s core config --dbname=%s --dbuser=%s --dbpass=%s --dbhost=%s";
 		WP_CLI::log( 'Creating wp-config.php...' );
    	WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $config, $path, $args[0], $dbuser, $dbpass, $dbhost ) );

		// Create the database
		$db_create = "wp --path=%s db create";
		WP_CLI::log( 'Creating the database...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $db_create, $path ) );

		// Install WordPress core.
		$admin_user  = $assoc_args['admin_user'];
		$admin_pass  = $assoc_args['admin_password'];
		$admin_email = $assoc_args['admin_email'];
		$subcommand  = 'install';
		$base_url    = $assoc_args['site_base_url'];

		if ( isset( $assoc_args['multisite'] ) ) {
			$subcommand = 'multisite-install';
		}

		$core_install = "wp --path=%s core %s --url=%s --title=%s --admin_user=%s --admin_password=%s --admin_email=%s";
		WP_CLI::log( 'Installing WordPress...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $core_install, $path, $subcommand, $base_url . $args[0], $args[0], $admin_user, $admin_pass, $admin_email ) );

		WP_CLI::success( "WordPress installed at $path" );
	}

	/**
	 * Uninstall the given WordPress install.
	 *
	 * ## OPTIONS
	 *
	 * <dest>
	 * : The site that should be uninstalled.
	 */
	public function uninstall( $args, $assoc_args ) {
		$path = $assoc_args['site_base_path'] . $args[0];

		// Let's make sure we really want to do this
		WP_CLI::confirm( 'Are you sure you want to proceed? Data WILL be lost!', $assoc_args );

		// Drop the database
		$db_drop = "wp --path=%s db drop --yes";
		WP_CLI::log( 'Dropping database...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $db_drop, $path ) );

		// Remove the files
		$remove_files = "rm -rf %s";
		WP_CLI::log( 'Removing files...' );
		WP_CLI::launch( \WP_CLI\Utils\esc_cmd( $remove_files, $path ) );

		WP_CLI::success( "Uninstalled WordPress from $path" );
	}

}
