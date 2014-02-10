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
	/**
	 * @var array initialised runners
	 */
	private static $_runner;

	/**
	 * @var array command line arguments
	 */
	private static $_args;

	/**
	 * @var array arguments passed to the command method
	 */
	private $_commandArgs=array();

	/**
	 * @var string command name
	 */
	private $_command;

	/**
	 * @var string sub command name (run will be used if not defined)
	 */
	private $_subcommand;

	/**
	 * Get configured singleton
	 *
	 * @param array $config singleton configuration
	 *
	 * @return Runner
	 * @since  XXX
	 */
	public static function getRunner($config=array()) {
		$args = static::parseArguments();
		$config['help'] = 'sweelix\\command\\Help';
		if(isset($args['command']) === true) {
			$config['command'] = $args['command'];
			unset($args['command']);
		} else {
			$config['command'] = false;
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

	/**
	 * Create a command runner. Should only be called through
	 * self::getRunner()
	 *
	 * @param array $config configuration parameters
	 * @param array $args   command line arguments
	 *
	 * @return void
	 * @since  XXX
	 */
	public function __construct($config=array(), $args=array()) {
		if($config['command'] === false) {
			$class = $config['help'];
		} else {
			if((isset($config['namespace']) === true) && (empty($config['namespace']) === false)) {
				$class = $config['namespace'].'\\'.ucfirst($config['command']);
			} else {
				$class = ucfirst($config['command']);
			}
		}
		$reflectionClass = new \ReflectionClass($class);
		if($reflectionClass->hasMethod($config['subcommand']) === false) {
			throw new CommandException($class.' class must implement method '.$config['subcommand'].'()');
		}
		$this->_command = new $class;
		$this->_subcommand = $config['subcommand'];
		$parameters = $reflectionClass->getMethod($this->_subcommand)->getParameters();
		$realParameters = array();
		$errors = null;
		foreach($parameters as $i => $parameter) {
			if(($parameter->isOptional() === false) && (isset($args[$parameter->name]) === false)) {
				$errors[] = $parameter->name;
			} elseif(isset($args[$parameter->name]) === true) {
				$this->_commandArgs[$i] = $args[$parameter->name];
			}
		}
		if($errors !== null) {
			$error = 'Parameters '.implode($errors);
			if(count($errors) > 1) {
				$error .= ' are';
			} else {
				$error .= ' is';
			}
			$error .= ' mandatory';
			throw new CommandException($error);
		}
	}

	/**
	 * Run the requested command
	 * If runner method returns an integer app exits with integer code
	 * else the result is returned
	 *
	 * @return mixed
	 * @since  XXX
	 */
	public function run() {
		$status = call_user_func_array(array($this->_command, $this->_subcommand), $this->_commandArgs);
		if(($status !== null) && (is_int($status) === true)) {
			exit($status);
		}
		return $status;
	}

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