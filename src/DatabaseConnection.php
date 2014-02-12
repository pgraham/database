<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
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

use \InvalidArgumentException;
use \PDO;

/**
 * Pdo Extension that provides an additional layer of abstraction for
 * normalizing database administration level commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnection extends PDO {

	/**
	 * Create a new connection with with the given options.
	 *
	 * @param array|DatabaseConnectionInfo $options
	 *   Either a {@link DatabaseConnectionInfo} instance or an array of options
	 *   to use to construct a DatabaseConnectionInfo instance.
	 */
	public function __construct($options) {
		if (is_array($options)) {
			$options = new DatabaseConnectionInfo($options);
		}

		if (!($options instanceof DatabaseConnectionInfo)) {
			throw new InvalidArgumentException(
				'Argument 1: Expected array or DatabaseConnectionInfo'
			);
		}

		$dsn = $options->getDsn();

		parent::__construct(
			$dsn,
			$options->getUsername(),
			$options->getPassword(),
			$options->getPdoOptions()
		);

		foreach ($options->getPdoAttributes() as $key => $value) {
			$this->setAttribute($key, $value);
		}

		$this->options = $options;
	}

	/**
	 * Getter for the options used to create the connection.
	 *
	 * @return DatabaseConnectionInfo
	 */
	public function getInfo() {
		return $this->options;
	}

	/**
	 * Create a prepared statement for the given query.
	 *
	 * @params string $statement
	 *   The SQL query to prepare.
	 * @params array $driverOpts
	 *   Options for the prepare statement.
	 */
	public function prepare($statement, $driverOpts = null) {
		if ($driverOpts === null) {
			$driverOpts = [];
		}

		$stmt = parent::prepare($statement, $driverOpts);
		return new PreparedStatement($stmt);
	}
}
