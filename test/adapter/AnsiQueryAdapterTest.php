<?php
/**
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech database. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */
namespace zpt\db\adapter;

require_once __DIR__ . '/../test-common.php';

use PHPUnit_Framework_TestCase as TestCase;

/**
 * This class tests the AnsiQueryAdapter.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class AnsiQueryAdapterTest extends TestCase
{

	public function testEscapeStarField() {
		$queryAdapter = new PgsqlQueryAdapter();
		$this->assertEquals('*', $queryAdapter->escapeField('*'));
	}

	public function testEscapeField() {
		$queryAdapter = new PgsqlQueryAdapter();
		$this->assertEquals('"field"', $queryAdapter->escapeField('field'));
	}

	public function testEscapeFieldWithEscapeCharacter() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"ta""ble"';
		$actual = $queryAdapter->escapeField('ta"ble');
	}

	public function testEscapeQualifiedField() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"table"."field"';
		$actual = $queryAdapter->escapeField("table.field");
		$this->assertEquals($expected, $actual);
	}

	public function testQualifiedFieldWithAll() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"table".*';
		$actual = $queryAdapter->escapeField('table.*');
		$this->assertEquals($expected, $actual);
	}

	public function testEscapeQualifedWithEscapeCharacter() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"ta""ble"."field"';
		$actual = $queryAdapter->escapeField('ta"ble.field');
	}

	public function testEscapeFieldWithAlias() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"table" "alias"';
		$actual = $queryAdapter->escapeField('table alias');
		$this->assertEquals($expected, $actual);
	}

	public function testEscapeQualifiedFieldWithAlias() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"table"."field" "alias"';
		$actual = $queryAdapter->escapeField('table.field alias');
		$this->assertEquals($expected, $actual);
	}

	public function testEscapeFieldWithAsAlias() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"field" as "alias"';
		$actual = $queryAdapter->escapeField('field as alias');
		$this->assertEquals($expected, $actual);

		$expected = '"field" AS "alias"';
		$actual = $queryAdapter->escapeField('field AS alias');
		$this->assertEquals($expected, $actual);
	}

	public function testEscapeFieldOrAliasNamedAs() {
		$queryAdapter = new PgsqlQueryAdapter();
		$expected = '"as" "alias"';
		$actual = $queryAdapter->escapeField('as alias');
		$this->assertEquals($expected, $actual);

		$expected = '"field" "as"';
		$actual = $queryAdapter->escapeField('field as');
		$this->assertEquals($expected, $actual);

		$expected = '"field" as "as"';
		$actual = $queryAdapter->escapeField('field as as');
		$this->assertEquals($expected, $actual);

		$expected = '"field" AS "as"';
		$actual = $queryAdapter->escapeField('field AS as');
		$this->assertEquals($expected, $actual);

		$expected = '"as" as "alias"';
		$actual = $queryAdapter->escapeField('as as alias');
		$this->assertEquals($expected, $actual);

		$expected = '"as" AS "alias"';
		$actual = $queryAdapter->escapeField('as AS alias');
		$this->assertEquals($expected, $actual);
	}
}
