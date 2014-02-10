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
	 * @var string where to search commands
	 */
	private $_searchPath;

	/**
	 * @var string commands namespace
	 */
	private $_namespace;

	/**
	 * @var array available commands
	 */
	private $_cmdList;

	/**
	 * run current command, display help and return status code 0
	 *
	 * @return int
	 * @since  XXX
	 */
	public function run($searchPath, $namespace) {
		$this->_searchPath = $searchPath;
		$this->_namespace = $namespace;
		echo 'Sweelix command runner'."\n";
		echo 'Usage: '.$_SERVER['argv'][0].' <command-name> <subcommand-name> [parameters ...]'."\n";
		echo "\n";
		if(count($this->getCommandList()) > 0) {
			echo 'The following commands are available:'."\n";
			echo implode("\n", $this->getCommandList());
		}
		echo "\n";
		return 0;
	}

	/**
	 * Try to find available commands
	 *
	 * @return array
	 * @since  XXX
	 */
	protected function getCommandList() {
		if($this->_cmdList === null) {
			$cmdList = array();
			$commands = array();
			if(is_dir($this->_searchPath) === true) {
				$files = scandir($this->_searchPath);
				foreach($files as $file) {
					$pinfo = pathinfo($file);
					if($pinfo['extension'] === 'php') {
						if(empty($this->_namespace) === false) {
							$commands[] = $this->_namespace.'\\'.$pinfo['filename'];
						} else {
							$commands[] = $pinfo['filename'];
						}

					}
				}
			}
			foreach($commands as $command) {
				$cliCommand = ' - '.lcfirst($pinfo['filename']);
				$reflection = new \ReflectionClass($command);
				$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
				foreach($methods as $method) {
					$method instanceof \ReflectionMethod;
					if($method->name == 'run') {
						$cli = $cliCommand;
					} else {
						$cli = $cliCommand.' '.$method->name;
					}

					$parameters = $method->getParameters();
					foreach($parameters as $parameter) {
						$parameter instanceof \ReflectionParameter;
						$param = '--'.$parameter->name;
						if($parameter->isDefaultValueAvailable() === true) {
							$param .= '='.$parameter->getDefaultValue();
						}
						if($parameter->isOptional() === true) {
							$param = '['.$param.']';
						}
						$cli .= ' '.$param;
					}
					$cmdList[] = $cli;
				}
			}
			$this->_cmdList = $cmdList;
		}
		return $this->_cmdList;
	}
}