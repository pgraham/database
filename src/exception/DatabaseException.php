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

use \Exception;
use \PDOException;

/**
 * Exception class which parses PDOExceptions to make it easier to
 * programatically determine the cause of an exception.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseException extends PDOException
{

	private $sqlCode;

	private $databaseAlreadyExists = false;
	private $userAlreadyExists = false;
	private $tableDoesNotExist = false;

	public function __construct(
		$msg = null,
		$code = null,
		Exception $previous = null
	) {
		// the inherited code property is expected to be an integer but it is
		// possible that a string will be passed to the constructor which will
		// produce warnings if passed to the parent constructor.
		$this->sqlCode = $code;
		if (!is_numeric($code)) {
			$code = 0;
		}

		parent::__construct($msg, $code, $previous);
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
}
