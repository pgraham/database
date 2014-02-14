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

use \Iterator;
use \PDOStatement;
use \PDO;

/**
 * This class encapsulates a result set from a query.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class QueryResult implements Iterator
{

	private $stmt;
	private $insertId;
	private $rowCount;

	private $cache = [];

	private $nextIdx;
	private $nextRow;

	public function __construct(PDOStatement $stmt, PDO $pdo) {
		$this->stmt = $stmt;
		$this->insertId = $pdo->lastInsertId(); // TODO Enable name to be passed
		$this->rowCount = $stmt->rowCount();
	}

	public function fetch() {
		if ($this->stmt !== null) {
			// There still hasn't been a complete iteration of the statement object
			// so grab the its next row.
			$row = $this->stmt->fetch();

			if ($row !== false) {
				// The returned row is cached so that this object, unlike a PDOStatment
				// object can be iterated more than once.
				$this->cache[] = $row;
			} else {
				// The last result in this iteration has been encountered so the
				// PDOStatement is no longer needed.
				$this->stmt = null;
			}
		} else {
			// Grab the next row using the caches internal array pointer.
			$row = current($this->cache);
			next($cache);
		}

		return $row;
	}

	public function fetchAll() {
		if ($this->stmt !== null) {
			$this->cache = $this->stmt->fetchAll();
			$this->stmt = null;
		}

		return $this->cache;
	}

	public function fetchColumn($idx = 0) {
		$row = $this->fetch();
		return $row[0];
	}

	public function getInsertId() {
		return $this->insertId;
	}

	public function getRowCount() {
		// TODO Implement real row count?
		return $this->rowCount;
	}

	/* ======================================================================== *
	 * Iteration implementation.
	 * ======================================================================== */

	public function current() {
		return $this->nextRow;
	}

	public function key() {
		return $this->nextIdx;
	}

	public function next() {
		$this->nextRow = $this->fetch();
		$this->nextIdx++;
	}

	public function rewind() {
		reset($this->cache);
		$this->nextRow = $this->fetch();
		$this->nextIdx = 0;
	}

	public function valid() {
		return $this->nextRow !== false;
	}
}
