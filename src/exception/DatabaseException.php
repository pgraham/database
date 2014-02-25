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
namespace zpt\db\exception;

use PDOException;
use RuntimeException;

/**
 * Exception class which parses PDOExceptions to make it easier to
 * programatically determine the cause of an exception.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseException extends RuntimeException
{

	private $sqlCode;
	private $sql;
	private $params;

	private $databaseAlreadyExists = false;
	private $userAlreadyExists = false;
	private $tableDoesNotExist = false;

	public function __construct(
		PDOException $cause,
		$sql = null,
		array $params = null
	) {

		$this->sqlCode = $cause->getCode();
		$this->sql = $sql;
		$this->params = $params;

		if ($sql !== null) {
			$msg = $this->buildMessage($sql, $params);
		} else {
			$msg = $cause->getMessage();
		}

		// the inherited code property is expected to be an integer but it is
		// possible that a string will be returned by the PDOException's getCode()
		// method which will produce warnings if passed to the parent constructor.
		if (is_numeric($this->sqlCode)) {
			$code = $this->sqlCode;
		} else {
			$code = 0;
		}

		parent::__construct($msg, $code, $cause);
	}

	public function getSql() {
		return $this->sql;
	}

	public function getSqlParameters() {
		return $this->params;
	}

	public function getSqlCode() {
		return $this->sqlCode;
	}

	public function databaseAlreadyExists($databaseAlreadyExists = null) {
		if ($databaseAlreadyExists === null) {
			return $this->databaseAlreadyExists;
		}
		$this->databaseAlreadyExists = (bool) $databaseAlreadyExists;
	}

	public function tableDoesNotExist($tableDoesNotExist = null) {
		if ($tableDoesNotExist === null) {
			return $this->tableDoesNotExist;
		}
		$this->tableDoesNotExist = (bool) $tableDoesNotExist;
	}

	public function userAlreadyExists($userAlreadyExists = null) {
		if ($userAlreadyExists === null) {
			return $this->userAlreadyExists;
		}
		$this->userAlreadyExists = (bool) $userAlreadyExists;
	}

	protected function buildMessage($stmt, $params) {
		$msg =  "Exception occured executing statement:\n  $stmt";

		if ($params !== null) {
			$msg .= "\n\n  Parameters: ";
			$msg .= implode("\n    ", array_map(function ($k) use ($params) {
				return "$k: {$params[$k]}";
			}, array_keys($params)));
		}
		return $msg;
	}
}
