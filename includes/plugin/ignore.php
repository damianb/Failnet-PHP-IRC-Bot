<?php
/**
 *
 *===================================================================
 *
 *  Failnet -- PHP-based IRC Bot
 *-------------------------------------------------------------------
 *	Script info:
 * Version:		2.0.0 Alpha 1
 * Copyright:	(c) 2009 - Failnet Project
 * License:		GNU General Public License - Version 2
 *
 *===================================================================
 * 
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
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
 * Failnet - Ignore handling plugin,
 * 		Used as the shell for Failnet's user ignore system. 
 * 
 *
 * @package plugins
 * @author Obsidian
 * @copyright (c) 2009 - Failnet Project
 * @license GNU General Public License - Version 2
 */
class failnet_plugin_ignore extends failnet_plugin_common
{
	public function help(&$name, &$commands)
	{
		$name = 'admin';
		$commands = array(
			'ignore'			=> 'ignore {$hostmask} - (authlevel ADMIN) - Orders Failnet to ignore messages from the specified hostmask',
			'unignore'			=> 'unignore {$hostmask} - (authlevel ADMIN) - Orders Failnet to no longer ignore messages from the specified hostmask',
		);
	}

	public function cmd_privmsg()
	{
		// Process the command
		$text = $this->event->get_arg('text');
		if(!$this->prefix($text))
			return;

		$cmd = $this->purify($text);
		$sender = $this->event->hostmask->nick;
		$hostmask = $this->event->hostmask;
		switch ($cmd)
		{
			case 'addignore':
			case 'ignore':
			case '+ignore':
				// Check auths
				if($this->failnet->authorize->authlevel($hostmask) < 10)
				{
					$this->call_privmsg($this->event->source(), $this->failnet->deny());
					return;
				}

				// Check for no params
				if(empty($text))
				{
					$this->call_privmsg($sender, 'Invalid arguments specified for command');
					return;
				}

				$success = $this->failnet->ignore->add_ignore($hostmask, $text);

				$this->call_privmsg($sender, ($success) ? 'User successfully ignored' : 'Unable to ignore user -- user hostmask already ignored');
			break;

			case 'delignore':
			case 'unignore':
			case '-ignore':
				if($this->failnet->authorize->authlevel($hostmask) < 10)
				{
					$this->call_privmsg($this->event->source(), $this->failnet->deny());
					return;
				}

				// Check for no params
				if(empty($text))
				{
					$this->call_privmsg($sender, 'Invalid arguments specified for command');
					return;
				}

				$success = $this->failnet->ignore->del_ignore($hostmask, $text);

				$this->call_privmsg($sender, ($success) ? 'User successfully unignored' : 'Unable to ignore user -- user hostmask not ignored');
			break;
		}
	}
}

?>