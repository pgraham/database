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

use zpt\db\DatabaseConnectionInfo;

/**
 * Tests for the DatabaseConnectionInfo object.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnectionInfoTest extends TestCase
{

	public function testBasicConstruction() {
		$connInfo = new DatabaseConnectionInfo([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);

		$this->assertEquals('sqlite', $connInfo->getDriver());
		$this->assertEquals(':memory:', $connInfo->getSchema());
	}

	public function testSqliteInMemoryDsn() {
		$connInfo = new DatabaseConnectionInfo([
			'driver' => 'sqlite',
			'schema' => ':memory:'
		]);

		$this->assertEquals('sqlite::memory:', $connInfo->getDsn());
	}
}
