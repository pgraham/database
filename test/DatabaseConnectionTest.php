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

use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Testsfor the DatabaseConnection object.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnectionTest extends TestCase
{

	public function testSqliteConnection() {
		$conn = new DatabaseConnection([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);

		$conn->exec('CREATE TABLE config ( key text, value text )');
		$insert = $conn->prepare("INSERT INTO config (key, value) VALUES (:k, :v)");
		$insert->execute([ 'k' => 'config1', 'v' => 'value1' ]);

		$sel = $conn->query('SELECT * FROM config');
		$all = $sel->fetchAll();
		$this->assertEquals(1, count($all));

		$r = $all[0];
		$this->assertEquals('config1', $r['key']);
		$this->assertEquals('value1', $r['value']);
	}
}
