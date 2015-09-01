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
 * Utility API
 *
 * Utility functions are *small* functions that are used often and therefore
 * have *no* prefix, to keep their names short.
 *
 * Utility functions have *no* dependencies on any other APIs, since they are
 * included first in order to make them available to all the APIs.
 * Miscellaneous functions that provide functionality on top of other APIS
 * are found in the helper_api.
 *
 * @package APIS
 * @copyright Copyright 2015 Favio Galvis - fgalvis@infortributos.com
 * @copyright Copyright 2015  DIDOC - rgonzalez@infortributos.com
 * @link https://github.com/FavioGalvis/DIDOC
 *
 * @uses TODO
 */

/**
 * Get the named php ini variable but return it as a boolean
 * @param string $p_name A php.ini variable to evaluate.
 * @return boolean
 * @access public
 */
function ini_get_bool( $p_name ) {
	$t_result = ini_get( $p_name );

	if( is_string( $t_result ) ) {
		switch( strtolower( $t_result ) ) {
			case 'off':
			case 'false':
			case 'no':
			case 'none':
			case '':
			case '0':
				return false;
				break;
			case 'on':
			case 'true':
			case 'yes':
			case '1':
				return true;
				break;
		}
	}
	return (bool)$t_result;
}