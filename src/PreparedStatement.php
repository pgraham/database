<?php
/**
 * =============================================================================
 * Copyright (c) 2014, Philip Graham
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
namespace zpt\db;

use \PDOException;
use \PDOStatement;
use \PDO;

/**
 * PdoStatement wrapper that translates caught PDOExceptions into
 * DatabaseExceptions. This class extends PDOStatement for typehinting only. All
 * of PDOStatements public API is overriden.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PreparedStatement
{

	private $exAdapter;
	private $pdo;
	private $stmt;

	/**
	 * Create a new PDOStatement wrapper that throws DatabaseExceptions instead of
	 * PDOExceptions.
	 *
	 * @param PDOStatment $statment
	 * @param PDO $pdo
	 *   The PDO connection object on which the query is to be executed.
	 * @param DatabaseExceptionAdapter $exceptionAdapter
	 *   Driver specific PDOException --> DatabaseException adapter.
	 */
	public function __construct(
		PDOStatement $statement,
		PDO $pdo,
		$exceptionAdapter
	) {
		$this->stmt = $statement;
		$this->pdo = $pdo;
		$this->exAdapter = $exceptionAdapter;
	}

	/**
	 * Access the underlying PDOStatment object.
	 *
	 * @return PDOStatment
	 */
	public function getPdoStatement() {
		return $this->stmt;
	}

	/**
	 * Execute the prepared statment with the given parameter values.
	 *
	 * @throws DatabaseException
	 */
	public function execute($inputParams = null) {
		if ($inputParams === null) {
			$inputParams = [];
		}

		try {
			$this->stmt->execute($inputParams);

			return new QueryResult($this->stmt, $this->pdo);
			return $this->stmt->execute($inputParams);
		} catch (PDOException $e) {
			throw $this->exAdapter->adapt($e);
		}
	}
}
