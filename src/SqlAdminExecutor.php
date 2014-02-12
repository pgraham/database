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
namespace zpt\db;

/**
 * Class for executing database administration and DDL statements in an engine
 * agnostic way.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqlAdminExecutor implements SqlAdminAdapter
{

	private $db;
	private $adapter;
	private $exceptionAdapter;

	public function __construct(DatabaseConnection $db) {
		$this->db = $db;

		$driver = $db->getInfo()->getDriver();
		$this->exceptionAdapter = $this->exceptionAdapterFactor
			->getAdapter($driver);

		switch ($driver) {
			case 'mysql':
			$this->adapter = new MysqlAdapter($db);
			break;

			case 'pgsql':
			$this->adapter = new PgsqlAdapter($db);
			break;

			case 'sqlite':
			$this->adapter = new SqliteAdapter($db);
			break;

			default:
			throw new InvalidArgumentException("Unsupported driver $dbdriver");
		}
	}

	public function createDatabase($name, $charSet) {
		try {
			$this->adapter->createDatabase($name, $charSet);
		} catch (PDOException $e) {
			$dbException = $this->exceptionAdapter->adapt($e);
			throw $dbException;
		}
	}

	public function createUser($username, $passwd, $host = null) {
		try {
			$this->adapter->createUser($username, $passwd, $host);
		} catch (PDOException $e) {
			$dbException = $this->exceptionAdapter->adapt($e);
			throw $dbException;
		}
	}

	public function dropDatabase($name) {
		try {
			$this->adapter->dropDatabase($name);
		} catch (PDOException $e) {
			$dbException = $this->exceptionAdapter->adapt($e);
			throw $dbException;
		}
	}

	public function dropUser($username, $host = null) {
		try {
			$this->adapter->dropUser($username, $host);
		} catch (PDOException $e) {
			$dbException = $this->exceptionAdapter->adapt($e);
			throw $dbException;
		}
	}

	public function grantUserPermissions(
		$db,
		$username,
		$permissions,
		$host = null
	) {
		try {
			$this->adapter->grantUserPermissions($db, $username, $permissions, $host);
		} catch (PDOException $e) {
			throw $this->exceptionAdapter->adapt($e);
		}
	}
}
