<?php
/**
 * Help.php
 *
 * PHP version 5.3+
 *
 * Runner
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   XXX
 * @link      http://www.sweelix.net
 * @category  command
 * @package   sweelix.command
 */

namespace sweelix\command;

/**
 * This is a basic Help command
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   XXX
 * @link      http://www.sweelix.net
 * @category  command
 * @package   sweelix.command
 */
class Help {

	/**
	 * run current command, display help and return status code 0
	 *
	 * @return int
	 * @since  XXX
	 */
	public function run() {
		echo 'Command runner Help'."\n";
		return 0;
	}
}