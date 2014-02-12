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
namespace zpt\util\db;

use \zpt\util\StringUtils;
use \PDOException;
use \PDO;

/**
 * SQL Adapter for PostgreSQL administrative commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PgsqlAdapter implements SqlAdminAdapter {

	const CREATE_DB_STMT = 'CREATE DATABASE {name} ENCODING {charSet}';
	const CREATE_USER_STMT = "CREATE ROLE {username} WITH LOGIN PASSWORD '{password}'";
	const DROP_DB_STMT = 'DROP DATABASE {name}';
	const DROP_USER_STMT = 'DROP ROLE {name}';

	const REVOKE_PUBLIC_CONNECT_STMT = 'REVOKE CONNECT ON DATABASE {database} FROM PUBLIC';
	const REVOKE_PUBLIC_PERMS_STMT = 'REVOKE ALL ON ALL TABLES IN SCHEMA public FROM public';

	const GRANT_CONNECT_STMT = 'GRANT CONNECT ON DATABASE {database} TO {username}';
	const GRANT_PERMS_STMT = 'GRANT {perms} ON ALL TABLES IN SCHEMA public TO {username}';

	private $pdo;

	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function createDatabase($name, $charSet) {
		if ($charSet === null) {
			$charSet = 'DEFAULT';
		} else if (!is_int($charSet)) {
			$charSet = "'$charSet'";
		}

		$stmt = StringUtils::format(self::CREATE_DB_STMT, [
			'name' => $name,
			'charSet' => $charSet
		]);

		$this->pdo->exec($stmt);

		$stmt = StringUtils::format(self::REVOKE_PUBLIC_CONNECT_STMT, [
			'database' => $name
		]);
		$this->pdo->exec($stmt);

		// Create a new connection that is connected to the new database and revoke
		// all default privileges
		$conn = $this->pdo->newConnection([ 'database' => $name ]);
		$conn->exec(self::REVOKE_PUBLIC_PERMS_STMT);
	}

	public function createUser($username, $passwd, $host = null) {
		$stmt = StringUtils::format(self::CREATE_USER_STMT, [
			'username' => $username,
			'password' => $passwd
		]);

		$this->pdo->exec($stmt);
	}

	public function dropDatabase($name) {
		$stmt = StringUtils::format(self::DROP_DB_STMT, [
			'name' => $name
		]);

		$this->pdo->exec($stmt);
	}

	public function dropUser($username, $host = null) {
		$stmt = StringUtils::format(self::DROP_USER_STMT, [
			'username' => $name
		]);

		$this->pdo->exec($stmt);
	}

	public function grantUserPermissions(
		$database,
		$username,
		$permissions,
		$host = null
	) {

		$connectStmt = StringUtils::format(self::GRANT_CONNECT_STMT, [
			'username' => $username,
			'database' => $database
		]);

		$perms = (new PermissionParser)->parse($permissions);
		$stmt = StringUtils::format(self::GRANT_PERMS_STMT, [
			'perms' => $perms,
			'username' => $username
		]);

		$conn = $this->pdo->newConnection([ 'database' => $database ]);
		$conn->exec($stmt);
	}
}
