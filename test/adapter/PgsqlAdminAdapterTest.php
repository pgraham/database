<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of Conductor. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 */
namespace zpt\db\test\adapter;

require_once __DIR__ . '/../test-common.php';

use PHPUnit_Framework_TestCase as TestCase;

use zpt\db\DatabaseConnection;
use zpt\db\adapter\PgsqlAdminAdapter;

/**
 * This class defines test cases for the PgsqlAdminAdapter class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PgsqlAdminAdapterTest extends TestCase
{

	protected function setUp() {
		if (!isset($GLOBALS['PGSQL_USER']) || !isset($GLOBALS['PGSQL_PASS'])) {
			$this->markTestSkipped("Postgresql connection information not available."
				. " View the README for information on how to setup database tests.");
		}
	}

	public function testConstruction() {
		$db = new DatabaseConnection([
			'driver' => 'pgsql',
			'username' => 'test_user',
			'password' => '123abc'
		]);

		$adapter = new PgsqlAdminAdapter($db);
	}

}
