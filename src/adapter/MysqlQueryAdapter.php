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
namespace zpt\db\adapter;

/**
 * {@link QueryAdapter} implementation for Mysql database engine using out of 
 * the box configuration.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MysqlQueryAdapter implements QueryAdapter
{

	public function escapeField($fieldName) {
		return "`$fieldName	`";
	}

}
