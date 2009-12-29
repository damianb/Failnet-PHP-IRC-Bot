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
 * Failnet - Server communication plugin,
 * 		Used to track what channels Failnet is in, the users inhabiting them, along with various other default server interactions.
 * 
 *
 * @package plugins
 * @author Obsidian
 * @copyright (c) 2009 - Failnet Project
 * @license GNU General Public License - Version 2
 */
class failnet_plugin_server extends failnet_plugin_common
{
	const FOUNDER = 32;
	const ADMIN = 16;
	const OP = 8;
	const HALFOP = 4;
	const VOICE = 2;
	const REGULAR = 1;

	public function cmd_response()
	{
		switch($this->event->code)
		{
			case failnet_event_response::RPL_ENDOFNAMES:
				$chanargs = explode(' ', $this->event->description);

				// Only do the intro message if we're allowed to speak.
				if($this->failnet->speak)
					$this->call_privmsg($chanargs[0], $this->failnet->get('intro_msg'));
			break;
			
			case failnet_event_response::RPL_NAMREPLY:
				$desc = preg_split('/[@*=]\s*/', $this->event->description, 2);
				list($chan, $users) = array_pad(explode(' :', trim($desc[1])), 2, null);
				$users = explode(' ', trim($users));
				foreach($users as $user)
				{
					if (empty($user)) 
						continue;
		
					$flag = self::REGULAR;
					if (substr($user, 0, 1) === '~')
					{
						$user = substr($user, 1);
						$flag |= self::FOUNDER;
					}
					if (substr($user, 0, 1) === '&')
					{
						$user = substr($user, 1);
						$flag |= self::ADMIN;
					}
					if (substr($user, 0, 1) === '@')
					{
						$user = substr($user, 1);
						$flag |= self::OP;
					}
					if (substr($user, 0, 1) === '%')
					{
						$user = substr($user, 1);
						$flag |= self::HALFOP;
					}
					if (substr($user, 0, 1) === '+')
					{
						$user = substr($user, 1);
						$flag |= self::VOICE;
					}

					$chan = trim(strtolower($chan));
					$user = trim(strtolower($user));

					$this->failnet->chans[$chan][$user] = $flag;
				}
			break;
		}
	}
	
	/**
	 * Tracks mode changes.
	 *
	 * @return void
	 */
	public function cmd_mode()
	{
		if (count($this->event->arguments) != 3)
			return;
		
		$chan = $this->event->get_arg('target');
		$modes = $this->event->get_arg('mode');
		$nick = $this->event->get_arg(2);

		if (preg_match('/(?:\+|-)[qaohv+-]+/i', $modes))
		{
			$chan = trim(strtolower($chan));
			$modes = str_split(trim(strtolower($modes)), 1);
			$nick = trim(strtolower($nick));
			while ($char = array_shift($modes))
			{
				switch ($char)
				{
					case '+':
						$mode = '+';
					break;

					case '-':
						$mode = '-';
					break;

					case 'q':
						if ($mode == '+')
						{
							$this->failnet->chans[$chan][$chan] |= self::FOUNDER;
						}
						elseif ($mode == '-')
						{
							$this->failnet->chans[$chan][$chan] ^= self::FOUNDER;
						}
					break;

					case 'a':
						if ($mode == '+')
						{
							$this->failnet->chans[$chan][$nick] |= self::ADMIN;
						}
						elseif ($mode == '-')
						{
							$this->failnet->chans[$chan][$nick] ^= self::ADMIN;
						}
					break;

					case 'o':
						if ($mode == '+')
						{
							$this->failnet->chans[$chan][$nick] |= self::OP;
						}
						elseif ($mode == '-')
						{
							$this->failnet->chans[$chan][$nick] ^= self::OP;
						}
					break;

					case 'h':
						if ($mode == '+')
						{
							$this->failnet->chans[$chan][$nick] |= self::HALFOP;
						}
						elseif ($mode == '-')
						{
							$this->failnet->chans[$chan][$nick] ^= self::HALFOP;
						}
					break;

					case 'v':
						if ($mode == '+')
						{
							$this->failnet->chans[$chan][$nick] |= self::VOICE;
						}
						elseif ($mode == '-')
						{
							$this->failnet->chans[$chan][$nick] ^= self::VOICE;
						}
					break;
				}
			}
		}
	}

	
	
