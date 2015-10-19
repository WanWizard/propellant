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

use InvalidArgumentException;
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
	const VERSION = '2.0.0-dev';

	/**
	 * @var  string  Constant used for when in testing mode
	 *
	 * @since  1.0.0
	 */
	const TEST = 'test';

	/**
	 * @var  string  Constant used for when in development
	 *
	 * @since  1.0.0
	 */
	const DEVELOPMENT = 'development';

	/**
	 * @var  string  Constant used for when in production
	 *
	 * @since  1.0.0
	 */
	const PRODUCTION = 'production';

	/**
	 * @var  string  Constant used for when testing the app in a staging env.
	 *
	 * @since  1.0.0
	 */
	const STAGING = 'staging';

	/**
	 * @var  bool  Whether or not the framework is initialized
	 *
	 * @since  1.0.0
	 */
	protected static $initialized = false;

	/**
	 * @var  bool  Whether we're running in CLI mode
	 *
	 * @since  1.0.0
	 */
	protected static $isCli = false;

	/**
	 * @var  bool  Whether we have readline support in CLI mode
	 *
	 * @since  1.0.0
	 */
	protected static $readlineSupport = false;

	/**
	 * @var  Autoloader  Instance of the composer autoloader
	 *
	 * @since  2.0.0
	 */
	protected static $autoloader = null;

	/**
	 * @var  ErrorHandler  Instance of the global error handler
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
	 * @param  string  the namespace of the main application component
	 * @param  array   optionally an array of configuration items
	 * @param  string  optionally the environment this application component has to run in
	 *
	 * @return  Application  the created application object
	 *
	 * @since  2.0.0
	 */
	public static function forge(string $appNamespace, string $appEnvironment = null): Application
	{
		// determine the application class to load
		$class = $appNamespace.'\\Application';

		// check if this namespace exists and defines an application class
		if ( ! class_exists($class))
		{
			// TODO: localize
			throw new InvalidArgumentException(sprintf('Can not forge this application, namespace "%s" does not define an Application class', $appNamespace));
		}

		// instantiate
		$class = new $class;

		// check if this class is a Fuel application class
		if ( ! $class instanceOf \Fuel\Foundation\Application)
		{
			// TODO: localize
			throw new InvalidArgumentException(sprintf('Can not forge this application, "%s" is not a Fuel Application class', $class));
		}

		// store the instance
		static::$applications[$class->getName()] = $class;

		// and return it
		return $class;
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
	 * Return the global framework environment
	 *
	 * @since  2.0.0
	 *
	 * @return  string
	 */
	public static function getEnvironment(): string
	{
		return (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : static::DEVELOPMENT);
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
