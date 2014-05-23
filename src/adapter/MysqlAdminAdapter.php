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

use zpt\util\StringUtils;
use zpt\db\DatabaseConnection;

/**
 * SQL Adapter for MySQL administrative commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MysqlAdminAdapter implements SqlAdminAdapter {

	const DEFAULT_CHARACTER_SET = 'utf8';
	const DEFAULT_USER_HOST = '%';

	const CREATE_DB_STMT = "CREATE DATABASE {name} CHARACTER SET {charSet};";
	const CREATE_USER_STMT = "CREATE USER '{username}'@'{host}' IDENTIFIED BY '{password}'";
	const DROP_DB_STMT = 'DROP DATABASE IF EXISTS {name}';
	const DROP_USER_STMT = "DROP USER '{username}'@'{host}'";

	const GRANT_STMT = "GRANT {perms} ON {database}.* TO '{username}'@'{host}'";

	private $db;

	public function __construct(DatabaseConnection $db) {
		$this->db = $db;
	}

	public function copyDatabase($source, $target) {
		$this->dropDatabase($target);
		$this->createDatabase($target, null);

		$user = escapeshellarg($this->db->getInfo()->getUsername());
		$pass = escapeshellarg($this->db->getInfo()->getPassword());
		$src = escapeshellarg($source);
		$tgt = escapeshellarg($target);

		$cmd = String('mysqldump -u{0} --password={1} {2}|'
			. 'mysql -u{0} --password={1} {3}')->format($user, $pass, $src, $tgt);

		$failure = false;
		passthru($cmd, $failure);
		if ($failure) {
			throw new RuntimeException("Unable to copy database $source to $target");
		}
	}

	/**
	 * Create a database with the give name. For now, character set is ignored.
	 */
	public function createDatabase($name, $charSet = null) {
		if ($charSet === null) {
			$charSet = self::DEFAULT_CHARACTER_SET;
		}

		$stmt = StringUtils::format(self::CREATE_DB_STMT, [
			'name' => $name,
			'charSet' => $charSet
		]);

		$this->db->exec($stmt);
	}

	public function createUser($username, $passwd, $host = null) {
		if ($host === null) {
			$host = self::DEFAULT_USER_HOST;
		}

		$stmt = StringUtils::format(self::CREATE_USER_STMT, [
			'username' => $username,
			'host'     => $host,
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
		if ($host === null) {
			$host = self::DEFAULT_USER_HOST;
		}

		$stmt = StringUtils::format(self::DROP_USER_STMT, [
			'username' => $username,
			'host'     => $host
		]);

		$this->db->exec($stmt);
	}

	public function grantUserPermissions(
		$db,
		$username,
		$permissions,
		$host = null
	) {

		$perms = (new PermissionParser)->parse($permissions);

		$stmt = StringUtils::format(self::GRANT_STMT, [
			'perms'    => $perms,
			'database' => $db,
			'username' => $username,
			'host'     => $host
		]);

		$this->db->exec($stmt);
	}
}
