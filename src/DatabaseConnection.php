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

	/** Database engine adapter factory */
	private $adapterFactory;

	/** Database driver adapter. */
	private $adminAdapter;

	/** Exception adapter. */
	private $exceptionAdapter;

	/** Query adapter for the underlying database engine. */
	private $queryAdapter;

	/* Whether or not a transaction is in progress */
	private $inTransaction = false;

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
		$this->adapterFactory = $adapterFactory;

		$driver = $options->getDriver();
		$this->exceptionAdapter = $adapterFactory->getExceptionAdapter($driver);
		$this->queryAdapter = $adapterFactory->getQueryAdapter($driver);

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
	 * Normalize to only a single transactions. In order to take advantange of
	 * drivers that support nested transactions access the underlying PDO object
	 * directly.
	 *
	 * @return boolean
	 */
	public function beginTransaction() {
		if ($this->inTransaction) {
			return false;
		}

		$this->inTransaction = $this->pdo->beginTransaction();
		return $this->inTransaction;
	}

	/**
	 * Passthrough for the commit() method.
	 *
	 * @return boolean
	 */
	public function commit() {
		if (!$this->inTransaction) {
			return false;
		}

		$this->inTransaction = false;
		return $this->pdo->commit();
	}

	/**
	 * Retrieve an {@link zpt\db\adapter\SqlAdminAdapter} for the current
	 * connection. This will only be useful if the user associated with the
	 * connection has sufficient priviledges to perform administrative SQL
	 * statements.
	 *
	 * @return zpt\db\adapter\SqlAdminAdapter
	 */
	public function getAdminAdapter() {
		if ($this->adminAdapter === null) {
			$this->adminAdapter = $this->adapterFactory->createAdminAdapter($this);
		}
		return $this->adminAdapter;
	}

	/**
	 * Retrieve an {@link zpt\db\adapter\QueryAdapter} for the current connection.
	 *
	 * @return zpt\db\adapter\QueryAdapter
	 */
	public function getQueryAdapter() {
		return $this->queryAdapter;
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
	 * Create a new database connection to the given schema using the same
	 * credentials and options as the current connection. The current connection
	 * is not closed.
	 *
	 * @param string $schema
	 */
	public function connectTo($schema) {
		$info = clone $this->getInfo();
		$info->setSchema($schema);
		return new DatabaseConnection($info);
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
	 * Passthrough for the rollback() method.
	 *
	 * @return boolean
	 */
	public function rollback() {
		if (!$this->inTransaction) {
			return false;
		}

		$this->inTransaction = false;
		return $this->pdo->rollback();
	}
}
