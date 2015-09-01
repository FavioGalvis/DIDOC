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
 * Layout API
 *
 * UI functions to render layout elements in every page. The layout api layer sits above the html api and abstract
 * the lower level html markup into web components
 *
 * Here is the call order for the layout functions
 *
 * layout_page_header
 *      layout_page_header_begin
 *      layout_page_header_end
 * layout_page_begin
 *      ...Page content here...
 * layout_page_end
 *
 *
 *
 * @package APIS
 * @subpackage LayoutAPI
 * @copyright Copyright 2015 Favio Galvis - fgalvis@infortributos.com
 * @copyright Copyright 2015  DIDOC - rgonzalez@infortributos.com
 * @link https://github.com/FavioGalvis/DIDOC
 *
 * @uses TODO
 */

/**
 * Print meta tags for the page head
 * @return null
 */
function layout_head_meta() {
	# use the following meta to force IE use its most up to date rendering engine
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' . "\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />' . "\n";
}

/**
 * Print opening markup for login/signup/register pages
 * @return null
 */
function layout_login_page_begin() {
	html_begin();
	html_head_begin();
	html_content_type();

	global $g_robots_meta;
	if( !is_blank( $g_robots_meta ) ) {
		echo "\t", '<meta name="robots" content="', $g_robots_meta, '" />', "\n";
	}
        
	html_title();
        layout_head_meta();
        
        
        
        
}

