<?php
/* 
 * Copyright (C) 2015 FGALVIS
 * Copyright (C) 2015 INFORMATICA Y TRIBUTOS
 *
 * DIDOC software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * DIDOC software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
/**
 * Didoc Core
 *
 * Initialises the Didoc core, connects to the database, starts plugins and
 * performs other global operations that either help initialise Didoc or
 * are required to be executed on every page load.
 *
 * @package Didoc
 * @copyright Copyright 2015 Favio Galvis - fgalvis@infortributos.com
 * @copyright Copyright 2015  DIDOC - rgonzalez@infortributos.com
 * @link https://github.com/FavioGalvis/DIDOC
 *
 * @uses TODO
 */

# Checks UNIX timestamp to mesure page execution time.
$g_request_time = microtime( true );

ob_start();

# Load supplied constants
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'constant_inc.php' );

# Include default configuration settings
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config_defaults_inc.php' );

# Load user-defined constants (if required)
if( file_exists( $g_config_path . 'custom_constants_inc.php' ) ) {
	require_once( $g_config_path . 'custom_constants_inc.php' );
}

# config_inc may not be present if this is a new install
$t_config_inc_found = file_exists( $g_config_path . 'config_inc.php' );

if( $t_config_inc_found ) {
	require_once( $g_config_path . 'config_inc.php' );
}

/**
 * Define an API inclusion function to replace require_once
 *
 * @param string $p_api_name An API file name.
 * @return void
 */
function require_api( $p_api_name ) {
	static $s_api_included;
	global $g_core_path;
	if( !isset( $s_api_included[$p_api_name] ) ) {
		require_once( $g_core_path . $p_api_name );
		$t_new_globals = array_diff_key( get_defined_vars(), $GLOBALS, array( 't_new_globals' => 0 ) );
		foreach ( $t_new_globals as $t_global_name => $t_global_value ) {
			$GLOBALS[$t_global_name] = $t_global_value;
		}
		$s_api_included[$p_api_name] = 1;
	}
}

/**
 * Define an LIB inclusion function to replace require_once
 *
 * @param string $p_library_name A library file name.
 * @return void
 */
function require_lib( $p_library_name ) {
	static $s_libraries_included;
	global $g_library_path;
	if( !isset( $s_libraries_included[$p_library_name] ) ) {
		$t_library_file_path = $g_library_path . $p_library_name;
		if( !file_exists( $t_library_file_path ) ) {
			echo 'External library \'' . $t_library_file_path . '\' not found.';
			exit;
		}

		require_once( $t_library_file_path );
		$t_new_globals = array_diff_key( get_defined_vars(), $GLOBALS, array( 't_new_globals' => 0 ) );
		foreach ( $t_new_globals as $t_global_name => $t_global_value ) {
			$GLOBALS[$t_global_name] = $t_global_value;
		}
		$s_libraries_included[$p_library_name] = 1;
	}
}

/**
 * Define an autoload function to automatically load classes when referenced
 *
 * @param string $p_class Class name being autoloaded.
 * @return void
 */
function __autoload( $p_class ) {
	global $g_class_path;
	global $g_library_path;

	$t_require_path = $g_class_path . $p_class . '.class.php';

	if( file_exists( $t_require_path ) ) {
		require_once( $t_require_path );
		return;
	}

	$t_require_path = $g_library_path . 'rssbuilder' . DIRECTORY_SEPARATOR . 'class.' . $p_class . '.inc.php';

	if( file_exists( $t_require_path ) ) {
		require_once( $t_require_path );
		return;
	}
}

# Register the autoload function to make it effective immediately
spl_autoload_register( '__autoload' );

# Load UTF8-capable string functions
define( 'UTF8', $g_library_path . 'utf8' );
require_lib( 'utf8/utf8.php' );
require_lib( 'utf8/str_pad.php' );

# Include PHP compatibility file
require_api( 'php_api.php' );

# Enforce our minimum PHP requirements
if( !php_version_at_least( PHP_MIN_VERSION ) ) {
	@ob_end_clean();
	echo '<strong>FATAL ERROR: Your version of PHP is too old. DIDOC requires PHP version ' . PHP_MIN_VERSION . ' or newer</strong><br />Your version of PHP is version ' . phpversion();
	die();
}

# Ensure that output is blank so far (output at this stage generally denotes
# that an error has occurred)
if( ( $t_output = ob_get_contents() ) != '' ) {
	echo 'Possible Whitespace/Error in Configuration File - Aborting. Output so far follows:<br />';
	var_dump( $t_output );
	die;
}
unset( $t_output );

# If no configuration file exists, redirect the user to the admin page so
# they can complete installation and configuration of Didoc
if( false === $t_config_inc_found ) {
	if( php_sapi_name() == 'cli' ) {
		echo 'Error: ' . $g_config_path . "config_inc.php file not found; ensure DIDOC is properly setup.\n";
		exit(1);
	}

	/*if( !( isset( $_SERVER['SCRIPT_NAME'] ) && ( 0 < strpos( $_SERVER['SCRIPT_NAME'], 'admin' ) ) ) ) {
		header( 'Content-Type: text/html' );
		# Temporary redirect (307) instead of Found (302) default
		header( 'Location: ../install/install.php', true, 307 );
		# Make sure it's not cached
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		exit;
	}*/
}