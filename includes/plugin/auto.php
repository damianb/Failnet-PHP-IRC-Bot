<?php
/**
 *
 *===================================================================
 *
 *  Failnet -- PHP-based IRC bot
 *-------------------------------------------------------------------
 *	Script info:
 * Version:		2.0.1
 * Copyright:	(c) 2009 - 2010 -- Failnet Project
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
 * Failnet - Automatic action plugin,
 * 		Performs autojoin on end-of-MOTD, autorejoin on kick, and join on invite capabilities.
 *
 *
 * @package plugins
 * @author Obsidian
 * @copyright (c) 2009 - 2010 -- Failnet Project
 * @license GNU General Public License - Version 2
 */
class failnet_plugin_auto extends failnet_plugin_common
{
	public function cmd_response()
	{
		switch ($this->event->code)
		{
			case failnet_event_response::RPL_ENDOFMOTD:
			case failnet_event_response::ERR_NOMOTD:
				$channels = $this->failnet->config('autojoins');
				if (!empty($channels))
				{
					foreach($channels as $channel)
					{
						$this->call_join($channel);
					}
				}
		}
	}

	public function cmd_kick()
	{
		// Make sure it was Failnet that was kicked.
		if($this->event->get_arg('user') == $this->failnet->config('nick'))
		{
			// Are we supposed to automatically rejoin on kick?
			if(!$this->failnet->config('autorejoin'))
				return;

			// Guess we are.  Let's setup for that.
			$this->call_join(trim(strtolower($this->event->get_arg('channel'))));
		}
	}

	public function cmd_invite()
	{
		// Check to see if it was us that is being invited to the channel.
		if($this->event->get_arg('user') == $this->failnet->config('nick'))
		{
			// Are we supposed to automatically join on invite?
			if(!$this->failnet->config('join_on_invite'))
				return;

			// Guess we are.  Let's do that then.
			$this->call_join(trim(strtolower($this->event->get_arg('channel'))));
		}
	}
}
