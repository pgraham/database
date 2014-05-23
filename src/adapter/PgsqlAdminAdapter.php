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
use zpt\db\exception\DatabaseException;
use zpt\util\StringUtils;

/**
 * SQL Adapter for PostgreSQL administrative commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PgsqlAdminAdapter implements SqlAdminAdapter {

	const CREATE_DB_STMT = 'CREATE DATABASE {name} ENCODING {charSet}';
	const CREATE_USER_STMT = "CREATE ROLE {username} WITH LOGIN PASSWORD '{password}'";
	const DROP_DB_STMT = 'DROP DATABASE IF EXISTS {name}';
	const DROP_USER_STMT = 'DROP ROLE {name}';

	const REVOKE_PUBLIC_CONNECT_STMT = 'REVOKE CONNECT ON DATABASE {database} FROM PUBLIC';
	const REVOKE_PUBLIC_PERMS_STMT = 'REVOKE ALL ON ALL TABLES IN SCHEMA public FROM public';

	const GRANT_CONNECT_STMT = 'GRANT CONNECT ON DATABASE {database} TO {username}';
	const GRANT_PERMS_STMT = 'GRANT {perms} ON ALL TABLES IN SCHEMA public TO {username}';

	private $db;

	public function __construct(DatabaseConnection $db) {
		$this->db = $db;
	}

	/**
	 * Copy the specified source database to the target. If the target already
	 * exists it will be dropped. Note that since pg_dump is used to copy the
	 * database and pg_dump doesn't accept command line passwords, this will only
	 * work if run as a user with appropriate permissions.
	 */
	public function copyDatabase($source, $target) {
		$this->dropDatabase($target);
		$this->createDatabase($target, null);

		$src = escapeshellarg($source);
		$tgt = escapeshellarg($tgt);

		// Use pg_dump to create the copy
		$cmd = String("pg_dump {0} | psql {1}")->format($src, $tgt);
		$failure = false;
		passthru($cmd, $failure);
		if ($failure) {
			throw new RuntimeException("Unable to copy database $source to $target");
		}
	}

	public function createDatabase($name, $charSet = null) {
		if ($charSet === null) {
			$charSet = 'DEFAULT';
		} else if (!is_int($charSet)) {
			$charSet = "'$charSet'";
		}

		$stmt = StringUtils::format(self::CREATE_DB_STMT, [
			'name' => $name,
			'charSet' => $charSet
		]);

		$this->db->exec($stmt);

		$stmt = StringUtils::format(self::REVOKE_PUBLIC_CONNECT_STMT, [
			'database' => $name
		]);
		$this->db->exec($stmt);

		// Create a new connection that is connected to the new database and revoke
		// all default privileges
		$conn = $this->db->newConnection([ 'database' => $name ]);
		$conn->exec(self::REVOKE_PUBLIC_PERMS_STMT);
	}

	public function createUser($username, $passwd, $host = null) {
		$stmt = StringUtils::format(self::CREATE_USER_STMT, [
			'username' => $username,
			'password' => $passwd
		]);

		$this->db->exec($stmt);
	}

	public function dropDatabase($name) {
		$stmt = StringUtils::format(self::DROP_DB_STMT, [
			'name' => $name
		]);

		$this->db->exec($stmt);
	}

	public function dropUser($username, $host = null) {
		$stmt = StringUtils::format(self::DROP_USER_STMT, [
			'username' => $name
		]);

		$this->db->exec($stmt);
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

		$conn = $this->db->newConnection([ 'database' => $database ]);
		$conn->exec($stmt);
	}
}
