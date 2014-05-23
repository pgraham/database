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

use \PDOException;

/**
 * Database exception adapter which parses Mysql exception messages in order to
 * produce DatabaseException instances.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class MysqlExceptionAdapter extends BaseExceptionAdapter
	implements DatabaseExceptionAdapter
{

	public function adapt(PDOException $e, $stmt = null, array $params = null) {
		$dbe = parent::adapt($e, $stmt, $params);

		$mysqlErr = $this->getMysqlErrorCode($e->getMessage());
		switch ($mysqlErr) {
			case '1044':
			$dbe->isAuthorizationError(true);
			break;
		}

		return $dbe;
	}

	private function getMysqlErrorCode($msg) {
		$matches = [];
		preg_match(
			'/^SQLSTATE\[[A-Z0-9]{5}]:.+:\s*(\d+).+$/',
			$msg,
			$matches
		);

		if (isset($matches[1])) {
			return $matches[1];
		} else {
			return '';
		}
	}
}
