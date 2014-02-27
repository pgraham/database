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
 * Interface for engine specific adapter for building SQL DML queries.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
interface QueryAdapter
{

	/**
	 * Apply field escaping to the given field.
	 *
	 * @param string $field
	 * @return string
	 */
	public function escapeField($fieldname);
}
