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
 * Factory for DatabaseExceptionAdapter instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AdapterFactory
{

	private $exceptionAdapters = [];

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
}
