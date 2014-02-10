<?php
/**
 * Runner.php
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
class Runner {
	private static $_runner;
	public static function getRunner($config=array()) {
		$args = static::parseArguments();
		if(isset($args['command']) === true) {
			$config['command'] = $args['command'];
			unset($args['command']);
		} else {
			$config['command'] = 'help';
		}
		if(isset($config['namespace']) === false) {
			$config['namespace'] = 'sweelix\\command';
		}
		if(isset($args['subcommand']) === true) {
			$config['subcommand'] = $args['subcommand'];
			unset($args['subcommand']);
		} else {
			$config['subcommand'] = 'run';
		}
		$runner = $config['command'].'-'.$config['subcommand'];
		if(isset(static::$_runner[$runner]) === false) {
			static::$_runner[$runner] = new static($config, $args['args']);
		}
		return static::$_runner[$runner];
	}

	private $_config;
	private $_commandArgs=array();
	private $_command;
	private $_subcommand;
	public function __construct($config=array(), $args=array()) {
		if((isset($config['namespace']) === true) && (empty($config['namespace']) === false)) {
			$class = $config['namespace'].'\\'.ucfirst($config['command']);
		} else {
			$class = ucfirst($config['command']);
		}
		$this->_command = new $class;
		$this->_subcommand = $config['subcommand'];
		$method = new \ReflectionMethod($this->_command, $this->_subcommand);
		$parameters = $method->getParameters();
		$realParameters = array();
		foreach($parameters as $i => $parameter) {
			if(isset($args[$parameter->name]) === true) {
				$this->_commandArgs[$i] = $args[$parameter->name];
			}
		}
	}

	public function run() {
		return call_user_func_array(array($this->_command, $this->_subcommand), $this->_commandArgs);
	}

	private static $_args;
	/**
	 * Parse command line arguments and return an array
	 * array( 'command' => 'xxx', 'arguments' => array()) which
	 * should be used to find and run appropriate command
	 *
	 * @return array
	 * @since  XXX
	 */
	protected static function parseArguments() {
		if(self::$_args === null) {
			if(isset($_SERVER['argv']) === false) {
				die('This script must be run from the CLI');
			}
			$arguments = array(	'args' => null, 'command' => null);
			foreach($_SERVER['argv'] as $i => $argument) {
				if($i > 0) {
					if(preg_match('/^--([^=]+)(=(.*))?$/', $argument, $matches) === 1) {
						if(count($matches) === 2) {
							$arguments['args'][$matches[1]] = true;
						} else {
							$arguments['args'][$matches[1]] = $matches[3];
						}
					} elseif(preg_match('/^([a-z0-9]+)$/i', $argument, $matches) === 1) {
						if(isset($arguments['command']) === false) {
							$arguments['command'] = $matches[1];
						} else {
							$arguments['subcommand'] = $matches[1];
						}
					}
				}
			}
			self::$_args = $arguments;
		}
		return self::$_args;
	}

}