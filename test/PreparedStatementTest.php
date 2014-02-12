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

require_once __DIR__ . '/test-common.php';

use PHPUnit_Framework_TestCase as TestCase;
use zpt\db\exception\DatabaseException;

class PreparedStatementTest extends TestCase
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

	public function testExecuteNoParams() {
		$this->conn->exec('CREATE TABLE config ( key text, value text )');
		$stmt = $this->conn->prepare('SELECT * FROM config');
		$result = $stmt->execute();
	}

	public function testExecuteWithParam() {
		$this->conn->exec('CREATE TABLE config ( key text, value text )');
		$stmt = $this->conn->prepare('SELECT * FROM config WHERE key=:k');
		$result = $stmt->execute([ 'k' => 'key' ]);
	}

	public function testExecuteWithMultipleParams() {
		$this->conn->exec('CREATE TABLE config ( key text, value text )');
		$stmt = $this->conn->prepare(
			'SELECT * FROM config
			 WHERE key=:k AND value=:v');
		$result = $stmt->execute([ 'k' => 'key', 'v' => 'value' ]);
	}

	public function testPrepareFailure() {
		try {
			$stmt = $this->conn->prepare('SELECT * FROM config');
			$this->fail('Expected exception for non-existant table');
		} catch (DatabaseException $e) {
			// Do nothing, exception is expected
		}
	}

}
