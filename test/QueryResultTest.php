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

use zpt\db\QueryResult;
use LogicException;

/**
 * Test the QueryResult class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class QueryResultTest extends TestCase
{

	private $db;

	protected function setUp() {
		$this->db = new DatabaseConnection([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);
	}

	protected function tearDown() {
		$this->db = null;
	}

	public function testIteration() {
		$this->db->exec('CREATE TABLE config ( key TEXT, val TEXT )');
		$this->db->exec("INSERT INTO config VALUES ('k1', 'v1')");

		$results = $this->db->query('SELECT * FROM config');

		$iter = 0;
		foreach ($results as $idx => $row) {
			$this->assertEquals('k1', $row['key']);
			$this->assertEquals('v1', $row['val']);

			$iter++;
		}
		$this->assertEquals(1, $iter);
	}

	public function testFetchAllNoCache() {
		$this->db->exec('CREATE TABLE config ( key TEXT, val TEXT )');
		$this->db->exec("INSERT INTO config VALUES ('k1', 'v1')");

		$qr = $this->db->query('SELECT * FROM config');

		$all = $qr->fetchAll();
		$this->assertCount(1, $all);

		try {
			$qr->fetch();
			$this->fail(
				"Expected exception for second iteration of non-cached query result"
			);
		} catch (LogicException $e) {
			$this->assertEquals(QueryResult::CACHE_NOT_ENABLED, $e->getCode());
		}
	}

	public function testFetchAllCache() {
		$this->db->exec('CREATE TABLE config ( key TEXT, val TEXT )');
		$this->db->exec("INSERT INTO config VALUES ('k1', 'v1')");

		$qr = $this->db->query('SELECT * FROM config')->useCache();

		$all1 = $qr->fetchAll();
		$all2 = $qr->fetchAll();

		$this->assertEquals($all1, $all2);
	}

	public function testMultipleUseCache() {
		$this->db->exec('CREATE TABLE config ( key TEXT, val TEXT )');
		$this->db->exec("INSERT INTO config VALUES ('k1', 'v1')");

		$qr = $this->db->query('SELECT * FROM config')->useCache();

		$all1 = $qr->fetchAll();
		$qr->useCache();
		$all2 = $qr->fetchAll();

		$this->assertEquals($all1, $all2);
	}

}
