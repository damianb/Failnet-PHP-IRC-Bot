<?php
/**
 *
 *===================================================================
 *
 *  Failnet -- PHP-based IRC Bot
 *-------------------------------------------------------------------
 *	Script info:
 * Version:		2.0.0
 * SVN ID:		$Id$
 * Copyright:	(c) 2009 - Obsidian
 * License:		http://opensource.org/licenses/gpl-2.0.php  |  GNU Public License v2
 *
 *===================================================================
 *
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://opensource.org/licenses/gpl-2.0.php>.
 */

/**
 * @ignore
 */
if(!defined('IN_FAILNET')) return;


/**
 * Failnet - Class autoloader
 * 
 * 
 * @author Obsidian
 * @copyright (c) 2009 - Obsidian
 * @license http://opensource.org/licenses/gpl-2.0.php | GNU Public License v2
 */
class failnet_autoload
{
	/**
	 * Constructor to add the base path to the include_path.
	 */
	public function __construct()
	{
		$path = dirname(__FILE__);
		$includePath = get_include_path();
		$includePathList = explode(PATH_SEPARATOR, $includePath); 
		if (!in_array($path, $includePathList))
			set_include_path($includePath . PATH_SEPARATOR . $path);
	}

	/**
	 * Autoload callback for loading class files.
	 *
	 * @param string $class Class to load
	 * @return void
	 */
	public function load($class)
	{
		$class = substr(strstr($class, '_'), 1));
		include 'includes' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.' . PHP_EXT;
	}

	/**
	 * Registers an instance of this class as an autoloader.
	 *
	 * @return void
	 */
	public static function register()
	{
		spl_autoload_register(array(new self, 'load'));
	}
}