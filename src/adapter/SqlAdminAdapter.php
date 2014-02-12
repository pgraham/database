<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of zeptech/database and is licensed by the Copyright holder
 * under the 3-clause BSD License.  The full text of the license can be found in
 * the LICENSE.txt file included in the root directory of this distribution or
 * at the link below.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 * =============================================================================
 */
namespace zpt\db\adapter;

/**
 * Interface for SQL Adapters for administrative commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface SqlAdminAdapter {

	/**
	 * Create a database with the given name and character set.
	 *
	 * @param string $name The name of the database
	 * @param string $charSet The character set for the new database
	 */
	public function createDatabase($name, $charSet);

	/**
	 * Create a database user with the given credentials.
	 *
	 * @param string $username The username for the new user
	 * @param string $passwd The password for the new user
	 * @param string $host [Optional] The host from which the user may connect.
	 *   May be ignored by some drivers.
	 */
	public function createUser($username, $passwd, $host = null);

	/**
	 * Drop the database with the given name.
	 *
	 * @param string $name The name of the database to drop
	 */
	public function dropDatabase($name);

	/**
	 * Drop the database user with the given username.
	 *
	 * @param string $username The name of the user to drop
	 * @param string $host [Optional] The host from which the user may connect.
	 *   May be ignored by some drivers.
	 */
	public function dropUser($username, $host = null);

	/**
	 * Grant the specified permissions to the given user on the given database.
	 * This will be clear any previously granted permissions which are
	 * unspecified.
	 *
	 * @param string $db The database on which permissions will be granted.
	 * @param string $username The user to which permissions will be granted.
	 * @param integer $permissions Bit mask defining the permissions to grant.
	 * @param string $host [Optional] Host from which user must be connected. May
	 *   be ignored by some adapters.
	 */
	public function grantUserPermissions($db, $username, $permissions, $host = null);

}
