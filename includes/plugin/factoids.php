<?php
/**
 *
 *===================================================================
 *
 *  Failnet -- PHP-based IRC bot
 *-------------------------------------------------------------------
 *	Script info:
 * Version:		2.0.0
 * SVN ID:		$Id$
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
 * Failnet - Factoid engine shell plugin,
 * 		The shell for the Factoid engine to be run in for Failnet. 
 * 
 *
 * @package plugins
 * @author Obsidian
 * @copyright (c) 2009 - Failnet Project
 * @license GNU General Public License - Version 2
 */
class failnet_plugin_factoids extends failnet_plugin_common
{
	/**
	 * What channels should we be quiet in?
	 * @var array
	 */
	private $quiet = array();
	
	/**
	 * How many factoids processed?
	 * @var integer
	 */
	private $done = 0;

	/**
	 * Only one factoid at a time?
	 * @var boolean
	 */
	private $return = false;

	public function cmd_privmsg()
	{
		// Process the command
		$text = $this->event->get_arg('text');
		$sender = $this->event->nick;
		$hostmask = $this->event->gethostmask();

		if($this->prefix($text))
		{
			$cmd = $this->purify($text);			
			switch ($cmd)
			{
				case 'quiet':
					// Make sure we are issuing this command in a channel
					if(!$this->event->fromchannel())
					{
						$this->call_privmsg($this->event->source(), 'I\'m sorry, but you must use this command in the channel that you want be to be quiet in.');
						return;
					}

					// See if we were already supposed to be quiet
					if(in_array($this->event->source(), $this->quiet))
					{
						$this->call_privmsg($this->event->source(), 'I already was being quiet.');
						return;
					}

					$this->quiet[] = $this->event->source();
					$this->call_privmsg($this->event->source(), 'Okay, I\'ll shut up for now.');
				break;

				case 'speak':
					// Make sure we are issuing this command in a channel
					if(!$this->event->fromchannel())
					{
						$this->call_privmsg($this->event->source(), 'I\'m sorry, but you must use this command in the channel that you want be to speak in.');
						return;
					}

					// See if we were already supposed to be quiet
					if(!in_array($this->event->source(), $this->quiet))
					{
						$this->call_privmsg($this->event->source(), 'I already was allowed to speak.');
						return;
					}

					// Build a lambda function to kill the quiet channel, then run it through array_split
					$drop_quiet = create_function('$param', 'return $param === "' . $this->event->source . '"'); 
					array_split($this->quiet, $drop_quiet);
					$this->call_privmsg($this->event->source(), 'Okay, I\'ll shut up for now.');
				break;
			}
		}
		else
		{
			// This isn't a command, so I guess we should check the factoids if we aren't supposed to be quiet in this channel.
		}
	}

	/**
	 * Check for matching factoids that apply to what our input is.
	 * @param string $tocheck - The message to check for factoid matching.
	 * @param string $sender - Who sent the message we are checking.
	 * @return void
	 */
	public function check($message, $sender = '[unknown]')
	{
		// Prep some vars
		$this->done = 0;
		$this->return = false;
		
		$message = str_replace('#', '\#', rtrim($message));
		if (preg_match('#^' . $this->failnet->get('nick') . '#i', $message))
		{
			$direct = true;
			$message = preg_replace('#^' . $this->failnet->get('nick') . '(|:|,) #is', '', $message);
		}
		else
		{
			$direct = false;
		}

		// Which factoid set will we use?
		if($direct)
		{
			$facts = &$this->failnet->factoids->index;
		}
		else
		{
			$facts = &$this->failnet->factoids->indirect;
		}

		// Prep the search/replace stuffs.
		$search = array('_nick_', '_owner_');
		$replace = array($this->failnet->get('nick'), $this->failnet->get('owner'));
		if ($sender != '[unknown]')
			$search[] = '_sender_'; $replace[] = $sender;

		// Scan for matching factoids!
		foreach($facts as $i => $fact)
		{
			$fact['pattern'] = str_replace($search, $replace, $fact['pattern']);

			if($this->return = true)
				return;

			if(preg_match('#' . $fact['pattern'] . '#is', $tocheck, $matches))
			{
				// Okay, we have a match.  Let's go through and find an entry for that factoid, then.
				$this->failnet->sql('entries', 'rand')->execute(array(':id' => $fact['factoid_id']));
				$result = $this->failnet->sql('entries', 'rand')->fetch(PDO::FETCH_ASSOC);
				
				// Now, check to see if this should be run through eval, selfcheck, authlevel, etc.
				// Are we authed for this entry?
				if($result['authlevel'] < $this->failnet->auth->authlevel($this->event->gethostmask()));
				{
					$this->call_privmsg($this->event->nick, $this->failnet->deny());
					$this->done();
					continue;
				}
				
				if($result['selfcheck'] == true && $this->failnet->checkuser($message))
				{
					$this->call_privmsg($this->event->source(), $this->failnet->deny());
					$this->done();
					continue;
				}
				// Uhm...do stuff....yeah.
			}

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			

			if ($fact['function'] == 1)
			{
				if (preg_match('#' . $fact['pattern'] . '#is', $tocheck, $matches))
				{
					if (sizeof($fact['factoids']) > 1)
					{
						$usefact = $fact['factoids'][rand(0, sizeof($fact['factoids']) - 1)];
						if (strpos($usefact, '_skip_') !== false)
						{
							eval($usefact);
							$this->done();
						}
					}
					else
					{
						eval($fact['factoids'][0]);
						$this->done();
					}
				}
			}
			else
			{
				if (preg_match('#' . $fact['pattern'] . '#is', $tocheck))
				{
					if (sizeof($fact['factoids']) > 1)
					{
						$usefact = $fact['factoids'][rand(0, sizeof($fact['factoids']) - 1)];
						if (strpos($usefact, '_action_') === 0)
						{
							$this->failnet->irc->action(preg_replace('#' . $fact['pattern'] . '#is', preg_replace('/^#_action\_#i', '', $usefact), $tocheck));
							$this->done();
						}
						elseif (strpos($usefact, '_skip_') === false)
						{
							$this->failnet->irc->privmsg(preg_replace('#' . $fact['pattern'] . '#is', $usefact, $tocheck));
							$this->done();
						}
					}
					else
					{
						if (strpos($fact['factoids'][0], '_action_') === 0)
						{
							$this->failnet->irc->action(preg_replace('#' . $fact['pattern'] . '#is', preg_replace('#^\_action\_#i', '', $fact['factoids'][0]), $tocheck));
							$this->done();
						}
						elseif (strpos($fact['factoids'][0], '_skip_') === false)
						{
							$this->failnet->irc->privmsg(preg_replace('#' . $fact['pattern'] . '#is', $fact['factoids'][0], $tocheck));
							$this->done();
						}
					}
				}
			}
			if($this->return = true)
				return;
		}
		if ($forme && $this->done == 0)
			return $this->failnet->no_factoid();
	}
	
	/**
	 * Helper function for failnet_plugin_factoids::check()
	 * @return void
	 */
	public function done()
	{
		if($this->failnet->get('single_factoid') == true)
		{
			$this->return = true;
		}
		else
		{
			$this->done++;
		}
	}
}

?>