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
namespace zpt\db\exception;

/**
 * Factory for DatabaseExceptionAdapter instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ExceptionAdapterFactory
{

	private $cache = [];

	/**
	 * Get a {@link DatabaseExceptionAdapter} instance for the specified driver.
	 * Instance may be cached/shared.
	 *
	 * @param string $driver
	 *   The database engive for which to retrieve an adapter.
	 * @return DatabaseExceptionAdapter
	 */
	public function getAdapter($driver) {
		if (!isset($this->cache[$driver])) {
			$this->cache[$driver] = $this->createAdapter($driver);
		}
		return $this->cache[$driver];
	}

	/**
	 * Create a {@link DatabaseExceptionAdapter} instance for the specified
	 * driver. Will always be a new, unshared instance.
	 *
	 * @param string $driver
	 *   The database engine for which to retrieve an adapter.
	 * @return DatabaseExceptionAdapter
	 */
	public function createAdapter($driver) {
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
}
