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

// Search for composer vendor directory and include autoloader.
$dir =dirname(__DIR__);

while(!file_exists($dir . DIRECTORY_SEPARATOR . 'vendor')) {
	$dir = dirname($dir);
}
$composerAutoloaderPath = implode(DIRECTORY_SEPARATOR, [
	$dir,
	'vendor',
	'autoload.php'
]);
require_once $composerAutoloaderPath;
