<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of Reed and is licensed by the Copyright holder under
 * the 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 * =============================================================================
 */
namespace zpt\db\adapter;

use zpt\db\DatabaseConnection;
use zpt\util\StringUtils;

/**
 * SQL Adapter for PostgreSQL administrative commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqliteAdminAdapter implements SqlAdminAdapter {

	private $db;

	public function __construct(DatabaseConnection $db) {
		$this->db = $db;
	}

	public function copyDatabase($source, $target) {
		throw new RuntimeException(
			"Copying SQLite databases is not currently supported"
		);
	}

	public function createDatabase($name, $charSet = null) {
		// TODO Create a new database file and connect to it. What is the path?
		throw new RuntimeException(
			"Creating SQLite databases is not currently supported"
		);
	}

	public function createUser($username, $passwd, $host = null) {
		throw new RuntimeException("SQLite databases do not support users");
	}

	public function dropDatabase($name) {
		// TODO Delete the database file and reconnect to an in memory database. 
		// What is the path?
		throw new RuntimeException(
			"Dropping SQLite databases is not currently supported"
		);
	}

	public function dropUser($username, $host = null) {
		throw new RuntimeException("SQLite databases do not support users");
	}

	public function grantUserPermissions(
		$database,
		$username,
		$permissions,
		$host = null
	) {
		throw new RuntimeException("SQLite databases do not upport users");
	}
}
