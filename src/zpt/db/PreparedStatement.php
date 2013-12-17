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
namespace zpt\db;

use \PDOStatement;
use \PDO;

/**
 * PdoStatement wrapper that translates caught PDOExceptions into
 * DatabaseExceptions. This class extends PDOStatement for typehinting only. All
 * of PDOStatements public API is overriden.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PreparedStatement extends PDOStatement {

	private $stmt;

	/**
	 * Create a new PDOStatement wrapper that throws DatabaseExceptions instead of
	 * PDOExceptions.
	 *
	 * @param PDOStatment $statment
	 * @param DatabaseExceptionAdapter $exceptionAdapter Driver specific 
	 *        PDOException --> DatabaseException adapter.
	 */
	public function __construct(PDOStatement $statement) {
		$this->stmt = $statement;
	}

	/** */
	public function bindColumn(
		$column,
		&$param,
		$type = null,
		$maxlen = null,
		$driverData = null
	) {

		return $this->stmt->bindColumn(
			$column,
			$param,
			$type,
			$maxlen,
			$driverData
		);
	}

	/** */
	public function bindParam(
		$param,
		&$var,
		$dataType = null,
		$length = null,
		$driverOpts = null
	) {

		if ($dataType === null) {
			$dataType = PDO::PARAM_STR;
		}

		return $this->stmt->bindParam(
			$param,
			$var,
			$dataType,
			$length,
			$driverOpts
		);

	}

	/** */
	public function bindValue($param, $val, $dataType = null) {
		if ($dataType === null) {
			$dataType = PDO::PARAM_STR;
		}

		return $this->stmt->bindValue($param, $val, $dataType);
	}

	/** */
	public function closeCursor() {
		return $this->stmt->closeCursor();
	}

	/** */
	public function columnCount() {
		return $this->stmt->columnCount();
	}

	/** */
	public function debugDumpParams() {
		$this->stmt->debugDumpParams();
	}

	/** */
	public function errorCode() {
		return $this->stmt->errorCode();
	}

	/** */
	public function errorInfo() {
		return $this->stmt->errorInfo();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DatabaseException
	 */
	public function execute($inputParams = null) {
		if ($inputParams === null) {
			$inputParams = [];
		}

		return $this->stmt->execute($inputParams);
	}

	/** */
	public function fetch(
		$fetchStyle = null,
		$cursorOrientation = null,
		$cursorOffset = 0
	) {
		if ($fetchStyle === null) {
			$fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE;
		}
		if ($cursorOrientation === null) {
			$cursorOrientation = PDO::FETCH_ORI_NEXT;
		}
		return $this->stmt->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
	}

	/** */
	public function fetchAll(
		$fetchStyle = null,
		$fetchArgument = null,
		$ctorArgs = []
	) {

		if ($fetchStyle === null) {
			$fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE;
		}

		return $this->stmt->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
	}

	/** */
	public function fetchColumn($columnNumber = 0) {
		return $this->stmt->fetchColumn($columnNumber);
	}

	/** */
	public function fetchObject($className = null, $ctorArgs = null) {
		if ($ctorArgs === null) {
			$ctorArgs = [];
		}

		return $this->stmt->fetchObject($className, $ctorArgs);
	}

	/** */
	public function getAttribute($attribute) {
		return $this->stmt->getAttribute($attribute);
	}

	/** */
	public function getColumnMeta($column) {
		return $this->stmt->getColumnMeta($column);
	}

	/** */
	public function nextRowset() {
		return $this->stmt->nextRowset();
	}

	/** */
	public function rowCount() {
		return $this->stmt->rowCount();
	}

	/** */
	public function setAttribute($attribute, $value) {
		return $this->stmt->setAttribute($attribute, $value);
	}

	/** */
	public function setFetchMode($mode, $modeArg = null, $ctorArgs = []) {
		if ($mode == PDO::FETCH_COLUMN) {
			return $this->stmt->setFetchMode($mode, $modeArg);
		} else if ($mode == PDO::FETCH_CLASS) {
			return $this->stmt->setFetchMode($mode, $modeArg, $ctorArgs);
		} else if ($mode === PDO::FETCH_INTO) {
			return $this->stmt->setFetchMode($mode, $modeArg);
		} else {
			return $this->stmt->setFetchMode($mode);
		}
	}

}
