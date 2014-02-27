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
 * {@link QueryAdapter} for ANSI compliant database engines. Not all methods
 * defined by the QueryAdapter interface have a defined behaviour in the
 * standard and so this implementation is incomplete and is thus defined as
 * abstract.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
abstract class AnsiQueryAdapter implements QueryAdapter
{

	public function escapeField($fieldName) {
		return "\"$fieldName\"";
	}

}
