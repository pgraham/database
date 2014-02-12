<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
 * All rights reserved.
 *
 * This file is part of Reed and is licensed by the Copyright holder under
 * the 3-clause BSD License.  The full text of the license can be found in the
 * LICENSE.txt file included in the root directory of this distribution or at
 * the link below.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 * =============================================================================
 */
namespace zpt\util\db;

use \zpt\util\DB;

/**
 * This class parses a database permissions bitmask into a comma separated
 * string of permissions.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PermissionParser {

	public function parse($permissions) {
		$perms = [];
		if ($permissions & DB::PERMISSION_SELECT) {
			$perms[] = 'SELECT';
		}

		if ($permissions & DB::PERMISSION_INSERT) {
			$perms[] = 'INSERT';
		}

		if ($permissions & DB::PERMISSION_UPDATE) {
			$perms[] = 'UPDATE';
		}

		if ($permissions & DB::PERMISSION_DELETE) {
			$perms[] = 'DELETE';
		}

		return implode(',', $perms);
	}
}
