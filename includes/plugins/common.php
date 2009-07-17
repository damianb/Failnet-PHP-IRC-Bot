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
if(!defined('IN_FAILNET')) exit(1);

/**
 * Failnet - Plugin base class,
 * 		Used as the common base class for all of Failnet's plugin class files 
 * 
 * 
 * @author Obsidian
 * @copyright (c) 2009 - Obsidian
 * @license http://opensource.org/licenses/gpl-2.0.php | GNU Public License v2
 */
abstract class failnet_plugin_common
{
	private $failnet;
	
	/**
	 * Constants for Failnet.
	 */
	const TAB = "\t";
	const HR = '---------------------------------------------------------------------';
	const ERROR_LOG = 'error';
	const USER_LOG = 'user';
	
	public function __construct(failnet_core $failnet)
	{
		$this->failnet = &$failnet;
		$this->init();
	}
	
	public function init() { }

	/**
	* Current event instance being processed
	*
	* @var object
	*/
	public $event;

	/**
	* Plugin loader used to provide access to other plugins
	*
	* @var failnet_plugin_loader
	*/
	public $plugin;

	/**
	* Queue of events initiated by the plugin in response to the current
	* event being processed
	*
	* @var array
	*/
	public $events = array();

	/**
	* Check if the dependencies for the plugin are met.
	* 
	* @return string
	*/
	public function dependencies()
	{
		return '';
	}

	/**
	* Callback dispatched before connections are checked for new events, 
	* allowing for the execution of logic that does not require an event 
	* to occur.
	*
	* @return void
	*/
	public function tick() { }

	/**
	* Callback dispatched right before commands are to be dispatched to the
	* server, allowing plugins to mutate, remove, or reorder events.
	*
	* @param array $events Events to be dispatched
	* @return void
	*/
	public function pre_dispatch(array &$events) { }

	/**
	* Callback dispatched right after commands are dispatched to the server,
	* informing plugins of what events were sent in and in what order.
	*
	* @param array $events Events that were dispatched
	* @return void
	*/
	public function post_dispatch(array $events) { }

	/**
	* Callback dispatched before a handler is called for the current event
	* based on its type.
	*
	* @return failnet_plugin_common
	*/
	public function pre_event()
	{
		return $this;
	}

	/**
	* Callback dispatched after a handle is called for the current event 
	* based on its type.
	*
	* @return failnet_plugin_common
	*/
	public function post_event()
	{
		return $this;
	}

	/**
	* Handler for when the bot connects to the current server.
	*
	* @return void
	*/
	public function cmd_connect() { }

	/**
	* Handler for when the bot disconnects from the current server.
	*
	* @return void
	*/
	public function cmd_disconnect() { }

	/**
	* Handler for when the client session is about to be terminated.
	*
	* @return void
	*/
	public function cmd_quit() { }

	/**
	* Handler for when a user joins a channel.
	*
	* @return void
	*/
	public function cmd_join() { }

	/**
	* Handler for when a user leaves a channel.
	*
	* @return void
	*/
	public function cmd_part() { }

	/**
	* Handler for when a user sends an invite request.
	*
	* @return void
	*/
	public function cmd_invite() { }

	/**
	* Handler for when a user obtains operator privileges.
	*
	* @return void
	*/
	public function cmd_oper() { }

	/**
	* Handler for when a channel topic is viewed or changed.
	*
	* @return void
	*/
	public function cmd_topic() { }

	/**
	* Handler for when a user or channel mode is changed.
	*
	* @return void
	*/
	public function cmd_mode() { }

	/**
	* Handler for when the server prompts the client for a nick.
	*
	* @return void
	*/
	public function cmd_nick() { }

	/**
	* Handler for when a message is received from a channel or user.
	*
	* @return void
	*/
	public function cmd_privmsg() { }

	/**
	* Handler for when an action is received from a channel or user
	*
	* @return void
	*/
	public function cmd_action() { }

	/**
	* Handler for when a notice is received.
	*
	* @return void
	*/
	public function cmd_notice() { }

	/**
	* Handler for when a user is kicked from a channel.
	*
	* @return void
	*/
	public function cmd_kick() { }

	/**
	* Handler for when the server or a user checks the client connection to
	* ensure activity.
	*
	* @return void
	*/
	public function cmd_ping() { }

	/**
	* Handler for when the server sends a CTCP TIME request.
	*
	* @return void
	*/
	public function cmd_time() { }

	/**
	* Handler for when the server sends a CTCP VERSION request.
	*
	* @return void
	*/
	public function cmd_version() { }

	/**
	* Handler for the reply to a CTCP PING request.
	*
	* @return void
	*/
	public function cmd_pingreply() { }

	/**
	* Handler for the reply to a CTCP TIME request.
	*
	* @return void
	*/
	public function cmd_timereply() { }

	/**
	* Handler for the reply to a CTCP VERSION request. 
	*
	* @return void
	*/
	public function cmd_versionreply() { }

	/**
	* Handler for unrecognized CTCP requests.
	*
	* @return void
	*/
	public function cmd_ctcp() { }

	/**
	* Handler for unrecognized CTCP responses.
	*
	* @return void
	*/
	public function cmd_ctcpreply() { }

	/**
	* Handler for raw requests from the server.
	*
	* @return void
	*/
	public function cmd_raw() { }

	/**
	* Handler for when the server sends a kill request.
	*
	* @return void
	*/
	public function cmd_kill() { }

	/**
	* Handler for when a server response is received to a client-issued
	* command.
	*
	* @return void
	*/
	public function cmd_response() { }
	
	/**
	* Provides cmd_* methods
	*
	* @param string $name Name of the method called
	* @param array $args Arguments passed in the call
	* @return void
	*/
	public function __call($name, array $args)
	{
		if (substr($name, 0, 5) == 'call_')
		{
			$type = substr($name, 5);
			if (defined('failnet_event_command::TYPE_' . strtoupper($type)))
			{
				$request = new failnet_event_command();
				$request->plugin = $this;
				$request->type = $type;
				$request->arguments = $args;
				$this->events[] = $request;
			}
		}
	}
}

?>
}