<?php
/**
 * CommandInterface.php
 *
 * PHP version 5.3+
 *
 * Interface
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
 * This interface must be implemented by all command the
 * user will be running
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   XXX
 * @link      http://www.sweelix.net
 * @category  command
 * @package   sweelix.command
 */
interface CommandInterface {
	/**
	 * Method which will be run.
	 * Return the exit code which will be used
	 *
	 * @return integer
	 * @since  XXX
	 */
	public function run();
}
