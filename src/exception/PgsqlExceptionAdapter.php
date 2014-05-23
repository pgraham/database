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
namespace zpt\db\exception;

use PDOException;

/**
 * Database exception adapter which parses Pgsql exception messages in order to
 * produce DatabaseException instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PgsqlExceptionAdapter extends BaseExceptionAdapter
	implements DatabaseExceptionAdapter
{

	public function adapt(PDOException $e, $stmt = null, array $params = null) {
		if ($e instanceof DatabaseException) {
			return $e;
		}

		$dbe = new DatabaseException($e, $stmt, $params);

		$code = $e->getCode();
		switch ($code) {
			case '42501':
			$dbe->isAuthorizationError(true);
			break;

			case '42710':
			$dbe->userAlreadyExists(true);
			break;

			case '42P04':
			$dbe->databaseAlreadyExists(true);
			break;

			case '42P01':
			$dbe->tableDoesNotExist(true);
			break;
		}

		return $dbe;
	}

}
