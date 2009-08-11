/**
 * Factoid entries table
 * $Id$
 */

CREATE TABLE entries (
	entry_id INTEGER PRIMARY KEY NOT NULL,
	factoid_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	authlevel INTEGER UNSIGNED NOT NULL DEFAULT '0',
	selfcheck INTEGER UNSIGNED NOT NULL DEFAULT '0',
	function INTEGER UNSIGNED NOT NULL DEFAULT '0',
	entry TEXT NOT NULL DEFAULT '',
);