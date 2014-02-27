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
namespace zpt\db\test\exception;

require_once __DIR__ . '/../test-common.php';

use PHPUnit_Framework_TestCase as TestCase;

use zpt\db\exception\SqliteExceptionAdapter;
use PDOException;
use PDO;

/**
 * This class tests the SqliteExceptionAdapter.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SqliteExceptionAdapterTest extends TestCase
{

	private $db;

	protected function setUp() {
		$this->db = new PDO("sqlite::memory:");
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function testExceptionAdapt() {
		$adapter = new SqliteExceptionAdapter();
		try {
			$sql = "SELECT * FROM";
			$this->db->exec($sql);
		} catch (PDOException $e) {
			$dbe = $adapter->adapt($e, $sql);
			$this->assertEquals("SELECT * FROM", $dbe->getSql());
		}
	}

	public function testTableDoesNotExist() {
		$adapter = new SqliteExceptionAdapter();

		try {
			$sql = "SELECT * FROM not_a_table";
			$this->db->prepare($sql);
		} catch (PDOException $e) {
			$dbe = $adapter->adapt($e, $sql);
			$this->assertTrue($dbe->tableDoesNotExist());
		}
	}

}
