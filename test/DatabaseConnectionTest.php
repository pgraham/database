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
namespace zpt\db\test;

require_once __DIR__ . '/test-common.php';

use PHPUnit_Framework_TestCase as TestCase;

use zpt\db\exception\DatabaseException;
use zpt\db\DatabaseConnection;

/**
 * Testsfor the DatabaseConnection object.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnectionTest extends TestCase
{

	private $conn;

	protected function setUp() {
		$this->conn = new DatabaseConnection([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);
	}

	protected function tearDown() {
		$this->conn = null;
	}

	public function testSqliteConnection() {
		$this->assertEquals('sqlite', $this->conn->getInfo()->getDriver());
		$this->assertEquals(':memory:', $this->conn->getInfo()->getSchema());
	}

	public function testExecException() {
		try {
			$sql = 'CREAT TABLE syntax_error ( key text )';
			$this->conn->exec($sql);
			$this->fail('Expected SQL syntax error exception');
		} catch (DatabaseException $e) {
			$this->assertEquals($sql, $e->getSql());
		}
	}

	public function testInsert() {
		$this->conn->exec('CREATE TABLE config (
			id integer PRIMARY KEY AUTOINCREMENT,
			key text,
			value text
		)');
		$insert = $this->conn->prepare(
			"INSERT INTO config (key, value) VALUES (:k, :v)"
		);
		$insertResult = $insert->execute([ 'k' => 'config1', 'v' => 'value1' ]);

		$selResult = $this->conn->query('SELECT * FROM config');
		$all = $selResult->fetchAll();
		$this->assertEquals(1, count($all));

		$r = $all[0];
		$this->assertEquals($insertResult->getInsertId(), $r['id']);
		$this->assertEquals('config1', $r['key']);
		$this->assertEquals('value1', $r['value']);
	}

	public function testPrepare() {
		$this->conn->exec('CREATE TABLE config ( key text, value text )');
		$stmt = $this->conn->prepare('SELECT * FROM config WHERE key = :key');

		$this->assertInstanceOf('zpt\db\PreparedStatement', $stmt);
	}

	public function testPrepareException() {
		try {
			$stmt = $this->conn->prepare('SELECT * FROM not_a_table');
			$this->fail('Expected exception for invalid prepared statement');
		} catch (DatabaseException $e) {
			$this->assertEquals('SELECT * FROM not_a_table', $e->getSql());
		}
	}
}
