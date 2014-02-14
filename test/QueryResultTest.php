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

/**
 * Test the QueryResult class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class QueryResultTest extends TestCase
{

	public function testIteration() {
		$db = new DatabaseConnection([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);

		$db->exec('CREATE TABLE config ( key TEXT, val TEXT )');
		$db->exec("INSERT INTO config VALUES ('k1', 'v1')");

		$results = $db->query('SELECT * FROM config');

		$iter = 0;
		foreach ($results as $idx => $row) {
			$this->assertEquals('k1', $row['key']);
			$this->assertEquals('v1', $row['val']);

			$iter++;
		}
		$this->assertEquals(1, $iter);
	}

}
