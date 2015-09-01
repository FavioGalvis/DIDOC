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
 * HTML API
 *
 * These functions control the HTML output of each page.
 *
 * @package APIS
 * @subpackage HTMLAPI
 * @copyright Copyright 2015 Favio Galvis - fgalvis@infortributos.com
 * @copyright Copyright 2015  DIDOC - rgonzalez@infortributos.com
 * @link https://github.com/FavioGalvis/DIDOC
 *
 * @uses TODO
 */

/**
 * This method must be called before the html_page_top* methods.  It marks the page as not
 * for indexing.
 * @return void
 */
function html_robots_noindex() {
	global $g_robots_meta;
	$g_robots_meta = 'noindex,follow';
}

/**
 * Print the document type and the opening <html> tag
 * @return void
 */
function html_begin() {
	echo '<!DOCTYPE html>', "\n";
	echo '<html>', "\n";
}

/**
 * Begin the <head> section
 * @return void
 */
function html_head_begin() {
	echo '<head>', "\n";
}

/**
 * Print the content-type
 * @return void
 */
function html_content_type() {
	echo "\t", '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />', "\n";
}

/**
 * Print the window title
 * @param string $p_page_title Window title.
 * @return void
 */
function html_title( $p_page_title = null ) {
	$t_page_title = string_html_specialchars( $p_page_title );
	$t_title = string_html_specialchars( 'window_title' );
	echo "\t", '<title>';
	if( empty( $t_page_title ) ) {
		echo $t_title;
	} else {
		if( empty( $t_title ) ) {
			echo $t_page_title;
		} else {
			echo $t_page_title . ' - ' . $t_title;
		}
	}
	echo '</title>', "\n";
}

/**
 * Prints a CSS link
 * @param string $p_filename Filename.
 * @return void
 */
function html_css_link( $p_filename ) {
	echo "\t", '<link rel="stylesheet" type="text/css" href="', string_sanitize_url( helper_didoc_url( 'css/' . $p_filename ), true ), '" />' . "\n";
}

/**
 * Print the link to include the CSS file
 * @return void
 */
function html_css() {
	global $g_stylesheets_included;
	html_css_link( config_get( 'css_include_file' ) );
	html_css_link( 'jquery-ui.min.css' );
	html_css_link( 'common_config.php' );
	# Add right-to-left css if needed
	if( lang_get( 'directionality' ) == 'rtl' ) {
		html_css_link( config_get( 'css_rtl_include_file' ) );
	}
	foreach( $g_stylesheets_included as $t_stylesheet_path ) {
		html_css_link( $t_stylesheet_path );
	}
}