	public function cmd_kick()
	{
		if($this->event->hostmask->nick != $this->failnet->get('nick'))
		{
			$chan = trim(strtolower($this->event->get_arg('channel')));
			$nick = trim(strtolower($this->event->hostmask->nick));

			if (isset($this->failnet->chans[$chan][$nick]))
				unset($this->failnet->chans[$chan][$nick]);
		}
		else
		{
			foreach($this->failnet->chans as $key => $channel)
			{
				if($channel == $this->event->get_arg('channel'))
				{
					unset($this->failnet->chans[$key]);
					return;
				}
			}
		}
	}
	
	public function cmd_part()
	{
		if($this->event->get_arg('user') != $this->failnet->get('nick'))
		{
			$chan = trim(strtolower($this->event->get_arg('channel')));
			$nick = trim(strtolower($this->event->hostmask->nick));

			if (isset($this->failnet->chans[$chan][$nick]))
				unset($this->failnet->chans[$chan][$nick]);
		}
		else
		{
			foreach($this->failnet->chans as $key => $channel)
			{
				if($channel == $this->event->get_arg('channel'))
				{
					unset($this->failnet->chans[$key]);
					return;
				}
			}
		}
	}

	public function cmd_join()
	{
		$chan = trim(strtolower($this->event->get_arg('channel')));
		$nick = trim(strtolower($this->event->hostmask->nick));

		$this->failnet->chans[$chan][$nick] = self::REGULAR;
	}

	public function cmd_quit()
	{
		$chan = trim(strtolower($this->event->get_arg('channel')));
		$nick = trim(strtolower($this->event->hostmask->nick));

		foreach($this->failnet->chans as $channame => $chan)
		{
			if(isset($chan[$nick]))
				unset($this->failnet->chans[$channame][$nick]);
		}
	}

	public function cmd_nick()
	{
		$nick = trim(strtolower($this->event->hostmask->nick));
		$new_nick = trim(strtolower($this->event->get_arg('nick')));

		foreach($this->failnet->chans as $channame => $chan)
		{
			if(isset($chan[$nick]))
			{
				$data = $chan[$nick];
				unset($this->failnet->chans[$channame][$nick]);
				$this->failnet->chans[$channame][$new_nick] = $data;
			}
		}
	}

	public function cmd_privmsg()
	{
		// Process the command
		$text = $this->event->get_arg('text');
		if(!$this->prefix($text))
			return;

		$cmd = $this->purify($text);

		// Make sure this is one of the 'is' commands, otherwise we run into a bug.
		if(!in_array($cmd, array('isfounder', 'isadmin', 'isop', 'ishalfop', 'isvoice', 'isin')))
			return;
		
		$sender = $this->event->hostmask->nick;
		$hostmask = $this->event->hostmask;
		
		// Make sure we're asking this in channel, or that we have additional params for the channel.
		$param = explode(' ', $text);

		if(!$this->event->fromchannel() && !isset($param[1]))
		{
			$this->call_notice($sender, 'Please specify the channel name to check within.');
			return;
		}
		elseif($this->event->fromchannel() && !isset($param[1]))
		{
			// If in channel and no channel param (we don't want to overwrite a specified channel),
			// 		we assume it is for this channel
			$param[1] = $this->event->source();
		}
		
		// And let's choose a command.
		switch ($cmd)
		{
			case 'isfounder':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], self::FOUNDER) ? 'Yep, they\'re a founder.' : 'Nope, they aren\'t a founder.');
			break;

			case 'isadmin':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], self::ADMIN) ? 'Yep, they\'re an admin.' : 'Nope, they aren\'t an admin.');
			break;

			case 'isop':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], self::OP) ? 'Yep, they\'re an op.' : 'Nope, they aren\'t an op.');
			break;

			case 'ishalfop':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], self::HALFOP) ? 'Yep, they\'re a halfop.' : 'Nope, they aren\'t a halfop.');
			break;

			case 'isvoice':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], self::VOICE) ? 'Yep, they have voice.' : 'Nope, they don\'t have voice.');
			break;

			case 'isin':
				$this->call_privmsg($sender, $this->failnet->user_is($param[0], $param[1], NULL) ? 'Yep, they\'re in here.' : 'Nope, they aren\'t in here.');
			break;
		}
	}

	public function cmd_ping()
	{
		if(isset($this->event->arguments[1]))
		{
			$this->call_ping($this->event->arguments[0], $this->event->arguments[1]);
		}
		else
		{
			$this->call_pong($this->event->arguments[0]);
		}
	}
}

?>