<?php
/**
 * @package    Fuel
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2015 - 2016 Fuel Development Team
 * @link       http://fuelphp.org
 */

declare (strict_types=1);

namespace Fuel\Foundation;

use Composer\Autoload\ClassLoader;
use \League\BooBoo\Runner as ErrorHandler;
use \League\BooBoo\Formatter\CommandLineFormatter;

/**
 * Fuel class
 *
 * This is a static class containing methods to initialize the framework, to
 * setup framework application components, and the execute the request.
 * It also contains some global helper methods.
 *
 * @package  Fuel\Foundation
 *
 * @since  1.0.0
 */
class Fuel
{
	/**
	 * @var  string  The global version of framework
	 *
	 * @since  1.0.0
	 */
	const VERSION = '2.0-dev';

	/**
	 * Whether or not the framework is initialized
	 *
	 * @since  1.0.0
	 */
	protected static $initialized = false;

	/**
	 * Whether we're running in CLI mode
	 *
	 * @since  1.0.0
	 */
	protected static $isCli = false;

	/**
	 * Whether we have readline support in CLI mode
	 *
	 * @since  1.0.0
	 */
	protected static $readlineSupport = false;

	/**
	 * Instance of the composer autoloader
	 *
	 * @since  2.0.0
	 */
	protected static $autoloader = null;

	/**
	 * Instance of the global error handler
	 *
	 * @since  2.0.0
	 */
	protected static $errorHandler = null;

	/**
	 * List of forged applications
	 *
	 * @since  2.0.0
	 */
	protected static $applications = [];

	/**
	 * Initialize the framework
	 *
	 * @since  1.0.0
	 *
	 * @param  ClassLoader  $autoloader  instance of the Composer autoloader
	 *
	 * @return void
	 */
	public static function initialize(ClassLoader $autoloader)
	{
		// some handy constants
		if ( ! defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}
		if ( ! defined('CRLF'))
		{
			define('CRLF', chr(13).chr(10));
		}

		// do we have access to mbstring? We need this in order to work with UTF-8 strings
		if ( ! defined('MBSTRING'))
		{
			define('MBSTRING', function_exists('mb_get_info'));
		}

		// store the composer autoloader instance
		static::$autoloader = $autoloader;

		// set the foundation root path so we can find our defaults
		define('ROOTPATH', realpath(pathinfo($autoloader->findFile(__CLASS__), PATHINFO_DIRNAME).DS.'..').DS);

		// determine CLi status and readline support
		if (static::$isCli = (bool) defined('STDIN'))
		{
			static::$readlineSupport = extension_loaded('readline');
		}

		// setup the standard framework error handler
		static::$errorHandler = static::errorHandler();
	}

	/**
	 * Create a new application instance, the main application component
	 * or return an already created one
	 *
	 * @param  string  name to identify this application
	 * @param  string  the namespace of the main application component
	 * @param  array   optionally an array of configuration items
	 * @param  string  optionally the environment this application component has to run in
	 *
	 * @return  Application  the created application object
	 *
	 * @since  2.0.0
	 */
	public static function forge(string $name, string $appNamespace, array $appConfig = [], string $appEnvironment = 'development'): Application
	{
		return new Application;
	}

	/**
	 * Process the application request
	 *
	 * @since  2.0.0
	 *
	 * @return  void
	 */
	public static function run()
	{
		echo '<h1><center>Hello World!</center></h1><br />';
		phpinfo();
	}

	/**
	 * Are we in CLI mode?
	 *
	 * @return  bool
	 *
	 * @since  2.0.0
	 */
	public function isCli()
	{
		return static::$isCli;
	}

	/**
	 * Do we have readline support in CLI mode?
	 *
	 * @return  bool
	 *
	 * @since  2.0.0
	 */
	public function hasReadlineSupport()
	{
		return static::$readlineSupport;
	}

	/**
	 * Return the composer autoloader instance
	 *
	 * @since  2.0.0
	 *
	 * @return  ClassLoader
	 */
	public static function getAutoloader(): ClassLoader
	{
		return static::$autoloader;
	}

	/**
	 * Return the global framework errorhandler
	 *
	 * @since  2.0.0
	 *
	 * @return  ErrorHandler
	 */
	public static function getErrorHandler(): ErrorHandler
	{
		return static::$errorHandler;
	}

	/**
	 * Setup a base error handler so we're not bothered with PHP's errors
	 *
	 * @since  2.0.0
	 *
	 * @return  void
	 */
	protected static function errorHandler(): ErrorHandler
	{
		// setup the error handler
		$runner = new ErrorHandler();

		// determine which formatter to use
		if (static::$isCli)
		{
			$formatter = new CommandLineFormatter();
		}
		else
		{
			// TODO: [HV] need custom Fuel handler to handle BooBoo error messages
			$formatter = new \League\BooBoo\Formatter\HtmlTableFormatter();
		}
		$formatter->setErrorLimit(E_ALL);
		$runner->pushFormatter($formatter);

		// we want to capture it all!
		$runner->treatErrorsAsExceptions(true);

		// activate the handler
		$runner->register();

		return $runner;
	}

}
