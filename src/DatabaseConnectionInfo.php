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
namespace zpt\db;

use \zpt\util\Map;
use \InvalidArgumentException;
use \PDO;

/**
 * This class encapsulates the info needed to establish a database connection.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class DatabaseConnectionInfo
{

	private static $DEFAULT_OPTS = [
		'host'          => 'localhost',
		'schema'        => '',
		'username'      => '',
		'password'      => '',
		'dsnOptions'    => [],
		'pdoOptions'    => [],
		'pdoAttributes' => []
	];

	private $driver;
	private $user;
	private $pw;
	private $host;
	private $schema;
	private $dsnOpts;
	private $pdoOpts;
	private $pdoAttrs;

	/**
	 * Create a new PdoConnectionInfo object with the given options. Supported
	 * options are:
	 *
	 *  - driver: *Required* The database engine to which the connection will be
	 *    established. Supported drivers are `mysql` and `pgsql`.
	 *  - username: *Required* The username for the database connection.
	 *  - password: *Required* The password for the database connection.
	 *  - host: The host on which the database engine is running.
	 *    Default: `localhost`.
	 *  - schema: The name of the database to connect to.
	 *  - dsnOptions: Additional options for the DSN.
	 *  - pdoOptions: Additional options for the PDO constructor.
	 *  - pdoAttributes: Attributes to set on the PDO connection.
	 */
	public function __construct(array $opts) {
		$opts = $this->applyDefaultOptions($opts);
		if (!isset($opts['driver'])) {
			$msg = "Connection requires `driver` to be specified";
			throw new InvalidArgumentException($msg);
		}

		$this->driver = $opts['driver'];
		$this->host = $opts['host'];
		$this->user = $opts['username'];
		$this->pw = $opts['password'];
		$this->schema = $opts['schema'];
		$this->dsnOpts = $opts['dsnOptions'];
		$this->pdoOpts = $opts['pdoOptions'];
		$this->pdoAttrs = $opts['pdoAttributes'];

		// If not explicitely set, use exception for error reporting.
		if (!isset($this->pdoAttrs[PDO::ATTR_ERRMODE])) {
			$this->pdoAttrs[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		}
	}

	/**
	 * Generate a DSN using the info encapsulated in the instance.
	 *
	 * @return string Database connection DSN
	 */
	public function getDsn() {
		// Handle sqlite in memory database
		if ($this->driver === 'sqlite' && $this->schema === ':memory:') {
			return 'sqlite::memory:';
		}
		
		$opts = [];
		$opts['host'] = $this->host;

		if ($this->schema) {
			$opts['dbname'] = $this->schema;
		}

		$dsnOpts = array_merge([], $this->dsnOpts, $opts);
		return "$this->driver:" . implode(';', Map::zip('=', $dsnOpts));
	}

	public function getDriver() {
		return $this->driver;
	}

	public function getUsername() {
		return $this->user;
	}

	public function getPassword() {
		return $this->pw;
	}

	public function getHost() {
		return $this->host;
	}

	public function getSchema() {
		return $this->schema;
	}

	public function getDsnOptions() {
		return $this->dsnOpts;
	}

	public function getPdoOptions() {
		return $this->pdoOpts;
	}

	public function getPdoAttributes() {
		return $this->pdoAttrs;
	}

	public function setDriver($driver) {
		$this->driver = $driver;
	}

	public function setUsername($username) {
		$this->user = $username;
	}

	public function setPassword($password) {
		$this->pw = $password;
	}

	public function sethost($host) {
		$this->host = $host;
	}

	public function setSchema($schema) {
		$this->schema = $schema;
	}

	public function setDsnOptions(array $dsnOptions) {
		$this->dsnOpts = $dsnOptions;
	}

	public function setPdoOptions(array $pdoOptions) {
		$this->pdoOpts = $pdoOptions;
	}

	public function setPdoAttributes(array $pdoAttributes) {
		$this->pdoAttrs = $pdoAttributes;
	}

	private function applyDefaultOptions($options) {
		return array_merge([], self::$DEFAULT_OPTS, $options);
	}
}
