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

use \zpt\db\adapter\AdapterFactory;
use \InvalidArgumentException;
use \PDOException;
use \PDO;

/**
 * Pdo Extension that provides an additional layer of abstraction for
 * normalizing database administration level commands.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnection
{

	/** Underlying PDO connection. */
	private $pdo;

	/** Database driver adapter. */
	private $adapter;

	/** Exception adapter. */
	private $exceptionAdapter;

	/**
	 * Create a new connection with with the given options.
	 *
	 * @param array|DatabaseConnectionInfo $options
	 *   Either a {@link DatabaseConnectionInfo} instance or an array of options
	 *   to use to construct a DatabaseConnectionInfo instance.
	 * @param AdapterFactory $exceptionAdapterFactory
	 *   AdapterFactory to use to retrieve the DatabaseExceptionAdapter instance
	 *   to use for the specified driver. If provided a cached adapter may be
	 *   used.
	 */
	public function __construct($options, AdapterFactory $adapterFactory = null) {
		if (is_array($options)) {
			$options = new DatabaseConnectionInfo($options);
		}

		if (!($options instanceof DatabaseConnectionInfo)) {
			throw new InvalidArgumentException(
				'Argument 1: Expected array or DatabaseConnectionInfo'
			);
		}

		if ($adapterFactory === null) {
			$adapterFactory = new AdapterFactory();
		}

		$driver = $options->getDriver();
		$this->exceptionAdapter = $adapterFactory->getExceptionAdapter($driver);

		try {
			$dsn = $options->getDsn();
			$this->pdo = new PDO(
				$dsn,
				$options->getUsername(),
				$options->getPassword(),
				$options->getPdoOptions()
			);
		} catch (PDOException $e) {
			throw $this->exceptionAdapter->adapt($e);
		}

		foreach ($options->getPdoAttributes() as $key => $value) {
			$this->pdo->setAttribute($key, $value);
		}

		$this->options = $options;
	}

	/**
	 * Access the underlying PDO object.
	 *
	 * @return PDO
	 */
	public function getPdo() {
		return $this->pdo;
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
	 * @see PDO::beginTransaction()
	 */
	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}

	/**
	 * @see PDO::commit()
	 */
	public function commit() {
		return $this->pdo->commit();
	}

	/**
	 * Executes the given SQL query and returns a QueryResult object containing
	 * the results of the query.
	 *
	 * @return StatementResult
	 * @throws zpt\db\exception\DatabaseException
	 *   Wraps any encountered PDOExceptions.
	 */
	public function exec($statement) {
		try {
			return $this->pdo->exec($statement);
		} catch (PDOException $e) {
			throw $this->exceptionAdapter->adapt($e, $statement);
		}
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

		try {
			$stmt = $this->pdo->prepare($statement, $driverOpts);
			return new PreparedStatement($stmt, $this->pdo, $this->exceptionAdapter);
		} catch (PDOException $e) {
			throw $this->exceptionAdapter->adapt($e, $statement);
		}
	}

	/**
	 * Issue a one-off SELECT statment.
	 *
	 * @param string $statement
	 * @return QueryResult
	 */
	public function query($statement) {
		try {
			$stmt = $this->pdo->query($statement);
			return new QueryResult($stmt, $this->pdo);
		} catch (PDOException $e) {
			throw $this->exceptionAdapter->adapt($e, $statement);
		}
	}

	/**
	 * @see PDO::rollback()
	 */
	public function rollback() {
		return $this->pdo->rollback();
	}
}
