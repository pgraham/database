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
use zpt\db\adapter\MysqlAdminAdapter;

/**
 * This class defines test cases for the MysqlAdminAdapter class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MysqlAdminAdapterTest extends TestCase
{

	protected function setUp() {
		if (!isset($GLOBALS['MYSQL_USER']) || !isset($GLOBALS['MYSQL_PASS'])) {
			$this->markTestSkipped("Mysql connection information not available."
				. " View the README for information on how to setup database tests.");
		}
	}

	public function testConstruction() {
		$db = new DatabaseConnection([
			'driver' => 'mysql',
			'username' => $GLOBALS['MYSQL_USER'],
			'password' => $GLOBALS['MYSQL_PASS']
		]);

		$adapter = new MysqlAdminAdapter($db);
	}

}
