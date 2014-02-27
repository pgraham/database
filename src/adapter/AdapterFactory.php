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

use zpt\db\exception\MysqlExceptionAdapter;
use zpt\db\exception\PgsqlExceptionAdapter;
use zpt\db\exception\SqliteExceptionAdapter;
use zpt\db\DatabaseConnection;

/**
 * Factory for DatabaseExceptionAdapter instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AdapterFactory
{

	private $exceptionAdapters = [];
	private $queryAdapters = [];

	/**
	 * Get a {@link DatabaseExceptionAdapter} instance for the specified driver.
	 * Instance may be cached/shared.
	 *
	 * @param string $driver
	 *   The database engine for which to retrieve an adapter.
	 * @return DatabaseExceptionAdapter
	 */
	public function getExceptionAdapter($driver) {
		if (!isset($this->exceptionAdapters[$driver])) {
			$this->exceptionAdapters[$driver] = $this->createExceptionAdapter(
				$driver);
		}
		return $this->exceptionAdapters[$driver];
	}

	/**
	 * Get a {@link QueryAdapter} instance for the specified driver. Instance may 
	 * be cached/shared.
	 */
	public function getQueryAdapter($driver) {
		if (!isset($this->queryAdapters[$driver])) {
			$this->queryAdapters[$driver] = $this->createQueryAdapter($driver);
		}
		return $this->queryAdapters[$driver];
	}

	/**
	 * Create a {@link SqlAdminAdapter} instance for the specified driver. Will
	 * always be a new, unshared instance.
	 *
	 * @param string $driver
	 *   The database engine for which to retrieve an adapter.
	 * @return SqlAdminAdapter
	 */
	public function createAdminAdapter(DatabaseConnection $db) {
		$driver = $db->getInfo()->getDriver();
		switch ($driver) {
			case 'mysql':
			return new MysqlAdminAdapter($db);

			case 'pgsql':
			return new PgsqlAdminAdapter($db);

			case 'sqlite':
			return new SqliteAdminAdapter($db);

			default:
			throw new InvalidArgumentException("Unsupported driver: $driver");
		}
	}

	/**
	 * Create a {@link DatabaseExceptionAdapter} instance for the specified
	 * driver. Will always be a new, unshared instance.
	 *
	 * @param string $driver
	 *   The database engine for which to retrieve an adapter.
	 * @return DatabaseExceptionAdapter
	 */
	public function createExceptionAdapter($driver) {
		switch ($driver) {
			case 'mysql':
			return new MysqlExceptionAdapter();

			case 'pgsql':
			return new PgsqlExceptionAdapter();

			case 'sqlite':
			return new SqliteExceptionAdapter();

			default:
			throw new InvalidArgumentException("Unsupported driver: $driver");
		}
	}

	/**
	 * Create a {@link QueryAdapter} instance for the specified driver. Will 
	 * always be a new, unshared instance.
	 *
	 * @param string $driver
	 *   The database engine for which to retrieve and adapter.
	 * @return QueryAdapter
	 */
	public function createQueryAdapter($driver) {
		switch ($driver) {
			case 'mysql':
			return new MysqlQueryAdapter();

			case 'pgsql':
			return new PgsqlQueryAdapter();

			case 'sqlite':
			return new SqliteQueryAdapter();

			default:
			throw new InvalidArgumentException("Unsupported driver: $driver");
		}
	}
}
