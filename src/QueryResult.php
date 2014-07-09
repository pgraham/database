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

use Iterator;
use LogicException;
use PDOStatement;
use PDO;

/**
 * This class encapsulates a result set from a query.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class QueryResult implements Iterator
{

	const CACHE_NOT_ENABLED = 1;

	private $stmt;
	private $insertId;
	private $rowCount;

	private $cache = null;

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
				if (is_array($this->cache)) {
					$this->cache[] = $row;
				}
			} else {
				// The last result in this iteration has been encountered so the
				// PDOStatement is no longer needed.
				$this->stmt = null;
			}
		} elseif (is_array($this->cache)) {
			// Grab the next row using the caches internal array pointer.
			$row = current($this->cache);
			next($cache);
		} else {
			throw new LogicException(
				"Results can only be iterated once unless cache is enabled.",
				self::CACHE_NOT_ENABLED
			);
		}

		return $row;
	}

	public function fetchAll() {
		if ($this->stmt !== null) {
			$all = $this->stmt->fetchAll();
			if (is_array($this->cache)) {
				$this->cache = $all;
			}
			$this->stmt = null;
			return $all;
		}

		if (is_array($this->cache)) {
			return $this->cache;
		}

		throw new LogicException(
			"Results can only be iterated once unless cache is enabled.",
			self::CACHE_NOT_ENABLED
		);
	}

	public function fetchColumn($idx = 0) {
		$row = $this->fetch();
		if (isset($row[$idx])) {
			return $row[$idx];
		} else {
			return null;
		}
	}

	public function getInsertId() {
		return $this->insertId;
	}

	public function getRowCount() {
		// TODO Implement real row count?
		return $this->rowCount;
	}

	public function useCache($useCache = true) {
		if ($useCache) {
			if (!is_array($this->cache)) {
				if ($this->stmt === null) {
					throw new LogicException(
						"Cache must be enabled prior to first iteration",
						self::CACHE_NOT_ENABLED
					);
				}

				$this->cache = [];
			}
		} else {
			$this->cache = null;
		}

		// Allow chaining
		return $this;
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
		if (is_array($this->cache)) {
			reset($this->cache);
		}
		$this->nextRow = $this->fetch();
		$this->nextIdx = 0;
	}

	public function valid() {
		return $this->nextRow !== false;
	}
}
