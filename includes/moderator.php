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
 * Failnet - Moderator class,
 * 		Used as Failnet's moderator system, allowing him to manage a channel. 
 * 
 *
 * @package moderator
 * @author Obsidian
 * @copyright (c) 2009 - Failnet Project
 * @license GNU General Public License - Version 2
 */
class failnet_moderator extends failnet_common
{
	/**
	 * Items that have a fixed karma rating, along with what the fixed rating is.
	 * @var array
	 */
	private $stopwords = array();

	/**
	 * List of banned hostmasks per channel, and what date/time the ban will last until (UNIX timestamp) - 0 being permaban.
	 * @var array
	 */
	private $banlist = array();

	/**
	 * List of the offending hostmasks and how many points they have accrued.
	 * @var array
	 */
	private $offenders = array();

	/**
	 * List of actions Failnet will take once a user's score hits a specified point
	 * @var array
	 */
	private $reactions = array();

	/**
	 * List of channels that the moderator system is enabled in.
	 * @var array
	 */
	private $channels = array();

	/**
	 * Reaction types
	 */
	const WARN = 1;
	const KICK = 2;
	const KNOCKOUT = 3;
	const PERMABAN = 4;

	/**
	 * Specialized init function to allow class construction to be easier.
	 * @see includes/failnet_common#init()
	 * @return void
	 */
	public function init()
	{
		try
		{
			$table_exists = $this->failnet->db->query('SELECT COUNT(*) FROM sqlite_master WHERE name = ' . $this->failnet->db->quote('offenders'))->fetchColumn();

			// We want this as a transaction in case anything goes wrong.
			$this->db->beginTransaction();

			if(!$table_exists)
			{
				display(array('- Moderator system database tables not installed, installing moderator system', '- Constructing database tables...', ' -  Creating offenders table...'));

				// Make our DB tables...
				$this->db->exec(file_get_contents(FAILNET_ROOT . 'includes/schemas/offenders.sql'));
				display(' -  Creating stopwords table...');
				$this->db->exec(file_get_contents(FAILNET_ROOT . 'includes/schemas/stopwords.sql'));
				display(' -  Creating reactions table...');
				$this->db->exec(file_get_contents(FAILNET_ROOT . 'includes/schemas/reactions.sql'));
				display(' -  Creating banlist table...');
				$this->db->exec(file_get_contents(FAILNET_ROOT . 'includes/schemas/banlist.sql'));
			}

			display('- Preparing database for moderator system...');

			// Prepare some PDO statements
			$this->failnet->sql('offenders', 'create', 'INSERT INTO offenders ( hostmask, points ) VALUES ( :hostmask, :points )');
			$this->failnet->sql('offenders', 'update', 'UPDATE offenders SET points = :points WHERE LOWER(hostmask) = LOWER(:hostmask)');
			$this->failnet->sql('offenders', 'get', 'SELECT points FROM offenders WHERE LOWER(hostmask) = LOWER(:hostmask) LIMIT 1');
			$this->failnet->sql('offenders', 'get_old', 'SELECT points FROM offenders WHERE last_update <= (:time - 3600)');

			$this->failnet->sql('stopwords', 'create', 'INSERT INTO stopwords ( stopword, points ) VALUES ( :stopword, :points )');
			$this->failnet->sql('stopwords', 'update', 'UPDATE stopwords SET points = :points WHERE LOWER(stopword) = LOWER(:stopword)');
			$this->failnet->sql('stopwords', 'delete', 'DELETE FROM stopwords WHERE LOWER(stopword) = LOWER(:stopword)');
			$this->failnet->sql('stopwords', 'get_all', 'SELECT stopword, points FROM stopwords');

			$this->failnet->sql('reactions', 'create', 'INSERT INTO reactions ( reaction_type, reaction_time, points ) VALUES ( :type, :time, :points )');
			$this->failnet->sql('reactions', 'update', 'UPDATE reactions SET reaction_time = :time, points = :points WHERE reaction_id = :id');
			$this->failnet->sql('reactions', 'get', 'SELECT * FROM reactions WHERE (reaction_type = :type AND reaction_time = :time)');
			$this->failnet->sql('reactions', 'get_id', 'SELECT * FROM reactions WHERE reaction_id = :id');
			$this->failnet->sql('reactions', 'get_all', 'SELECT * FROM reactions');
			$this->failnet->sql('reactions', 'get_worst', 'SELECT reaction_type, reaction_time FROM reactions WHERE (points <= :points) ORDER BY points DESC LIMIT 1');
			$this->failnet->sql('reactions', 'delete', 'DELETE FROM reactions WHERE (reaction_type = :type AND reaction_time = :time)');
			$this->failnet->sql('reactions', 'delete_id', 'DELETE FROM reactions WHERE reaction_id = :id');

			$this->failnet->sql('banlist', 'create', 'INSERT INTO banlist ( hostmask, channel, unban_time ) VALUES ( :hostmask, :channel, :time )');
			$this->failnet->sql('banlist', 'update', 'UPDATE banlist SET unban_time = :time WHERE (LOWER(hostmask) = LOWER(:hostmask) AND LOWER(channel) = LOWER(:channel))');
			$this->failnet->sql('banlist', 'get', 'SELECT * FROM banlist WHERE (LOWER(hostmask) = LOWER(:hostmask) AND LOWER(channel) = LOWER(:channel))');
			$this->failnet->sql('banlist', 'get_id', 'SELECT * FROM banlist WHERE ban_id = :id');
			$this->failnet->sql('banlist', 'get_all', 'SELECT * FROM banlist');
			$this->failnet->sql('banlist', 'get_old', 'SELECT * FROM banlist WHERE (unban_time <> 0 AND unban_time <= :time)');
			$this->failnet->sql('banlist', 'delete', 'DELETE FROM banlist WHERE (LOWER(hostmask) = LOWER(:hostmask) AND LOWER(channel) = LOWER(:channel))');
			$this->failnet->sql('banlist', 'delete_id', 'DELETE FROM banlist WHERE ban_id = :id');
			$this->failnet->sql('banlist', 'delete_old', 'DELETE FROM banlist WHERE (unban_time <> 0 AND unban_time <= :time)');

			$this->failnet->db->commit();
		}
		catch (PDOException $e)
		{
			// Something went boom.  Time to panic!
			$this->failnet->db->rollBack();
			if(file_exists(FAILNET_ROOT . 'data/restart.inc')) 
				unlink(FAILNET_ROOT . 'data/restart.inc');
			trigger_error($e, E_USER_WARNING);
			sleep(3);
			exit(1);
		}

		$this->load();
	}
}

?>