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
use zpt\db\exception\DatabaseException;

/**
 * This class defines test cases for the MysqlAdminAdapter class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MysqlAdminAdapterTest extends TestCase
{

	private function getDb() {
		if (!isset($GLOBALS['MYSQL_USER']) || !isset($GLOBALS['MYSQL_PASS'])) {
			$this->markTestSkipped("Mysql connection information not available."
				. " View the README for information on how to setup database tests.");
		}

		return new DatabaseConnection([
			'driver' => 'mysql',
			'username' => $GLOBALS['MYSQL_USER'],
			'password' => $GLOBALS['MYSQL_PASS']
		]);
	}

	private function getPrivDb() {
		if (!isset($GLOBALS['MYSQL_PRIV_USER']) &&
		    !isset($GLOBALS['MYSQL_PRIV_PASS']))
		{
			$this->markTestSkipped("Mysql connection information for priviledged "
				. "user not available. View the README for information on how to setup "
				. "database tests");
		}

		return new DatabaseConnection([
			'driver' => 'mysql',
			'username' => $GLOBALS['MYSQL_PRIV_USER'],
			'password' => $GLOBALS['MYSQL_PRIV_PASS']
		]);
	}

	public function testConstruction() {
		$db = $this->getDb();
		$adapter = new MysqlAdminAdapter($db);
	}

	public function testCopyAuthError() {
		$db = $this->getDb();
		$adapter = new MysqlAdminAdapter($db);

		try {
			$adapter->copyDatabase('mysql', 'mysql_backup');
			$this->fail("Expected an error attempting to copy database mysql");
		} catch (DatabaseException $e) {
			$this->assertTrue($e->isAuthorizationError());
		}
	}

	public function testCopy() {
		$db = $this->getPrivDb();
		$adapter = new MysqlAdminAdapter($db);
		$adapter->createDatabase('phpunit_db', null);
		$adapter->copyDatabase('phpunit_db', 'phpunit_db_cp');
	}

}
